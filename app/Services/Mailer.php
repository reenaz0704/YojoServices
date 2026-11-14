<?php
namespace App\Services;

use App\Core\Logger;

/**
 * Dependency-free SMTP client (AUTH LOGIN, SSL or STARTTLS).
 * Falls back to logging the message when SMTP is disabled.
 */
class Mailer
{
    private $socket;
    private $log = [];

    public function send(string $to, string $subject, string $html, ?string $replyTo = null): bool
    {
        $cfg = config('mail');
        if (!$cfg['enabled']) {
            Logger::write('mail', 'info', "TO: $to | SUBJECT: $subject\n" . strip_tags(preg_replace('/<br\s*\/?>|<\/p>|<\/tr>/i', "\n", $html)) . "\n----");
            return true;
        }
        try {
            $this->deliver($cfg, $to, $subject, $html, $replyTo);
            return true;
        } catch (\Throwable $e) {
            Logger::write('mail', 'error', "Failed to send to $to: " . $e->getMessage() . ' | ' . implode(' / ', $this->log));
            return false;
        } finally {
            if (is_resource($this->socket)) {
                fclose($this->socket);
            }
        }
    }

    private function deliver(array $cfg, string $to, string $subject, string $html, ?string $replyTo): void
    {
        $host = ($cfg['encryption'] === 'ssl' ? 'ssl://' : '') . $cfg['host'];
        $this->socket = @stream_socket_client($host . ':' . $cfg['port'], $errno, $errstr, $cfg['timeout']);
        if (!$this->socket) {
            throw new \RuntimeException("Connection failed: $errstr ($errno)");
        }
        stream_set_timeout($this->socket, $cfg['timeout']);
        $this->expect(220);
        $domain = parse_url(full_url('/'), PHP_URL_HOST) ?: 'localhost';
        $this->cmd("EHLO $domain", 250);
        if ($cfg['encryption'] === 'tls') {
            $this->cmd('STARTTLS', 220);
            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                throw new \RuntimeException('STARTTLS negotiation failed');
            }
            $this->cmd("EHLO $domain", 250);
        }
        if ($cfg['username'] !== '') {
            $this->cmd('AUTH LOGIN', 334);
            $this->cmd(base64_encode($cfg['username']), 334);
            $this->cmd(base64_encode($cfg['password']), 235);
        }
        $this->cmd('MAIL FROM:<' . $cfg['from_email'] . '>', 250);
        $this->cmd('RCPT TO:<' . $to . '>', [250, 251]);
        $this->cmd('DATA', 354);

        $boundary = 'b' . bin2hex(random_bytes(8));
        $headers = [
            'Date: ' . date('r'),
            'From: ' . $this->encode($cfg['from_name']) . ' <' . $cfg['from_email'] . '>',
            'To: <' . $to . '>',
            'Subject: ' . $this->encode($subject),
            'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $domain . '>',
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];
        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = 'Reply-To: <' . $replyTo . '>';
        }
        $text = trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>|<\/p>|<\/tr>|<\/h\d>/i', "\n", $html))));
        $body = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($text))
            . "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($html))
            . "--$boundary--";
        $data = implode("\r\n", $headers) . "\r\n\r\n" . $body;
        $data = preg_replace('/^\./m', '..', $data); // dot-stuffing
        $this->cmd($data . "\r\n.", 250);
        $this->cmd('QUIT', 221);
    }

    private function encode(string $s): string
    {
        return '=?UTF-8?B?' . base64_encode($s) . '?=';
    }

    private function cmd(string $line, $expected): void
    {
        fwrite($this->socket, $line . "\r\n");
        $this->expect($expected);
    }

    private function expect($codes): void
    {
        $codes = (array) $codes;
        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        $this->log[] = trim(substr($response, 0, 80));
        if (!in_array((int) substr($response, 0, 3), $codes, true)) {
            throw new \RuntimeException('Unexpected SMTP response: ' . trim($response));
        }
    }

    /** Render an email template from resources/views/emails. */
    public static function template(string $name, array $data): string
    {
        return \App\Core\View::render('emails/' . $name, $data, 'emails/layout');
    }
}

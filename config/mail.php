<?php
/**
 * SMTP configuration. When 'enabled' is false, emails are written to
 * storage/logs/mail-YYYY-MM.log instead of being sent (useful for local dev).
 * Production: set real credentials in config/mail.local.php.
 */
return [
    'enabled'    => getenv('MAIL_ENABLED') === '1',
    'host'       => getenv('MAIL_HOST') ?: 'smtp.hostinger.com',
    'port'       => (int) (getenv('MAIL_PORT') ?: 465),
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'ssl',   // ssl | tls | none
    'username'   => getenv('MAIL_USERNAME') ?: '',
    'password'   => getenv('MAIL_PASSWORD') ?: '',
    'from_email' => getenv('MAIL_FROM') ?: 'info@yojosolutions.com',
    'from_name'  => 'Yojo Solutions',
    // Internal recipient for enquiry notifications (fallback if the admin setting is empty).
    'notify_to'  => getenv('MAIL_NOTIFY') ?: 'info@yojosolutions.com',
    'timeout'    => 15,
];

<?php
namespace App\Core;

/**
 * Server-side validation. Rules: required, email, max:N, min:N, phone, in:a,b,c, date, time, accepted, array.
 */
class Validator
{
    private $errors = [];
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules, array $labels = []): self
    {
        $v = new self($data);
        foreach ($rules as $field => $ruleString) {
            $v->apply($field, explode('|', $ruleString), $labels[$field] ?? ucwords(str_replace('_', ' ', $field)));
        }
        return $v;
    }

    private function apply(string $field, array $rules, string $label): void
    {
        $value = $this->data[$field] ?? null;
        $isEmpty = $value === null || $value === '' || (is_array($value) && count($value) === 0);
        foreach ($rules as $rule) {
            $parts = explode(':', $rule, 2);
            $name = $parts[0];
            $arg = $parts[1] ?? null;
            if ($name !== 'required' && $name !== 'accepted' && $isEmpty) {
                continue;
            }
            $error = null;
            switch ($name) {
                case 'required':
                    if ($isEmpty) { $error = "$label is required."; }
                    break;
                case 'accepted':
                    if (!in_array($value, [1, '1', true, 'true', 'on', 'yes'], true)) { $error = "Please accept the $label."; }
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) { $error = "$label must be a valid email address."; }
                    break;
                case 'max':
                    if (is_string($value) && mb_strlen($value) > (int) $arg) { $error = "$label may not exceed $arg characters."; }
                    break;
                case 'min':
                    if (is_string($value) && mb_strlen(trim($value)) < (int) $arg) { $error = "$label must be at least $arg characters."; }
                    break;
                case 'phone':
                    if (!preg_match('/^\+?[0-9\s\-()]{7,20}$/', (string) $value)) { $error = "$label must be a valid phone number."; }
                    break;
                case 'in':
                    if (!in_array((string) $value, explode(',', (string) $arg), true)) { $error = "$label has an invalid value."; }
                    break;
                case 'date':
                    $d = \DateTime::createFromFormat('Y-m-d', (string) $value);
                    if (!$d || $d->format('Y-m-d') !== $value) { $error = "$label must be a valid date."; }
                    break;
                case 'future':
                    if (strtotime((string) $value) < strtotime('today')) { $error = "$label must be today or later."; }
                    break;
                case 'time':
                    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) $value)) { $error = "$label must be a valid time."; }
                    break;
                case 'array':
                    if (!is_array($value)) { $error = "$label is invalid."; }
                    break;
            }
            if ($error) {
                $this->errors[$field] = $error;
                return;
            }
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

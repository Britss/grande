<?php
declare(strict_types=1);

namespace App\Support;

final class Validator
{
    private array $errors = [];

    public function __construct(private readonly array $data)
    {
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $label): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value === '') {
            $this->addError($field, sprintf('%s is required.', $label));
        }

        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($field, sprintf('%s must be a valid email address.', $label));
        }

        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && mb_strlen($value) < $length) {
            $this->addError($field, sprintf('%s must be at least %d characters.', $label, $length));
        }

        return $this;
    }

    public function max(string $field, int $length, string $label): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && mb_strlen($value) > $length) {
            $this->addError($field, sprintf('%s must not exceed %d characters.', $label, $length));
        }

        return $this;
    }

    public function regex(string $field, string $pattern, string $message): self
    {
        $value = trim((string) ($this->data[$field] ?? ''));

        if ($value !== '' && preg_match($pattern, $value) !== 1) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function accepted(string $field, string $label): self
    {
        $value = $this->data[$field] ?? null;
        $acceptedValues = ['1', 1, true, 'true', 'on', 'yes'];

        if (!in_array($value, $acceptedValues, true)) {
            $this->addError($field, sprintf('You must accept the %s.', strtolower($label)));
        }

        return $this;
    }

    public function same(string $field, string $otherField, string $label, string $otherLabel): self
    {
        $value = (string) ($this->data[$field] ?? '');
        $otherValue = (string) ($this->data[$otherField] ?? '');

        if ($value !== '' && $otherValue !== '' && $value !== $otherValue) {
            $this->addError($field, sprintf('%s must match %s.', $label, strtolower($otherLabel)));
        }

        return $this;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= [];
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

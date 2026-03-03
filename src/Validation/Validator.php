<?php

namespace LunoxHoshizaki\Validation;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public static function make(array $data, array $rules): static
    {
        return new static($data, $rules);
    }

    public function fails(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                // Parse rule with parameters e.g., min:8
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramString] = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                    $rule = $ruleName;
                }

                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    if (!$this->$method($field, $value, $params)) {
                        // Stop checking other rules for this field if it failed
                        break;
                    }
                }
            }
        }

        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    // --- Validation Rules ---

    protected function validateRequired(string $field, $value, array $params): bool
    {
        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            $this->addError($field, "The {$field} field is required.");
            return false;
        }
        return true;
    }

    protected function validateEmail(string $field, $value, array $params): bool
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "The {$field} must be a valid email address.");
            return false;
        }
        return true;
    }

    protected function validateMin(string $field, $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);
        if ($value && strlen((string)$value) < $min) {
            $this->addError($field, "The {$field} must be at least {$min} characters.");
            return false;
        }
        return true;
    }

    protected function validateMax(string $field, $value, array $params): bool
    {
        $max = (int) ($params[0] ?? 0);
        if ($value && strlen((string)$value) > $max) {
            $this->addError($field, "The {$field} may not be greater than {$max} characters.");
            return false;
        }
        return true;
    }

    protected function validateConfirmed(string $field, $value, array $params): bool
    {
        $confirmationField = $field . '_confirmation';
        $confirmationValue = $this->data[$confirmationField] ?? null;

        if ($value !== $confirmationValue) {
            $this->addError($field, "The {$field} confirmation does not match.");
            return false;
        }
        return true;
    }
}

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
                // Handle 'nullable' — skip all remaining rules if value is null/empty
                if ($rule === 'nullable') {
                    if (is_null($value) || (is_string($value) && trim($value) === '')) {
                        break; // Skip remaining rules for this field
                    }
                    continue;
                }

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

    protected function validateNumeric(string $field, $value, array $params): bool
    {
        if ($value && !is_numeric($value)) {
            $this->addError($field, "The {$field} must be a number.");
            return false;
        }
        return true;
    }

    protected function validateInteger(string $field, $value, array $params): bool
    {
        if ($value && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "The {$field} must be an integer.");
            return false;
        }
        return true;
    }

    protected function validateIn(string $field, $value, array $params): bool
    {
        if ($value && !in_array($value, $params)) {
            $allowed = implode(', ', $params);
            $this->addError($field, "The {$field} must be one of: {$allowed}.");
            return false;
        }
        return true;
    }

    protected function validateNotIn(string $field, $value, array $params): bool
    {
        if ($value && in_array($value, $params)) {
            $this->addError($field, "The {$field} has an invalid value.");
            return false;
        }
        return true;
    }

    protected function validateUrl(string $field, $value, array $params): bool
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "The {$field} must be a valid URL.");
            return false;
        }
        return true;
    }

    protected function validateDate(string $field, $value, array $params): bool
    {
        if ($value && strtotime($value) === false) {
            $this->addError($field, "The {$field} is not a valid date.");
            return false;
        }
        return true;
    }

    protected function validateAlpha(string $field, $value, array $params): bool
    {
        if ($value && !ctype_alpha($value)) {
            $this->addError($field, "The {$field} may only contain letters.");
            return false;
        }
        return true;
    }

    protected function validateAlpha_num(string $field, $value, array $params): bool
    {
        if ($value && !ctype_alnum($value)) {
            $this->addError($field, "The {$field} may only contain letters and numbers.");
            return false;
        }
        return true;
    }

    protected function validateRegex(string $field, $value, array $params): bool
    {
        $pattern = $params[0] ?? '';
        if ($value && !preg_match($pattern, $value)) {
            $this->addError($field, "The {$field} format is invalid.");
            return false;
        }
        return true;
    }

    protected function validateUnique(string $field, $value, array $params): bool
    {
        if (!$value) {
            return true;
        }

        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;
        $exceptId = $params[2] ?? null;

        if (empty($table)) {
            return true;
        }

        try {
            $pdo = \LunoxHoshizaki\Database\Model::getConnection();
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
            $bindings = [$value];

            if ($exceptId) {
                $sql .= " AND id != ?";
                $bindings[] = $exceptId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);
            $count = (int) $stmt->fetchColumn();

            if ($count > 0) {
                $this->addError($field, "The {$field} has already been taken.");
                return false;
            }
        } catch (\Exception $e) {
            // If DB check fails, let it pass (fail open for non-critical validation)
            return true;
        }

        return true;
    }

    protected function validateExists(string $field, $value, array $params): bool
    {
        if (!$value) {
            return true;
        }

        $table = $params[0] ?? '';
        $column = $params[1] ?? $field;

        if (empty($table)) {
            return true;
        }

        try {
            $pdo = \LunoxHoshizaki\Database\Model::getConnection();
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$value]);
            $count = (int) $stmt->fetchColumn();

            if ($count === 0) {
                $this->addError($field, "The selected {$field} is invalid.");
                return false;
            }
        } catch (\Exception $e) {
            return true;
        }

        return true;
    }

    protected function validateBetween(string $field, $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);
        $max = (int) ($params[1] ?? 0);
        $length = strlen((string) $value);

        if ($value && ($length < $min || $length > $max)) {
            $this->addError($field, "The {$field} must be between {$min} and {$max} characters.");
            return false;
        }
        return true;
    }

    protected function validateSize(string $field, $value, array $params): bool
    {
        $size = (int) ($params[0] ?? 0);
        if ($value && strlen((string) $value) !== $size) {
            $this->addError($field, "The {$field} must be exactly {$size} characters.");
            return false;
        }
        return true;
    }

    protected function validateBoolean(string $field, $value, array $params): bool
    {
        $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false'];
        if (!is_null($value) && !in_array($value, $acceptable, true)) {
            $this->addError($field, "The {$field} must be true or false.");
            return false;
        }
        return true;
    }

    protected function validateArray(string $field, $value, array $params): bool
    {
        if ($value && !is_array($value)) {
            $this->addError($field, "The {$field} must be an array.");
            return false;
        }
        return true;
    }

    protected function validateString(string $field, $value, array $params): bool
    {
        if ($value && !is_string($value)) {
            $this->addError($field, "The {$field} must be a string.");
            return false;
        }
        return true;
    }
}

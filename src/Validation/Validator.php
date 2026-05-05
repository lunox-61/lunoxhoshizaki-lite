<?php

namespace LunoxHoshizaki\Validation;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    /**
     * Custom error messages provided by the developer.
     * Format: ['field.rule' => 'message'] or ['rule' => 'message']
     */
    protected array $customMessages = [];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
    }

    /**
     * Create a new Validator instance.
     *
     * @param array $data     Input data to validate
     * @param array $rules    Validation rules
     * @param array $messages Custom error messages (optional)
     *
     * Usage:
     *   Validator::make($data, [
     *       'name' => 'required|min:3',
     *       'email' => 'required|email|unique:users,email',
     *       'items.*.name' => 'required|string',
     *   ], [
     *       'name.required' => 'Nama harus diisi.',
     *       'email.email'   => 'Format email tidak valid.',
     *       'required'      => 'Field :field wajib diisi.',
     *   ]);
     */
    public static function make(array $data, array $rules, array $messages = []): static
    {
        return new static($data, $rules, $messages);
    }

    public function fails(): bool
    {
        foreach ($this->rules as $field => $ruleString) {
            // Handle nested wildcard rules: 'items.*.name'
            if (str_contains($field, '*')) {
                $this->validateWildcardField($field, $ruleString);
                continue;
            }

            $rules = explode('|', $ruleString);
            $value = $this->getNestedValue($field);

            foreach ($rules as $rule) {
                // Handle 'nullable' — skip all remaining rules if value is null/empty
                if ($rule === 'nullable') {
                    if (is_null($value) || (is_string($value) && trim($value) === '')) {
                        break; // Skip remaining rules for this field
                    }
                    continue;
                }

                // Handle 'sometimes' — skip if the field is not present at all
                if ($rule === 'sometimes') {
                    if (!$this->fieldExists($field)) {
                        break;
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

                // Normalize method name: 'alpha_num' → 'validateAlphaNum'
                $method = 'validate' . str_replace('_', '', ucwords($rule, '_'));
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

    // -------------------------------------------------------------------------
    // Nested / Wildcard Support
    // -------------------------------------------------------------------------

    /**
     * Get a value from nested data using dot notation.
     *
     * 'user.name' reads $data['user']['name']
     */
    protected function getNestedValue(string $field, ?array $data = null): mixed
    {
        $data = $data ?? $this->data;
        $keys = explode('.', $field);

        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Check if a field exists in the data (including nested fields).
     */
    protected function fieldExists(string $field): bool
    {
        $keys = explode('.', $field);
        $data = $this->data;

        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate wildcard fields like 'items.*.name'.
     *
     * Expands the wildcard into concrete paths and validates each one.
     */
    protected function validateWildcardField(string $pattern, string $ruleString): void
    {
        $fields = $this->expandWildcard($pattern, $this->data);

        foreach ($fields as $concreteField) {
            $rules = explode('|', $ruleString);
            $value = $this->getNestedValue($concreteField);

            foreach ($rules as $rule) {
                if ($rule === 'nullable') {
                    if (is_null($value) || (is_string($value) && trim($value) === '')) {
                        break;
                    }
                    continue;
                }

                if ($rule === 'sometimes') {
                    if (!$this->fieldExists($concreteField)) {
                        break;
                    }
                    continue;
                }

                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramString] = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                    $rule = $ruleName;
                }

                $method = 'validate' . str_replace('_', '', ucwords($rule, '_'));
                if (method_exists($this, $method)) {
                    if (!$this->$method($concreteField, $value, $params)) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Expand a wildcard pattern into an array of concrete field paths.
     *
     * 'items.*.name' with data ['items' => [['name'=>'A'], ['name'=>'B']]]
     * returns ['items.0.name', 'items.1.name']
     */
    protected function expandWildcard(string $pattern, array $data, string $prefix = ''): array
    {
        $parts = explode('.', $pattern);
        $results = [];

        if (empty($parts)) {
            return [$prefix];
        }

        $firstPart = array_shift($parts);
        $remaining = implode('.', $parts);
        $currentPrefix = $prefix ? $prefix . '.' . $firstPart : $firstPart;

        if ($firstPart === '*') {
            // Expand the wildcard — the current level should be an array
            $currentData = $prefix ? $this->getNestedValue($prefix) : $data;
            if (is_array($currentData)) {
                foreach (array_keys($currentData) as $key) {
                    $newPrefix = $prefix ? $prefix . '.' . $key : (string) $key;
                    if (empty($remaining)) {
                        $results[] = $newPrefix;
                    } else {
                        $results = array_merge(
                            $results,
                            $this->expandWildcard($remaining, $data, $newPrefix)
                        );
                    }
                }
            }
        } else {
            if (empty($remaining)) {
                $results[] = $currentPrefix;
            } else {
                $results = array_merge(
                    $results,
                    $this->expandWildcard($remaining, $data, $currentPrefix)
                );
            }
        }

        return $results;
    }

    // -------------------------------------------------------------------------
    // Error Handling with Custom Messages
    // -------------------------------------------------------------------------

    /**
     * Add a validation error with custom message support.
     *
     * Resolution order:
     *   1. $messages['field.rule']   — specific to this field + rule combo
     *   2. $messages['rule']         — global override for this rule
     *   3. Default hardcoded message
     *
     * Placeholder :field is replaced with the field name.
     */
    protected function addError(string $field, string $message, string $ruleName = ''): void
    {
        // Check for custom message
        $customMessage = $this->customMessages[$field . '.' . $ruleName]
            ?? $this->customMessages[$ruleName]
            ?? null;

        if ($customMessage) {
            $message = str_replace(':field', $field, $customMessage);
        }

        $this->errors[$field][] = $message;
    }

    // -------------------------------------------------------------------------
    // Safe Identifier Validation (SQL Injection Prevention)
    // -------------------------------------------------------------------------

    /**
     * Validate that a SQL identifier (table/column name) is safe.
     *
     * Security Fix: Prevents SQL Injection in unique/exists rules.
     * Only allows alphanumeric, underscores, and dots (for table.column syntax).
     *
     * Satisfies: OWASP A03:2021, CWE-89, NIST SI-10
     */
    protected function isSafeIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_.]+$/', $identifier);
    }

    // -------------------------------------------------------------------------
    // Validation Rules
    // -------------------------------------------------------------------------

    protected function validateRequired(string $field, $value, array $params): bool
    {
        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            $this->addError($field, "The {$field} field is required.", 'required');
            return false;
        }
        return true;
    }

    protected function validateEmail(string $field, $value, array $params): bool
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "The {$field} must be a valid email address.", 'email');
            return false;
        }
        return true;
    }

    protected function validateMin(string $field, $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);
        if ($value && strlen((string)$value) < $min) {
            $this->addError($field, "The {$field} must be at least {$min} characters.", 'min');
            return false;
        }
        return true;
    }

    protected function validateMax(string $field, $value, array $params): bool
    {
        $max = (int) ($params[0] ?? 0);
        if ($value && strlen((string)$value) > $max) {
            $this->addError($field, "The {$field} may not be greater than {$max} characters.", 'max');
            return false;
        }
        return true;
    }

    protected function validateConfirmed(string $field, $value, array $params): bool
    {
        $confirmationField = $field . '_confirmation';
        $confirmationValue = $this->data[$confirmationField] ?? null;

        if ($value !== $confirmationValue) {
            $this->addError($field, "The {$field} confirmation does not match.", 'confirmed');
            return false;
        }
        return true;
    }

    protected function validateNumeric(string $field, $value, array $params): bool
    {
        if ($value && !is_numeric($value)) {
            $this->addError($field, "The {$field} must be a number.", 'numeric');
            return false;
        }
        return true;
    }

    protected function validateInteger(string $field, $value, array $params): bool
    {
        if ($value && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->addError($field, "The {$field} must be an integer.", 'integer');
            return false;
        }
        return true;
    }

    protected function validateIn(string $field, $value, array $params): bool
    {
        if ($value && !in_array($value, $params)) {
            $allowed = implode(', ', $params);
            $this->addError($field, "The {$field} must be one of: {$allowed}.", 'in');
            return false;
        }
        return true;
    }

    protected function validateNotIn(string $field, $value, array $params): bool
    {
        if ($value && in_array($value, $params)) {
            $this->addError($field, "The {$field} has an invalid value.", 'not_in');
            return false;
        }
        return true;
    }

    protected function validateUrl(string $field, $value, array $params): bool
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "The {$field} must be a valid URL.", 'url');
            return false;
        }
        return true;
    }

    protected function validateDate(string $field, $value, array $params): bool
    {
        if ($value && strtotime($value) === false) {
            $this->addError($field, "The {$field} is not a valid date.", 'date');
            return false;
        }
        return true;
    }

    protected function validateAlpha(string $field, $value, array $params): bool
    {
        if ($value && !ctype_alpha($value)) {
            $this->addError($field, "The {$field} may only contain letters.", 'alpha');
            return false;
        }
        return true;
    }

    /**
     * Validate that the field contains only letters and numbers.
     *
     * Method name normalized from 'alpha_num' rule via ucwords().
     * Backward compatible: 'alpha_num' in rule string → validateAlphaNum().
     */
    protected function validateAlphaNum(string $field, $value, array $params): bool
    {
        if ($value && !ctype_alnum($value)) {
            $this->addError($field, "The {$field} may only contain letters and numbers.", 'alpha_num');
            return false;
        }
        return true;
    }

    protected function validateRegex(string $field, $value, array $params): bool
    {
        $pattern = $params[0] ?? '';
        if ($value && !preg_match($pattern, $value)) {
            $this->addError($field, "The {$field} format is invalid.", 'regex');
            return false;
        }
        return true;
    }

    /**
     * Validate that a value is unique in a database table.
     *
     * Usage: 'email' => 'unique:users,email' or 'unique:users,email,5' (except ID 5)
     *
     * Security Fix: Table and column names are validated against a safe identifier
     * regex to prevent SQL Injection (CWE-89).
     *
     * Failure mode: fail-CLOSED. If the database query fails, validation FAILS
     * to prevent duplicate data from being inserted during DB errors.
     */
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

        // Security: Validate identifiers to prevent SQL Injection
        if (!$this->isSafeIdentifier($table) || !$this->isSafeIdentifier($column)) {
            $this->addError($field, "Validation configuration error for {$field}.", 'unique');
            return false;
        }

        try {
            $pdo = \LunoxHoshizaki\Database\Model::getConnection();
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
            $bindings = [$value];

            if ($exceptId) {
                $sql .= " AND id != ?";
                $bindings[] = $exceptId;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($bindings);
            $count = (int) $stmt->fetchColumn();

            if ($count > 0) {
                $this->addError($field, "The {$field} has already been taken.", 'unique');
                return false;
            }
        } catch (\Exception $e) {
            // Fail-CLOSED: if DB check fails, reject the data to prevent duplicates
            $this->addError($field, "Unable to verify uniqueness of {$field}. Please try again.", 'unique');
            return false;
        }

        return true;
    }

    /**
     * Validate that a value exists in a database table.
     *
     * Usage: 'role_id' => 'exists:roles,id'
     *
     * Security Fix: Table and column names validated against safe identifier regex.
     * Failure mode: fail-CLOSED.
     */
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

        // Security: Validate identifiers to prevent SQL Injection
        if (!$this->isSafeIdentifier($table) || !$this->isSafeIdentifier($column)) {
            $this->addError($field, "Validation configuration error for {$field}.", 'exists');
            return false;
        }

        try {
            $pdo = \LunoxHoshizaki\Database\Model::getConnection();
            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$value]);
            $count = (int) $stmt->fetchColumn();

            if ($count === 0) {
                $this->addError($field, "The selected {$field} is invalid.", 'exists');
                return false;
            }
        } catch (\Exception $e) {
            // Fail-CLOSED: if DB check fails, reject the data
            $this->addError($field, "Unable to verify existence of {$field}. Please try again.", 'exists');
            return false;
        }

        return true;
    }

    protected function validateBetween(string $field, $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);
        $max = (int) ($params[1] ?? 0);
        $length = strlen((string) $value);

        if ($value && ($length < $min || $length > $max)) {
            $this->addError($field, "The {$field} must be between {$min} and {$max} characters.", 'between');
            return false;
        }
        return true;
    }

    protected function validateSize(string $field, $value, array $params): bool
    {
        $size = (int) ($params[0] ?? 0);
        if ($value && strlen((string) $value) !== $size) {
            $this->addError($field, "The {$field} must be exactly {$size} characters.", 'size');
            return false;
        }
        return true;
    }

    protected function validateBoolean(string $field, $value, array $params): bool
    {
        $acceptable = [true, false, 0, 1, '0', '1', 'true', 'false'];
        if (!is_null($value) && !in_array($value, $acceptable, true)) {
            $this->addError($field, "The {$field} must be true or false.", 'boolean');
            return false;
        }
        return true;
    }

    protected function validateArray(string $field, $value, array $params): bool
    {
        if ($value && !is_array($value)) {
            $this->addError($field, "The {$field} must be an array.", 'array');
            return false;
        }
        return true;
    }

    protected function validateString(string $field, $value, array $params): bool
    {
        if ($value && !is_string($value)) {
            $this->addError($field, "The {$field} must be a string.", 'string');
            return false;
        }
        return true;
    }

    /**
     * Validate that a password meets minimum complexity requirements.
     * Default: min 8 chars, at least 1 uppercase, 1 lowercase, 1 digit.
     *
     * Usage in rules: 'password' => 'required|password'
     *
     * Gap R5 Fix: Password complexity enforcement.
     * Satisfies: NIST SP 800-63B IA-5(1), OWASP A07:2021, ISO A.8.5
     */
    protected function validatePassword(string $field, $value, array $params): bool
    {
        if (!$value) {
            return true; // Let 'required' handle empty values
        }

        $errors = [];

        if (strlen((string) $value) < 8) {
            $errors[] = 'at least 8 characters';
        }
        if (!preg_match('/[A-Z]/', (string) $value)) {
            $errors[] = 'at least one uppercase letter';
        }
        if (!preg_match('/[a-z]/', (string) $value)) {
            $errors[] = 'at least one lowercase letter';
        }
        if (!preg_match('/[0-9]/', (string) $value)) {
            $errors[] = 'at least one number';
        }

        if (!empty($errors)) {
            $this->addError($field, "The {$field} must contain: " . implode(', ', $errors) . '.', 'password');
            return false;
        }

        return true;
    }

    /**
     * Validate that a password meets STRONG complexity requirements.
     * Requires: min 12 chars, uppercase, lowercase, digit, AND special character.
     *
     * Usage in rules: 'password' => 'required|password_strength'
     *
     * Satisfies: NIST SP 800-63B (AAL2+), ISO A.8.24
     */
    protected function validatePasswordStrength(string $field, $value, array $params): bool
    {
        if (!$value) {
            return true;
        }

        $errors = [];

        if (strlen((string) $value) < 12) {
            $errors[] = 'at least 12 characters';
        }
        if (!preg_match('/[A-Z]/', (string) $value)) {
            $errors[] = 'at least one uppercase letter';
        }
        if (!preg_match('/[a-z]/', (string) $value)) {
            $errors[] = 'at least one lowercase letter';
        }
        if (!preg_match('/[0-9]/', (string) $value)) {
            $errors[] = 'at least one number';
        }
        if (!preg_match('/[\W_]/', (string) $value)) {
            $errors[] = 'at least one special character (!@#$%^&* etc.)';
        }

        if (!empty($errors)) {
            $this->addError($field, "The {$field} must contain: " . implode(', ', $errors) . '.', 'password_strength');
            return false;
        }

        return true;
    }

    /**
     * Validate uploaded file extension against an allowed list.
     *
     * Usage in rules: 'avatar' => 'mimes:jpg,png,webp'
     *
     * Gap R1 (Validator-side) Fix: file extension enforcement via validation rules.
     * Satisfies: OWASP A04:2021, CWE-434, NIST SI-10
     */
    protected function validateMimes(string $field, $value, array $params): bool
    {
        // Value here should be the file array from $_FILES
        $file = $this->data[$field] ?? null;

        // If it's an array (file upload), validate the extension
        if (!is_array($file) || empty($file['name'])) {
            return true; // Let 'required' handle missing files
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed   = array_map('strtolower', $params);

        if (!in_array($extension, $allowed, true)) {
            $this->addError($field, "The {$field} must be a file of type: " . implode(', ', $allowed) . '.', 'mimes');
            return false;
        }

        return true;
    }

    /**
     * Validate that an uploaded file does not exceed a given size in kilobytes.
     *
     * Usage in rules: 'avatar' => 'max_size:2048'  (2048 KB = 2 MB)
     *
     * Satisfies: OWASP A04:2021, ISO A.8.6 – Capacity Management
     */
    protected function validateMaxSize(string $field, $value, array $params): bool
    {
        $file    = $this->data[$field] ?? null;
        $maxKb   = (int) ($params[0] ?? 2048); // default 2 MB

        if (!is_array($file) || !isset($file['size'])) {
            return true;
        }

        $fileSizeKb = $file['size'] / 1024;

        if ($fileSizeKb > $maxKb) {
            $this->addError($field, "The {$field} may not be larger than {$maxKb} KB.", 'max_size');
            return false;
        }

        return true;
    }
}

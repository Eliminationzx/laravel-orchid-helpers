<?php

declare(strict_types=1);

namespace OrchidHelpers\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Validate data against rules
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     * @throws ValidationException
     */
    public function validate(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Validate data without throwing exception
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data, array $rules, array $messages = [], array $customAttributes = []): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, $rules, $messages, $customAttributes);
    }

    /**
     * Check if data is valid
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return bool
     */
    public function isValid(array $data, array $rules, array $messages = [], array $customAttributes = []): bool
    {
        $validator = $this->validator($data, $rules, $messages, $customAttributes);
        return !$validator->fails();
    }

    /**
     * Get validation errors
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     */
    public function errors(array $data, array $rules, array $messages = [], array $customAttributes = []): array
    {
        $validator = $this->validator($data, $rules, $messages, $customAttributes);
        return $validator->errors()->toArray();
    }

    /**
     * Validate email address
     *
     * @param  string  $email
     * @param  bool  $checkDns
     * @return bool
     */
    public function validateEmail(string $email, bool $checkDns = false): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        if ($checkDns) {
            $domain = substr(strrchr($email, "@"), 1);
            return checkdnsrr($domain, 'MX');
        }
        
        return true;
    }

    /**
     * Validate URL
     *
     * @param  string  $url
     * @param  bool  $checkActive
     * @return bool
     */
    public function validateUrl(string $url, bool $checkActive = false): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        if ($checkActive) {
            $headers = @get_headers($url);
            return $headers && strpos($headers[0], '200') !== false;
        }
        
        return true;
    }

    /**
     * Validate phone number
     *
     * @param  string  $phone
     * @param  string  $countryCode
     * @return bool
     */
    public function validatePhone(string $phone, string $countryCode = 'US'): bool
    {
        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $phone);
        
        // Basic validation for different countries
        $patterns = [
            'US' => '/^1?\d{10}$/',
            'UK' => '/^44\d{10}$/',
            'CA' => '/^1?\d{10}$/',
            'AU' => '/^61\d{9}$/',
            'DE' => '/^49\d{10,11}$/',
            'FR' => '/^33\d{9}$/',
            'JP' => '/^81\d{9,10}$/',
        ];
        
        $pattern = $patterns[$countryCode] ?? '/^\d{10,15}$/';
        
        return preg_match($pattern, $digits) === 1;
    }

    /**
     * Validate credit card number
     *
     * @param  string  $cardNumber
     * @return bool
     */
    public function validateCreditCard(string $cardNumber): bool
    {
        // Remove all non-digit characters
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        // Check length
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }
        
        // Luhn algorithm
        $sum = 0;
        $reverse = strrev($cardNumber);
        
        for ($i = 0; $i < strlen($reverse); $i++) {
            $digit = (int) $reverse[$i];
            
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return $sum % 10 === 0;
    }

    /**
     * Validate date
     *
     * @param  string  $date
     * @param  string  $format
     * @return bool
     */
    public function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate date range
     *
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  string  $format
     * @return bool
     */
    public function validateDateRange(string $startDate, string $endDate, string $format = 'Y-m-d'): bool
    {
        if (!$this->validateDate($startDate, $format) || !$this->validateDate($endDate, $format)) {
            return false;
        }
        
        $start = \DateTime::createFromFormat($format, $startDate);
        $end = \DateTime::createFromFormat($format, $endDate);
        
        return $start <= $end;
    }

    /**
     * Validate password strength
     *
     * @param  string  $password
     * @param  int  $minLength
     * @param  bool  $requireUppercase
     * @param  bool  $requireLowercase
     * @param  bool  $requireNumbers
     * @param  bool  $requireSpecialChars
     * @return array
     */
    public function validatePasswordStrength(
        string $password,
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumbers = true,
        bool $requireSpecialChars = true
    ): array {
        $errors = [];
        
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long.";
        }
        
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        
        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        
        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        
        if ($requireSpecialChars && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }
        
        $strength = 0;
        $criteria = 0;
        $totalCriteria = 4;
        
        if (strlen($password) >= $minLength) {
            $criteria++;
            $strength += 25;
        }
        
        if (preg_match('/[A-Z]/', $password)) {
            $criteria++;
            $strength += 25;
        }
        
        if (preg_match('/[a-z]/', $password)) {
            $criteria++;
            $strength += 25;
        }
        
        if (preg_match('/[0-9]/', $password) || preg_match('/[^A-Za-z0-9]/', $password)) {
            $criteria++;
            $strength += 25;
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'strength' => $strength,
            'criteria_met' => $criteria,
            'total_criteria' => $totalCriteria,
            'strength_level' => $this->getPasswordStrengthLevel($strength),
        ];
    }

    /**
     * Get password strength level
     *
     * @param  int  $strength
     * @return string
     */
    private function getPasswordStrengthLevel(int $strength): string
    {
        if ($strength >= 90) {
            return 'very_strong';
        } elseif ($strength >= 70) {
            return 'strong';
        } elseif ($strength >= 50) {
            return 'moderate';
        } elseif ($strength >= 30) {
            return 'weak';
        } else {
            return 'very_weak';
        }
    }

    /**
     * Validate file upload
     *
     * @param  array  $file
     * @param  array  $allowedExtensions
     * @param  int  $maxSize
     * @param  array  $allowedMimeTypes
     * @return array
     */
    public function validateFileUpload(array $file, array $allowedExtensions = [], int $maxSize = 10485760, array $allowedMimeTypes = []): array
    {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || $file['tmp_name'] === '') {
            $errors[] = 'No file was uploaded.';
            return ['is_valid' => false, 'errors' => $errors];
        }
        
        // Check for upload errors
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $this->getUploadErrorMessage($file['error']);
            return ['is_valid' => false, 'errors' => $errors];
        }
        
        // Check file size
        if (isset($file['size']) && $file['size'] > $maxSize) {
            $maxSizeMb = round($maxSize / 1048576, 2);
            $errors[] = "File size must be less than {$maxSizeMb} MB.";
        }
        
        // Check file extension
        if (!empty($allowedExtensions) && isset($file['name'])) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions);
            }
        }
        
        // Check MIME type
        if (!empty($allowedMimeTypes) && isset($file['tmp_name'])) {
            $mimeType = mime_content_type($file['tmp_name']);
            if (!in_array($mimeType, $allowedMimeTypes)) {
                $errors[] = 'File MIME type not allowed.';
            }
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get upload error message
     *
     * @param  int  $errorCode
     * @return string
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        ];
        
        return $messages[$errorCode] ?? 'Unknown upload error.';
    }

    /**
     * Validate JSON string
     *
     * @param  string  $json
     * @return bool
     */
    public function validateJson(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Validate IP address
     *
     * @param  string  $ip
     * @param  string  $type
     * @return bool
     */
    public function validateIp(string $ip, string $type = 'both'): bool
    {
        $flags = 0;
        
        switch ($type) {
            case 'ipv4':
                $flags = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $flags = FILTER_FLAG_IPV6;
                break;
            case 'private':
                $flags = FILTER_FLAG_NO_PRIV_RANGE;
                break;
            case 'public':
                $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
                break;
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
    }

    /**
     * Validate domain name
     *
     * @param  string  $domain
     * @return bool
     */
    public function validateDomain(string $domain): bool
    {
        return preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain) === 1;
    }

    /**
     * Validate array structure
     *
     * @param  array  $data
     * @param  array  $structure
     * @return array
     */
    public function validateArrayStructure(array $data, array $structure): array
    {
        $errors = [];
        
        foreach ($structure as $key => $rules) {
            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }
            
            foreach ($rules as $rule) {
                if ($rule === 'required' && !array_key_exists($key, $data)) {
                    $errors[] = "Field '{$key}' is required.";
                } elseif ($rule === 'array' && isset($data[$key]) && !is_array($data[$key])) {
                    $errors[] = "Field '{$key}' must be an array.";
                } elseif ($rule === 'string' && isset($data[$key]) && !is_string($data[$key])) {
                    $errors[] = "Field '{$key}' must be a string.";
                } elseif ($rule === 'numeric' && isset($data[$key]) && !is_numeric($data[$key])) {
                    $errors[] = "Field '{$key}' must be numeric.";
                } elseif ($rule === 'boolean' && isset($data[$key]) && !is_bool($data[$key])) {
                    $errors[] = "Field '{$key}' must be boolean.";
                }
            }
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Sanitize input
     *
     * @param  mixed  $input
     * @param  string  $type
     * @return mixed
     */
    public function sanitize($input, string $type = 'string')
    {
        switch ($type) {
            case 'string':
                return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'html':
                return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            case 'sql':
                // Basic SQL sanitization (use parameterized queries instead)
                return addslashes($input);
            default:
                return $input;
        }
    }

    /**
     * Get validation rules for common scenarios
     *
     * @param  string  $scenario
     * @return array
     */
    public function getRulesForScenario(string $scenario): array
    {
        $rules = [
            'user_registration' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|same:password',
            ],
            'user_login' => [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            'user_profile_update' => [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email',
                'phone' => 'sometimes|string|max:20',
                'avatar' => 'sometimes|image|max:2048',
            ],
            'product_create' => [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'images' => 'sometimes|array',
                'images.*' => 'image|max:5120',
            ],
            'order_create' => [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_address' => 'required|string|max:500',
                'billing_address' => 'sometimes|string|max:500',
                'payment_method' => 'required|string|in:credit_card,paypal,cash',
            ],
            'contact_form' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ],
        ];
        
        return $rules[$scenario] ?? [];
    }

    /**
     * Get validation messages for common scenarios
     *
     * @param  string  $scenario
     * @return array
     */
    public function getMessagesForScenario(string $scenario): array
    {
        $messages = [
            'user_registration' => [
                'email.unique' => 'This email is already registered.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.confirmed' => 'Password confirmation does not match.',
            ],
            'user_login' => [
                'email.email' => 'Please enter a valid email address.',
            ],
        ];
        
        return $messages[$scenario] ?? [];
    }

    /**
     * Get custom attributes for validation
     *
     * @param  string  $scenario
     * @return array
     */
    public function getAttributesForScenario(string $scenario): array
    {
        $attributes = [
            'user_registration' => [
                'name' => 'Full Name',
                'email' => 'Email Address',
                'password' => 'Password',
                'password_confirmation' => 'Password Confirmation',
            ],
            'user_login' => [
                'email' => 'Email Address',
                'password' => 'Password',
            ],
        ];
        
        return $attributes[$scenario] ?? [];
    }

    /**
     * Create validator for scenario
     *
     * @param  array  $data
     * @param  string  $scenario
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function createValidatorForScenario(array $data, string $scenario): \Illuminate\Contracts\Validation\Validator
    {
        $rules = $this->getRulesForScenario($scenario);
        $messages = $this->getMessagesForScenario($scenario);
        $attributes = $this->getAttributesForScenario($scenario);
        
        return $this->validator($data, $rules, $messages, $attributes);
    }

    /**
     * Validate data for scenario
     *
     * @param  array  $data
     * @param  string  $scenario
     * @return array
     * @throws ValidationException
     */
    public function validateScenario(array $data, string $scenario): array
    {
        $rules = $this->getRulesForScenario($scenario);
        $messages = $this->getMessagesForScenario($scenario);
        $attributes = $this->getAttributesForScenario($scenario);
        
        return $this->validate($data, $rules, $messages, $attributes);
    }
}

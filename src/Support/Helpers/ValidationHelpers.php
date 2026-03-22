<?php

declare(strict_types=1);

if (!function_exists('validate_email')) {
    /**
     * Validate an email address.
     *
     * @param string $email
     * @return bool
     */
    function validate_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validate_url')) {
    /**
     * Validate a URL.
     *
     * @param string $url
     * @return bool
     */
    function validate_url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('validate_ip')) {
    /**
     * Validate an IP address.
     *
     * @param string $ip
     * @param string $type
     * @return bool
     */
    function validate_ip(string $ip, string $type = 'both'): bool
    {
        $flags = match ($type) {
            'ipv4' => FILTER_FLAG_IPV4,
            'ipv6' => FILTER_FLAG_IPV6,
            default => 0,
        };
        return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
    }
}

if (!function_exists('validate_domain')) {
    /**
     * Validate a domain name.
     *
     * @param string $domain
     * @return bool
     */
    function validate_domain(string $domain): bool
    {
        return preg_match('/^(?!-)[A-Za-z0-9-]{1,63}(?<!-)(\.[A-Za-z]{2,})+$/', $domain) === 1;
    }
}

if (!function_exists('validate_phone')) {
    /**
     * Validate a phone number (basic international format).
     *
     * @param string $phone
     * @return bool
     */
    function validate_phone(string $phone): bool
    {
        return preg_match('/^\+?[1-9]\d{1,14}$/', $phone) === 1;
    }
}

if (!function_exists('validate_date')) {
    /**
     * Validate a date string.
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    function validate_date(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}

if (!function_exists('validate_datetime')) {
    /**
     * Validate a datetime string.
     *
     * @param string $datetime
     * @param string $format
     * @return bool
     */
    function validate_datetime(string $datetime, string $format = 'Y-m-d H:i:s'): bool
    {
        $d = DateTime::createFromFormat($format, $datetime);
        return $d && $d->format($format) === $datetime;
    }
}

if (!function_exists('validate_json')) {
    /**
     * Validate JSON string.
     *
     * @param string $json
     * @return bool
     */
    function validate_json(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('validate_uuid')) {
    /**
     * Validate UUID.
     *
     * @param string $uuid
     * @return bool
     */
    function validate_uuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
}

if (!function_exists('validate_credit_card')) {
    /**
     * Validate credit card number using Luhn algorithm.
     *
     * @param string $number
     * @return bool
     */
    function validate_credit_card(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);
        $length = strlen($number);
        if ($length < 13 || $length > 19) {
            return false;
        }

        $sum = 0;
        $reverse = strrev($number);
        for ($i = 0; $i < $length; $i++) {
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
}

if (!function_exists('validate_password_strength')) {
    /**
     * Validate password strength.
     *
     * @param string $password
     * @param int $minLength
     * @param bool $requireUppercase
     * @param bool $requireLowercase
     * @param bool $requireNumbers
     * @param bool $requireSpecial
     * @return bool
     */
    function validate_password_strength(
        string $password,
        int $minLength = 8,
        bool $requireUppercase = true,
        bool $requireLowercase = true,
        bool $requireNumbers = true,
        bool $requireSpecial = true
    ): bool {
        if (strlen($password) < $minLength) {
            return false;
        }

        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            return false;
        }

        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            return false;
        }

        if ($requireSpecial && !preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }

        return true;
    }
}

if (!function_exists('validate_alpha')) {
    /**
     * Validate alphabetic characters only.
     *
     * @param string $value
     * @return bool
     */
    function validate_alpha(string $value): bool
    {
        return preg_match('/^[A-Za-z]+$/', $value) === 1;
    }
}

if (!function_exists('validate_alpha_numeric')) {
    /**
     * Validate alphanumeric characters only.
     *
     * @param string $value
     * @return bool
     */
    function validate_alpha_numeric(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9]+$/', $value) === 1;
    }
}

if (!function_exists('validate_alpha_dash')) {
    /**
     * Validate alphanumeric with dashes and underscores.
     *
     * @param string $value
     * @return bool
     */
    function validate_alpha_dash(string $value): bool
    {
        return preg_match('/^[A-Za-z0-9_-]+$/', $value) === 1;
    }
}

if (!function_exists('validate_numeric')) {
    /**
     * Validate numeric value.
     *
     * @param string $value
     * @return bool
     */
    function validate_numeric(string $value): bool
    {
        return is_numeric($value);
    }
}

if (!function_exists('validate_integer')) {
    /**
     * Validate integer value.
     *
     * @param string $value
     * @return bool
     */
    function validate_integer(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}

if (!function_exists('validate_float')) {
    /**
     * Validate float value.
     *
     * @param string $value
     * @return bool
     */
    function validate_float(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
}

if (!function_exists('validate_boolean')) {
    /**
     * Validate boolean value.
     *
     * @param mixed $value
     * @return bool
     */
    function validate_boolean(mixed $value): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1', 'true', 'false', 'on', 'off', 'yes', 'no'], true);
    }
}

if (!function_exists('validate_min_length')) {
    /**
     * Validate minimum length.
     *
     * @param string $value
     * @param int $min
     * @return bool
     */
    function validate_min_length(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }
}

if (!function_exists('validate_max_length')) {
    /**
     * Validate maximum length.
     *
     * @param string $value
     * @param int $max
     * @return bool
     */
    function validate_max_length(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }
}

if (!function_exists('validate_length_between')) {
    /**
     * Validate length between min and max.
     *
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    function validate_length_between(string $value, int $min, int $max): bool
    {
        $length = strlen($value);
        return $length >= $min && $length <= $max;
    }
}

if (!function_exists('validate_in_array')) {
    /**
     * Validate value exists in array.
     *
     * @param mixed $value
     * @param array $array
     * @return bool
     */
    function validate_in_array(mixed $value, array $array): bool
    {
        return in_array($value, $array, true);
    }
}

if (!function_exists('validate_not_in_array')) {
    /**
     * Validate value does not exist in array.
     *
     * @param mixed $value
     * @param array $array
     * @return bool
     */
    function validate_not_in_array(mixed $value, array $array): bool
    {
        return !in_array($value, $array, true);
    }
}

if (!function_exists('validate_regex')) {
    /**
     * Validate against regex pattern.
     *
     * @param string $value
     * @param string $pattern
     * @return bool
     */
    function validate_regex(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }
}

if (!function_exists('validate_required')) {
    /**
     * Validate required field.
     *
     * @param mixed $value
     * @return bool
     */
    function validate_required(mixed $value): bool
    {
        if (is_string($value)) {
            $value = trim($value);
        }
        return !empty($value);
    }
}

if (!function_exists('validate_same')) {
    /**
     * Validate two values are the same.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     */
    function validate_same(mixed $value1, mixed $value2): bool
    {
        return $value1 === $value2;
    }
}

if (!function_exists('validate_different')) {
    /**
     * Validate two values are different.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     */
    function validate_different(mixed $value1, mixed $value2): bool
    {
        return $value1 !== $value2;
    }
}

if (!function_exists('validate_min_value')) {
    /**
     * Validate minimum numeric value.
     *
     * @param int|float $value
     * @param int|float $min
     * @return bool
     */
    function validate_min_value(int|float $value, int|float $min): bool
    {
        return $value >= $min;
    }
}

if (!function_exists('validate_max_value')) {
    /**
     * Validate maximum numeric value.
     *
     * @param int|float $value
     * @param int|float $max
     * @return bool
     */
    function validate_max_value(int|float $value, int|float $max): bool
    {
        return $value <= $max;
    }
}

if (!function_exists('validate_between')) {
    /**
     * Validate numeric value between min and max.
     *
     * @param int|float $value
     * @param int|float $min
     * @param int|float $max
     * @return bool
     */
    function validate_between(int|float $value, int|float $min, int|float $max): bool
    {
        return $value >= $min && $value <= $max;
    }
}

if (!function_exists('validate_file_extension')) {
    /**
     * Validate file extension.
     *
     * @param string $filename
     * @param array $extensions
     * @return bool
     */
    function validate_file_extension(string $filename, array $extensions): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $extensions, true);
    }
}

if (!function_exists('validate_file_mime')) {
    /**
     * Validate file MIME type.
     *
     * @param string $path
     * @param array $mimes
     * @return bool
     */
    function validate_file_mime(string $path, array $mimes): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return in_array($mime, $mimes, true);
    }
}

if (!function_exists('validate_file_size')) {
    /**
     * Validate file size.
     *
     * @param string $path
     * @param int $maxSize
     * @return bool
     */
    function validate_file_size(string $path, int $maxSize): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $size = filesize($path);
        return $size !== false && $size <= $maxSize;
    }
}

if (!function_exists('validate_custom')) {
    /**
     * Validate using a custom callback.
     *
     * @param mixed $value
     * @param callable $callback
     * @return bool
     */
    function validate_custom(mixed $value, callable $callback): bool
    {
        return $callback($value);
    }
}
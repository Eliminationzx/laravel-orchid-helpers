<?php

declare(strict_types=1);

use Illuminate\Support\Str;

if (!function_exists('str_after')) {
    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    function str_after(string $subject, string $search): string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}

if (!function_exists('str_after_last')) {
    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    function str_after_last(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }
}

if (!function_exists('str_before')) {
    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    function str_before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, $search, true);

        return $result === false ? $subject : $result;
    }
}

if (!function_exists('str_before_last')) {
    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     * @return string
     */
    function str_before_last(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return Str::substr($subject, 0, $pos);
    }
}

if (!function_exists('str_between')) {
    /**
     * Get the portion of a string between two given values.
     *
     * @param string $subject
     * @param string $from
     * @param string $to
     * @return string
     */
    function str_between(string $subject, string $from, string $to): string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return str_before(str_after($subject, $from), $to);
    }
}

if (!function_exists('str_contains_all')) {
    /**
     * Determine if a given string contains all array values.
     *
     * @param string $haystack
     * @param array<string> $needles
     * @return bool
     */
    function str_contains_all(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (!str_contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('str_contains_any')) {
    /**
     * Determine if a given string contains any of the array values.
     *
     * @param string $haystack
     * @param array<string> $needles
     * @return bool
     */
    function str_contains_any(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_ends_with_any')) {
    /**
     * Determine if a given string ends with any of the given values.
     *
     * @param string $haystack
     * @param array<string> $needles
     * @return bool
     */
    function str_ends_with_any(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_finish')) {
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $value
     * @param string $cap
     * @return string
     */
    function str_finish(string $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }
}

if (!function_exists('str_headline')) {
    /**
     * Convert the given string to title case for each word with special handling for acronyms.
     *
     * @param string $value
     * @return string
     */
    function str_headline(string $value): string
    {
        $parts = explode(' ', $value);

        $parts = count($parts) > 1
            ? array_map([Str::class, 'title'], $parts)
            : array_map([Str::class, 'title'], Str::ucsplit(implode('_', $parts)));

        $collapsed = Str::replace(['-', '_', ' '], '_', implode('_', $parts));

        return implode(' ', array_filter(explode('_', $collapsed)));
    }
}

if (!function_exists('str_is_uuid')) {
    /**
     * Determine if a given string is a valid UUID.
     *
     * @param string $value
     * @return bool
     */
    function str_is_uuid(string $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }
}

if (!function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    function str_limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }
}

if (!function_exists('str_mask')) {
    /**
     * Mask a portion of a string with a repeated character.
     *
     * @param string $string
     * @param string $character
     * @param int $index
     * @param int|null $length
     * @param string $encoding
     * @return string
     */
    function str_mask(string $string, string $character, int $index, ?int $length = null, string $encoding = 'UTF-8'): string
    {
        if ($character === '') {
            return $string;
        }

        $segment = mb_substr($string, $index, $length, $encoding);

        if ($segment === '') {
            return $string;
        }

        $strlen = mb_strlen($string, $encoding);
        $startIndex = $index;

        if ($index < 0) {
            $startIndex = $index < -$strlen ? 0 : $strlen + $index;
        }

        $start = mb_substr($string, 0, $startIndex, $encoding);
        $segmentLen = mb_strlen($segment, $encoding);
        $end = mb_substr($string, $startIndex + $segmentLen);

        return $start . str_repeat($character, $segmentLen) . $end;
    }
}

if (!function_exists('str_plural_studly')) {
    /**
     * Get the plural form of an English word in studly case.
     *
     * @param string $value
     * @param int $count
     * @return string
     */
    function str_plural_studly(string $value, int $count = 2): string
    {
        $plural = Str::plural($value, $count);

        return Str::studly($plural);
    }
}

if (!function_exists('str_remove')) {
    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param string|array<string> $search
     * @param string $subject
     * @param bool $caseSensitive
     * @return string
     */
    function str_remove(string|array $search, string $subject, bool $caseSensitive = true): string
    {
        $subject = $caseSensitive ? $subject : mb_strtolower($subject);

        foreach ((array) $search as $s) {
            $s = $caseSensitive ? $s : mb_strtolower($s);
            $subject = str_replace($s, '', $subject);
        }

        return $subject;
    }
}

if (!function_exists('str_reverse')) {
    /**
     * Reverse the given string.
     *
     * @param string $value
     * @return string
     */
    function str_reverse(string $value): string
    {
        return implode('', array_reverse(mb_str_split($value)));
    }
}

if (!function_exists('str_squish')) {
    /**
     * Remove all extra whitespace from the given string.
     *
     * @param string $value
     * @return string
     */
    function str_squish(string $value): string
    {
        return preg_replace('~(\s|\x{3164})+~u', ' ', preg_replace('~^[\s\x{FEFF}]+|[\s\x{FEFF}]+$~u', '', $value));
    }
}

if (!function_exists('str_substr_count')) {
    /**
     * Returns the number of substring occurrences.
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     * @param int|null $length
     * @return int
     */
    function str_substr_count(string $haystack, string $needle, int $offset = 0, ?int $length = null): int
    {
        if (!is_null($length)) {
            $haystack = substr($haystack, $offset, $length);
            $offset = 0;
        }

        return substr_count($haystack, $needle, $offset, $length);
    }
}

if (!function_exists('str_title_case')) {
    /**
     * Convert the given string to title case.
     *
     * @param string $value
     * @return string
     */
    function str_title_case(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}

if (!function_exists('str_to_words')) {
    /**
     * Convert a string to an array of words.
     *
     * @param string $string
     * @param int $words
     * @param string $end
     * @return string
     */
    function str_to_words(string $string, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $string, $matches);

        if (!isset($matches[0]) || mb_strlen($string) === mb_strlen($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $end;
    }
}

if (!function_exists('str_transliterate')) {
    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $value
     * @param string $language
     * @return string
     */
    function str_transliterate(string $value, string $language = 'en'): string
    {
        return Str::transliterate($value, $language);
    }
}

if (!function_exists('str_wrap')) {
    /**
     * Wrap the string with the given strings.
     *
     * @param string $value
     * @param string $before
     * @param string|null $after
     * @return string
     */
    function str_wrap(string $value, string $before, ?string $after = null): string
    {
        return $before . $value . ($after ?? $before);
    }
}

if (!function_exists('str_camel_to_snake')) {
    /**
     * Convert a camelCase string to snake_case.
     *
     * @param string $value
     * @return string
     */
    function str_camel_to_snake(string $value): string
    {
        return Str::snake($value);
    }
}

if (!function_exists('str_snake_to_camel')) {
    /**
     * Convert a snake_case string to camelCase.
     *
     * @param string $value
     * @return string
     */
    function str_snake_to_camel(string $value): string
    {
        return Str::camel($value);
    }
}

if (!function_exists('str_slug_unique')) {
    /**
     * Generate a unique URL-friendly "slug" from a given string with a uniqueness check.
     *
     * @param string $title
     * @param callable $uniquenessChecker
     * @param string $separator
     * @param string|null $language
     * @return string
     */
    function str_slug_unique(string $title, callable $uniquenessChecker, string $separator = '-', ?string $language = 'en'): string
    {
        $slug = Str::slug($title, $separator, $language);
        $originalSlug = $slug;
        $counter = 1;

        while ($uniquenessChecker($slug)) {
            $slug = $originalSlug . $separator . $counter;
            $counter++;
        }

        return $slug;
    }
}

if (!function_exists('str_truncate')) {
    /**
     * Truncate a string to a specified length and add an ellipsis if truncated.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    function str_truncate(string $value, int $limit, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }
}

if (!function_exists('str_is_json')) {
    /**
     * Determine if a given string is valid JSON.
     *
     * @param string $value
     * @return bool
     */
    function str_is_json(string $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }

        return true;
    }
}

if (!function_exists('str_is_email')) {
    /**
     * Determine if a given string is a valid email address.
     *
     * @param string $value
     * @return bool
     */
    function str_is_email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('str_is_url')) {
    /**
     * Determine if a given string is a valid URL.
     *
     * @param string $value
     * @return bool
     */
    function str_is_url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('str_is_ip')) {
    /**
     * Determine if a given string is a valid IP address.
     *
     * @param string $value
     * @return bool
     */
    function str_is_ip(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}
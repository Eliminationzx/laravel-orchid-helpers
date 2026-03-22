<?php

declare(strict_types=1);

if (!function_exists('number_format_human')) {
    /**
     * Format a number with human-readable suffixes (K, M, B, T).
     *
     * @param int|float $number
     * @param int $precision
     * @return string
     */
    function number_format_human(int|float $number, int $precision = 1): string
    {
        if ($number < 1000) {
            return (string) $number;
        }

        $units = ['', 'K', 'M', 'B', 'T'];
        $index = floor(log(abs($number), 1000));
        $index = min($index, count($units) - 1);

        $formatted = $number / pow(1000, $index);
        $formatted = round($formatted, $precision);

        return rtrim(rtrim(sprintf("%.{$precision}f", $formatted), '0'), '.') . $units[$index];
    }
}

if (!function_exists('number_format_currency')) {
    /**
     * Format a number as currency.
     *
     * @param int|float $amount
     * @param string $currency
     * @param int $decimals
     * @return string
     */
    function number_format_currency(int|float $amount, string $currency = 'USD', int $decimals = 2): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        $formatted = number_format(abs($amount), $decimals);

        if ($amount < 0) {
            return '-' . $symbol . $formatted;
        }

        return $symbol . $formatted;
    }
}

if (!function_exists('number_format_percentage')) {
    /**
     * Format a number as a percentage.
     *
     * @param int|float $number
     * @param int $decimals
     * @param bool $includeSymbol
     * @return string
     */
    function number_format_percentage(int|float $number, int $decimals = 2, bool $includeSymbol = true): string
    {
        $formatted = number_format($number, $decimals);
        return $includeSymbol ? $formatted . '%' : $formatted;
    }
}

if (!function_exists('number_round_up')) {
    /**
     * Round a number up to the nearest specified value.
     *
     * @param int|float $number
     * @param int $nearest
     * @return int|float
     */
    function number_round_up(int|float $number, int $nearest = 1): int|float
    {
        if ($nearest === 0) {
            return $number;
        }
        return ceil($number / $nearest) * $nearest;
    }
}

if (!function_exists('number_round_down')) {
    /**
     * Round a number down to the nearest specified value.
     *
     * @param int|float $number
     * @param int $nearest
     * @return int|float
     */
    function number_round_down(int|float $number, int $nearest = 1): int|float
    {
        if ($nearest === 0) {
            return $number;
        }
        return floor($number / $nearest) * $nearest;
    }
}

if (!function_exists('number_is_even')) {
    /**
     * Check if a number is even.
     *
     * @param int $number
     * @return bool
     */
    function number_is_even(int $number): bool
    {
        return $number % 2 === 0;
    }
}

if (!function_exists('number_is_odd')) {
    /**
     * Check if a number is odd.
     *
     * @param int $number
     * @return bool
     */
    function number_is_odd(int $number): bool
    {
        return $number % 2 !== 0;
    }
}

if (!function_exists('number_is_positive')) {
    /**
     * Check if a number is positive.
     *
     * @param int|float $number
     * @return bool
     */
    function number_is_positive(int|float $number): bool
    {
        return $number > 0;
    }
}

if (!function_exists('number_is_negative')) {
    /**
     * Check if a number is negative.
     *
     * @param int|float $number
     * @return bool
     */
    function number_is_negative(int|float $number): bool
    {
        return $number < 0;
    }
}

if (!function_exists('number_is_zero')) {
    /**
     * Check if a number is zero.
     *
     * @param int|float $number
     * @return bool
     */
    function number_is_zero(int|float $number): bool
    {
        return $number == 0;
    }
}

if (!function_exists('number_is_between')) {
    /**
     * Check if a number is between two values.
     *
     * @param int|float $number
     * @param int|float $min
     * @param int|float $max
     * @param bool $inclusive
     * @return bool
     */
    function number_is_between(int|float $number, int|float $min, int|float $max, bool $inclusive = true): bool
    {
        if ($inclusive) {
            return $number >= $min && $number <= $max;
        }
        return $number > $min && $number < $max;
    }
}

if (!function_exists('number_clamp')) {
    /**
     * Clamp a number between a minimum and maximum value.
     *
     * @param int|float $number
     * @param int|float $min
     * @param int|float $max
     * @return int|float
     */
    function number_clamp(int|float $number, int|float $min, int|float $max): int|float
    {
        return max($min, min($max, $number));
    }
}

if (!function_exists('number_random')) {
    /**
     * Generate a random number between min and max.
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    function number_random(int $min = 0, int $max = PHP_INT_MAX): int
    {
        return random_int($min, $max);
    }
}

if (!function_exists('number_random_float')) {
    /**
     * Generate a random float between min and max.
     *
     * @param float $min
     * @param float $max
     * @param int $precision
     * @return float
     */
    function number_random_float(float $min = 0.0, float $max = 1.0, int $precision = 2): float
    {
        $random = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return round($random, $precision);
    }
}

if (!function_exists('number_parse')) {
    /**
     * Parse a string to a number, with fallback.
     *
     * @param string $value
     * @param int|float|null $default
     * @return int|float|null
     */
    function number_parse(string $value, int|float|null $default = null): int|float|null
    {
        if (is_numeric($value)) {
            return $value + 0; // Convert to int or float
        }
        return $default;
    }
}

if (!function_exists('number_to_words')) {
    /**
     * Convert a number to words.
     *
     * @param int|float $number
     * @return string
     */
    function number_to_words(int|float $number): string
    {
        $whole = (int) abs($number);
        $fraction = abs($number) - $whole;

        $words = number_to_words_integer($whole);

        if ($fraction > 0) {
            $words .= ' point';
            $fractionStr = (string) $fraction;
            $fractionStr = substr($fractionStr, strpos($fractionStr, '.') + 1);
            for ($i = 0; $i < strlen($fractionStr); $i++) {
                $words .= ' ' . number_to_words_integer((int) $fractionStr[$i]);
            }
        }

        if ($number < 0) {
            $words = 'minus ' . $words;
        }

        return $words;
    }
}

if (!function_exists('number_to_words_integer')) {
    /**
     * Convert an integer to words.
     *
     * @param int $number
     * @return string
     */
    function number_to_words_integer(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $units = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
        $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $thousands = ['', 'thousand', 'million', 'billion', 'trillion'];

        $words = [];
        $groups = 0;

        while ($number > 0) {
            $group = $number % 1000;
            $number = (int) ($number / 1000);

            if ($group > 0) {
                $groupWords = '';

                $hundreds = (int) ($group / 100);
                $remainder = $group % 100;

                if ($hundreds > 0) {
                    $groupWords .= $units[$hundreds] . ' hundred';
                    if ($remainder > 0) {
                        $groupWords .= ' and ';
                    }
                }

                if ($remainder >= 20) {
                    $tensDigit = (int) ($remainder / 10);
                    $unitsDigit = $remainder % 10;
                    $groupWords .= $tens[$tensDigit];
                    if ($unitsDigit > 0) {
                        $groupWords .= '-' . $units[$unitsDigit];
                    }
                } elseif ($remainder >= 10) {
                    $groupWords .= $teens[$remainder - 10];
                } elseif ($remainder > 0) {
                    $groupWords .= $units[$remainder];
                }

                if ($groups > 0) {
                    $groupWords .= ' ' . $thousands[$groups];
                }

                array_unshift($words, $groupWords);
            }

            $groups++;
        }

        return implode(' ', $words);
    }
}

if (!function_exists('number_roman')) {
    /**
     * Convert a number to Roman numerals.
     *
     * @param int $number
     * @return string
     */
    function number_roman(int $number): string
    {
        if ($number < 1 || $number > 3999) {
            return (string) $number;
        }

        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            $matches = (int) ($number / $value);
            $result .= str_repeat($roman, $matches);
            $number %= $value;
        }

        return $result;
    }
}

if (!function_exists('number_average')) {
    /**
     * Calculate the average of numbers.
     *
     * @param array<int|float> $numbers
     * @return float|null
     */
    function number_average(array $numbers): ?float
    {
        if (empty($numbers)) {
            return null;
        }
        return array_sum($numbers) / count($numbers);
    }
}

if (!function_exists('number_median')) {
    /**
     * Calculate the median of numbers.
     *
     * @param array<int|float> $numbers
     * @return float|null
     */
    function number_median(array $numbers): ?float
    {
        if (empty($numbers)) {
            return null;
        }

        sort($numbers);
        $count = count($numbers);
        $middle = (int) floor($count / 2);

        if ($count % 2 === 0) {
            return ($numbers[$middle - 1] + $numbers[$middle]) / 2;
        }

        return $numbers[$middle];
    }
}

if (!function_exists('number_sum')) {
    /**
     * Calculate the sum of numbers.
     *
     * @param array<int|float> $numbers
     * @return int|float
     */
    function number_sum(array $numbers): int|float
    {
        return array_sum($numbers);
    }
}

if (!function_exists('number_product')) {
    /**
     * Calculate the product of numbers.
     *
     * @param array<int|float> $numbers
     * @return int|float
     */
    function number_product(array $numbers): int|float
    {
        return array_product($numbers);
    }
}

if (!function_exists('number_min')) {
    /**
     * Get the minimum value from numbers.
     *
     * @param array<int|float> $numbers
     * @return int|float|null
     */
    function number_min(array $numbers): int|float|null
    {
        if (empty($numbers)) {
            return null;
        }
        return min($numbers);
    }
}

if (!function_exists('number_max')) {
    /**
     * Get the maximum value from numbers.
     *
     * @param array<int|float> $numbers
     * @return int|float|null
     */
    function number_max(array $numbers): int|float|null
    {
        if (empty($numbers)) {
            return null;
        }
        return max($numbers);
    }
}

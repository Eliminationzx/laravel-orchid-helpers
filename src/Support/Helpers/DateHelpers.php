<?php

declare(strict_types=1);

use Carbon\Carbon;
use Carbon\CarbonInterface;

if (!function_exists('date_now')) {
    function date_now(DateTimeZone|string|null $tz = null): CarbonInterface
    {
        return Carbon::now($tz);
    }
}

if (!function_exists('date_today')) {
    function date_today(DateTimeZone|string|null $tz = null): CarbonInterface
    {
        return Carbon::today($tz);
    }
}

if (!function_exists('date_yesterday')) {
    function date_yesterday(DateTimeZone|string|null $tz = null): CarbonInterface
    {
        return Carbon::yesterday($tz);
    }
}

if (!function_exists('date_tomorrow')) {
    function date_tomorrow(DateTimeZone|string|null $tz = null): CarbonInterface
    {
        return Carbon::tomorrow($tz);
    }
}

if (!function_exists('date_parse')) {
    function date_parse(string $date, DateTimeZone|string|null $tz = null): ?CarbonInterface
    {
        try {
            return Carbon::parse($date, $tz);
        } catch (Exception) {
            return null;
        }
    }
}

if (!function_exists('date_is_valid')) {
    function date_is_valid(string $date): bool
    {
        return date_parse($date) !== null;
    }
}

if (!function_exists('date_format_localized')) {
    function date_format_localized(CarbonInterface|string $date, string $format = 'F j, Y'): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->translatedFormat($format);
    }
}

if (!function_exists('date_human_diff')) {
    function date_human_diff(CarbonInterface|string $from, CarbonInterface|string|null $to = null): string
    {
        if (is_string($from)) {
            $from = Carbon::parse($from);
        }
        if ($to === null) {
            return $from->diffForHumans();
        }
        if (is_string($to)) {
            $to = Carbon::parse($to);
        }
        return $from->diffForHumans($to);
    }
}

if (!function_exists('date_is_weekend')) {
    function date_is_weekend(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isWeekend();
    }
}

if (!function_exists('date_is_weekday')) {
    function date_is_weekday(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isWeekday();
    }
}

if (!function_exists('date_is_past')) {
    function date_is_past(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isPast();
    }
}

if (!function_exists('date_is_future')) {
    function date_is_future(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isFuture();
    }
}

if (!function_exists('date_is_today')) {
    function date_is_today(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isToday();
    }
}

if (!function_exists('date_is_yesterday')) {
    function date_is_yesterday(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isYesterday();
    }
}

if (!function_exists('date_is_tomorrow')) {
    function date_is_tomorrow(CarbonInterface|string $date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->isTomorrow();
    }
}

if (!function_exists('date_start_of_day')) {
    function date_start_of_day(CarbonInterface|string $date): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->startOfDay();
    }
}

if (!function_exists('date_end_of_day')) {
    function date_end_of_day(CarbonInterface|string $date): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->endOfDay();
    }
}

if (!function_exists('date_start_of_month')) {
    function date_start_of_month(CarbonInterface|string $date): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->startOfMonth();
    }
}

if (!function_exists('date_end_of_month')) {
    function date_end_of_month(CarbonInterface|string $date): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->endOfMonth();
    }
}

if (!function_exists('date_add_days')) {
    function date_add_days(CarbonInterface|string $date, int $days): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->addDays($days);
    }
}

if (!function_exists('date_sub_days')) {
    function date_sub_days(CarbonInterface|string $date, int $days): CarbonInterface
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->copy()->subDays($days);
    }
}

if (!function_exists('date_diff_in_days')) {
    function date_diff_in_days(CarbonInterface|string $from, CarbonInterface|string|null $to = null): int
    {
        if (is_string($from)) {
            $from = Carbon::parse($from);
        }
        if ($to === null) {
            $to = Carbon::now();
        } elseif (is_string($to)) {
            $to = Carbon::parse($to);
        }
        return $from->diffInDays($to);
    }
}

if (!function_exists('date_age')) {
    function date_age(CarbonInterface|string $birthDate, CarbonInterface|string|null $referenceDate = null): int
    {
        if (is_string($birthDate)) {
            $birthDate = Carbon::parse($birthDate);
        }
        if ($referenceDate === null) {
            $referenceDate = Carbon::now();
        } elseif (is_string($referenceDate)) {
            $referenceDate = Carbon::parse($referenceDate);
        }
        return $birthDate->diffInYears($referenceDate);
    }
}

if (!function_exists('date_is_between')) {
    function date_is_between(CarbonInterface|string $date, CarbonInterface|string $start, CarbonInterface|string $end, bool $inclusive = true): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        if (is_string($start)) {
            $start = Carbon::parse($start);
        }
        if (is_string($end)) {
            $end = Carbon::parse($end);
        }
        if ($inclusive) {
            return $date->betweenIncluded($start, $end);
        }
        return $date->between($start, $end);
    }
}

if (!function_exists('date_to_iso')) {
    function date_to_iso(CarbonInterface|string $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->toIso8601String();
    }
}

if (!function_exists('date_from_timestamp')) {
    function date_from_timestamp(int $timestamp, DateTimeZone|string|null $tz = null): CarbonInterface
    {
        return Carbon::createFromTimestamp($timestamp, $tz);
    }
}

if (!function_exists('date_to_timestamp')) {
    function date_to_timestamp(CarbonInterface|string $date): int
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->timestamp;
    }
}
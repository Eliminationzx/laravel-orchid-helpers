<?php

declare(strict_types=1);

use Illuminate\Support\Facades\URL as LaravelURL;
use Illuminate\Support\Str;

if (!function_exists('url_ensure_scheme')) {
    /**
     * Ensure URL has a scheme (add https:// if missing).
     *
     * @param string $url
     * @param string $scheme
     * @return string
     */
    function url_ensure_scheme(string $url, string $scheme = 'https://'): string
    {
        if (empty($url)) {
            return $url;
        }

        if (!preg_match('/^https?:\/\//i', $url)) {
            return $scheme . ltrim($url, '/');
        }

        return $url;
    }
}

if (!function_exists('url_remove_scheme')) {
    /**
     * Remove scheme from URL.
     *
     * @param string $url
     * @return string
     */
    function url_remove_scheme(string $url): string
    {
        return preg_replace('/^https?:\/\//i', '', $url);
    }
}

if (!function_exists('url_get_domain')) {
    /**
     * Extract domain from URL.
     *
     * @param string $url
     * @return string|null
     */
    function url_get_domain(string $url): ?string
    {
        $parsed = parse_url(url_ensure_scheme($url));
        return $parsed['host'] ?? null;
    }
}

if (!function_exists('url_get_path')) {
    /**
     * Extract path from URL.
     *
     * @param string $url
     * @return string|null
     */
    function url_get_path(string $url): ?string
    {
        $parsed = parse_url(url_ensure_scheme($url));
        return $parsed['path'] ?? null;
    }
}

if (!function_exists('url_get_query')) {
    /**
     * Extract query string from URL.
     *
     * @param string $url
     * @return string|null
     */
    function url_get_query(string $url): ?string
    {
        $parsed = parse_url(url_ensure_scheme($url));
        return $parsed['query'] ?? null;
    }
}

if (!function_exists('url_get_query_params')) {
    /**
     * Extract query parameters from URL as array.
     *
     * @param string $url
     * @return array<string, mixed>
     */
    function url_get_query_params(string $url): array
    {
        $query = url_get_query($url);
        if (!$query) {
            return [];
        }

        parse_str($query, $params);
        return $params;
    }
}

if (!function_exists('url_add_query_params')) {
    /**
     * Add query parameters to URL.
     *
     * @param string $url
     * @param array<string, mixed> $params
     * @param bool $merge
     * @return string
     */
    function url_add_query_params(string $url, array $params, bool $merge = true): string
    {
        if (empty($params)) {
            return $url;
        }

        $existing = url_get_query_params($url);
        if ($merge) {
            $params = array_merge($existing, $params);
        }

        $query = http_build_query($params);
        $urlParts = parse_url(url_ensure_scheme($url));

        $result = '';
        if (isset($urlParts['scheme'])) {
            $result .= $urlParts['scheme'] . '://';
        }
        if (isset($urlParts['host'])) {
            $result .= $urlParts['host'];
        }
        if (isset($urlParts['port'])) {
            $result .= ':' . $urlParts['port'];
        }
        if (isset($urlParts['path'])) {
            $result .= $urlParts['path'];
        }
        if ($query) {
            $result .= '?' . $query;
        }
        if (isset($urlParts['fragment'])) {
            $result .= '#' . $urlParts['fragment'];
        }

        return $result;
    }
}

if (!function_exists('url_remove_query_params')) {
    /**
     * Remove query parameters from URL.
     *
     * @param string $url
     * @param array<string> $params
     * @return string
     */
    function url_remove_query_params(string $url, array $params): string
    {
        $existing = url_get_query_params($url);
        foreach ($params as $param) {
            unset($existing[$param]);
        }

        $urlParts = parse_url(url_ensure_scheme($url));
        $result = '';
        if (isset($urlParts['scheme'])) {
            $result .= $urlParts['scheme'] . '://';
        }
        if (isset($urlParts['host'])) {
            $result .= $urlParts['host'];
        }
        if (isset($urlParts['port'])) {
            $result .= ':' . $urlParts['port'];
        }
        if (isset($urlParts['path'])) {
            $result .= $urlParts['path'];
        }
        if (!empty($existing)) {
            $result .= '?' . http_build_query($existing);
        }
        if (isset($urlParts['fragment'])) {
            $result .= '#' . $urlParts['fragment'];
        }

        return $result;
    }
}

if (!function_exists('url_is_absolute')) {
    /**
     * Check if URL is absolute.
     *
     * @param string $url
     * @return bool
     */
    function url_is_absolute(string $url): bool
    {
        return preg_match('/^https?:\/\//i', $url) === 1;
    }
}

if (!function_exists('url_is_relative')) {
    /**
     * Check if URL is relative.
     *
     * @param string $url
     * @return bool
     */
    function url_is_relative(string $url): bool
    {
        return !url_is_absolute($url);
    }
}

if (!function_exists('url_to_absolute')) {
    /**
     * Convert relative URL to absolute using base URL.
     *
     * @param string $relative
     * @param string $baseUrl
     * @return string
     */
    function url_to_absolute(string $relative, string $baseUrl): string
    {
        if (url_is_absolute($relative)) {
            return $relative;
        }

        $base = rtrim($baseUrl, '/');
        $relative = ltrim($relative, '/');

        return $base . '/' . $relative;
    }
}

if (!function_exists('url_slugify')) {
    /**
     * Create URL-friendly slug from string.
     *
     * @param string $text
     * @param string $separator
     * @param string $language
     * @return string
     */
    function url_slugify(string $text, string $separator = '-', string $language = 'en'): string
    {
        return Str::slug($text, $separator, $language);
    }
}

if (!function_exists('url_encode')) {
    /**
     * URL encode a string.
     *
     * @param string $value
     * @return string
     */
    function url_encode(string $value): string
    {
        return urlencode($value);
    }
}

if (!function_exists('url_decode')) {
    /**
     * URL decode a string.
     *
     * @param string $value
     * @return string
     */
    function url_decode(string $value): string
    {
        return urldecode($value);
    }
}

if (!function_exists('url_secure')) {
    /**
     * Generate a secure URL (HTTPS).
     *
     * @param string $path
     * @param array<string, mixed> $parameters
     * @return string
     */
    function url_secure(string $path, array $parameters = []): string
    {
        return LaravelURL::secure($path, $parameters);
    }
}

if (!function_exists('url_route')) {
    /**
     * Generate a URL to a named route.
     *
     * @param string $name
     * @param array<string, mixed> $parameters
     * @param bool $absolute
     * @return string
     */
    function url_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        return LaravelURL::route($name, $parameters, $absolute);
    }
}

if (!function_exists('url_action')) {
    /**
     * Generate a URL to a controller action.
     *
     * @param string|array $action
     * @param array<string, mixed> $parameters
     * @param bool $absolute
     * @return string
     */
    function url_action(string|array $action, array $parameters = [], bool $absolute = true): string
    {
        return LaravelURL::action($action, $parameters, $absolute);
    }
}

if (!function_exists('url_asset')) {
    /**
     * Generate a URL to an asset.
     *
     * @param string $path
     * @param bool $secure
     * @return string
     */
    function url_asset(string $path, bool $secure = null): string
    {
        return LaravelURL::asset($path, $secure);
    }
}

if (!function_exists('url_mix')) {
    /**
     * Generate a URL to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     * @return string
     */
    function url_mix(string $path, string $manifestDirectory = ''): string
    {
        return LaravelURL::mix($path, $manifestDirectory);
    }
}

if (!function_exists('url_signed')) {
    /**
     * Create a signed route URL.
     *
     * @param string $name
     * @param array<string, mixed> $parameters
     * @param \DateTimeInterface|\DateInterval|int|null $expiration
     * @param bool $absolute
     * @return string
     */
    function url_signed(
        string $name,
        array $parameters = [],
        DateTimeInterface|DateInterval|int|null $expiration = null,
        bool $absolute = true
    ): string {
        return LaravelURL::signedRoute($name, $parameters, $expiration, $absolute);
    }
}

if (!function_exists('url_temporary_signed')) {
    /**
     * Create a temporary signed route URL.
     *
     * @param string $name
     * @param \DateTimeInterface|\DateInterval|int $expiration
     * @param array<string, mixed> $parameters
     * @param bool $absolute
     * @return string
     */
    function url_temporary_signed(
        string $name,
        DateTimeInterface|DateInterval|int $expiration,
        array $parameters = [],
        bool $absolute = true
    ): string {
        return LaravelURL::temporarySignedRoute($name, $expiration, $parameters, $absolute);
    }
}

if (!function_exists('url_has_valid_signature')) {
    /**
     * Determine if the given request has a valid signature.
     *
     * @param \Illuminate\Http\Request|null $request
     * @return bool
     */
    function url_has_valid_signature($request = null): bool
    {
        return LaravelURL::hasValidSignature($request);
    }
}

if (!function_exists('url_previous')) {
    /**
     * Generate a URL to the previous location.
     *
     * @param mixed $fallback
     * @return string
     */
    function url_previous(mixed $fallback = false): string
    {
        return LaravelURL::previous($fallback);
    }
}

if (!function_exists('url_current')) {
    /**
     * Get the current URL.
     *
     * @param bool $withQuery
     * @return string
     */
    function url_current(bool $withQuery = true): string
    {
        $request = request();
        if (!$request) {
            return '';
        }

        if ($withQuery) {
            return $request->fullUrl();
        }

        return $request->url();
    }
}

if (!function_exists('url_canonical')) {
    /**
     * Generate canonical URL (strip query parameters except needed ones).
     *
     * @param string $url
     * @param array<string> $keepParams
     * @return string
     */
    function url_canonical(string $url, array $keepParams = []): string
    {
        $params = url_get_query_params($url);
        $filtered = [];

        foreach ($keepParams as $param) {
            if (isset($params[$param])) {
                $filtered[$param] = $params[$param];
            }
        }

        return url_add_query_params($url, $filtered, false);
    }
}

if (!function_exists('url_shorten')) {
    /**
     * Shorten a URL (basic implementation).
     *
     * @param string $url
     * @param int $length
     * @return string
     */
    function url_shorten(string $url, int $length = 50): string
    {
        if (strlen($url) <= $length) {
            return $url;
        }

        $parts = parse_url(url_ensure_scheme($url));
        $shortened = '';

        if (isset($parts['host'])) {
            $shortened .= $parts['host'];
        }

        if (isset($parts['path'])) {
            $path = $parts['path'];
            if (strlen($shortened . $path) > $length) {
                $path = substr($path, 0, $length - strlen($shortened) - 3) . '...';
            }
            $shortened .= $path;
        }

        return $shortened;
    }
}

if (!function_exists('url_is_same_domain')) {
    /**
     * Check if two URLs are on the same domain.
     *
     * @param string $url1
     * @param string $url2
     * @return bool
     */
    function url_is_same_domain(string $url1, string $url2): bool
    {
        $domain1 = url_get_domain($url1);
        $domain2 = url_get_domain($url2);

        if (!$domain1 || !$domain2) {
            return false;
        }

        return $domain1 === $domain2;
    }
}
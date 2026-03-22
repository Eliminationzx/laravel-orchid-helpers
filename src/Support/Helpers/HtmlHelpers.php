<?php

declare(strict_types=1);

if (!function_exists('html_encode')) {
    /**
     * Encode HTML special characters.
     *
     * @param string $value
     * @param bool $doubleEncode
     * @return string
     */
    function html_encode(string $value, bool $doubleEncode = true): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}

if (!function_exists('html_decode')) {
    /**
     * Decode HTML special characters.
     *
     * @param string $value
     * @return string
     */
    function html_decode(string $value): string
    {
        return htmlspecialchars_decode($value, ENT_QUOTES);
    }
}

if (!function_exists('html_strip_tags')) {
    /**
     * Strip HTML tags from string.
     *
     * @param string $value
     * @param string|null $allowedTags
     * @return string
     */
    function html_strip_tags(string $value, ?string $allowedTags = null): string
    {
        return strip_tags($value, $allowedTags);
    }
}

if (!function_exists('html_clean')) {
    /**
     * Clean HTML by removing dangerous tags and attributes.
     *
     * @param string $html
     * @return string
     */
    function html_clean(string $html): string
    {
        // Basic HTML cleaning if HTMLPurifier is not available
        // Remove script tags and event handlers
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/on\w+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*\'[^\']*\'/i', '', $html);
        $html = preg_replace('/on\w+\s*=\s*[^\s>]+/i', '', $html);
        
        // Allow only safe tags
        $allowedTags = '<p><br><div><span><strong><em><b><i><u><a><img><ul><ol><li><table><tr><td><th><thead><tbody><tfoot><h1><h2><h3><h4><h5><h6><blockquote><code><pre>';
        return strip_tags($html, $allowedTags);
    }
}

if (!function_exists('html_link')) {
    /**
     * Generate HTML link.
     *
     * @param string $url
     * @param string $text
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_link(string $url, string $text, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }
        return '<a href="' . html_encode($url) . '"' . $attrs . '>' . html_encode($text) . '</a>';
    }
}

if (!function_exists('html_image')) {
    /**
     * Generate HTML image.
     *
     * @param string $src
     * @param string $alt
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_image(string $src, string $alt = '', array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }
        return '<img src="' . html_encode($src) . '" alt="' . html_encode($alt) . '"' . $attrs . '>';
    }
}

if (!function_exists('html_div')) {
    /**
     * Generate HTML div.
     *
     * @param string $content
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_div(string $content, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }
        return '<div' . $attrs . '>' . $content . '</div>';
    }
}

if (!function_exists('html_span')) {
    /**
     * Generate HTML span.
     *
     * @param string $content
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_span(string $content, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }
        return '<span' . $attrs . '>' . $content . '</span>';
    }
}

if (!function_exists('html_ul')) {
    /**
     * Generate HTML unordered list.
     *
     * @param array<string> $items
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_ul(array $items, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }

        $list = '<ul' . $attrs . '>';
        foreach ($items as $item) {
            $list .= '<li>' . html_encode($item) . '</li>';
        }
        $list .= '</ul>';

        return $list;
    }
}

if (!function_exists('html_ol')) {
    /**
     * Generate HTML ordered list.
     *
     * @param array<string> $items
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_ol(array $items, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }

        $list = '<ol' . $attrs . '>';
        foreach ($items as $item) {
            $list .= '<li>' . html_encode($item) . '</li>';
        }
        $list .= '</ol>';

        return $list;
    }
}

if (!function_exists('html_table')) {
    /**
     * Generate HTML table.
     *
     * @param array<array<string>> $rows
     * @param array<string>|null $headers
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_table(array $rows, ?array $headers = null, array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }

        $table = '<table' . $attrs . '>';

        if ($headers) {
            $table .= '<thead><tr>';
            foreach ($headers as $header) {
                $table .= '<th>' . html_encode($header) . '</th>';
            }
            $table .= '</tr></thead>';
        }

        $table .= '<tbody>';
        foreach ($rows as $row) {
            $table .= '<tr>';
            foreach ($row as $cell) {
                $table .= '<td>' . html_encode($cell) . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';

        return $table;
    }
}

if (!function_exists('html_form')) {
    /**
     * Generate HTML form.
     *
     * @param string $action
     * @param string $method
     * @param string $content
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_form(string $action, string $method = 'POST', string $content = '', array $attributes = []): string
    {
        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= ' ' . $key . '="' . html_encode($value) . '"';
        }

        return '<form action="' . html_encode($action) . '" method="' . html_encode($method) . '"' . $attrs . '>' . $content . '</form>';
    }
}

if (!function_exists('html_input')) {
    /**
     * Generate HTML input.
     *
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_input(string $type, string $name, mixed $value = null, array $attributes = []): string
    {
        $attrs = '';
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        
        if ($value !== null) {
            $attributes['value'] = $value;
        }

        foreach ($attributes as $key => $val) {
            $attrs .= ' ' . $key . '="' . html_encode((string) $val) . '"';
        }

        return '<input' . $attrs . '>';
    }
}

if (!function_exists('html_textarea')) {
    /**
     * Generate HTML textarea.
     *
     * @param string $name
     * @param string $value
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_textarea(string $name, string $value = '', array $attributes = []): string
    {
        $attrs = '';
        $attributes['name'] = $name;

        foreach ($attributes as $key => $val) {
            $attrs .= ' ' . $key . '="' . html_encode((string) $val) . '"';
        }

        return '<textarea' . $attrs . '>' . html_encode($value) . '</textarea>';
    }
}

if (!function_exists('html_select')) {
    /**
     * Generate HTML select.
     *
     * @param string $name
     * @param array<string, string> $options
     * @param string|array|null $selected
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_select(string $name, array $options, string|array|null $selected = null, array $attributes = []): string
    {
        $attrs = '';
        $attributes['name'] = $name;

        foreach ($attributes as $key => $val) {
            $attrs .= ' ' . $key . '="' . html_encode((string) $val) . '"';
        }

        $select = '<select' . $attrs . '>';
        foreach ($options as $value => $label) {
            $isSelected = false;
            if (is_array($selected)) {
                $isSelected = in_array($value, $selected, true);
            } else {
                $isSelected = (string) $value === (string) $selected;
            }

            $selectedAttr = $isSelected ? ' selected' : '';
            $select .= '<option value="' . html_encode((string) $value) . '"' . $selectedAttr . '>' . html_encode($label) . '</option>';
        }
        $select .= '</select>';

        return $select;
    }
}

if (!function_exists('html_attributes')) {
    /**
     * Convert array to HTML attributes string.
     *
     * @param array<string, mixed> $attributes
     * @return string
     */
    function html_attributes(array $attributes): string
    {
        $result = '';
        foreach ($attributes as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $result .= ' ' . $key;
            } else {
                $result .= ' ' . $key . '="' . html_encode((string) $value) . '"';
            }
        }
        return $result;
    }
}

if (!function_exists('html_class')) {
    /**
     * Generate CSS class string from array.
     *
     * @param array<string, bool> $classes
     * @return string
     */
    function html_class(array $classes): string
    {
        $result = [];
        foreach ($classes as $class => $condition) {
            if (is_int($class)) {
                $result[] = $condition;
            } elseif ($condition) {
                $result[] = $class;
            }
        }
        return implode(' ', $result);
    }
}

if (!function_exists('html_style')) {
    /**
     * Generate CSS style string from array.
     *
     * @param array<string, string> $styles
     * @return string
     */
    function html_style(array $styles): string
    {
        $result = [];
        foreach ($styles as $property => $value) {
            $result[] = $property . ': ' . $value;
        }
        return implode('; ', $result);
    }
}

if (!function_exists('html_meta')) {
    /**
     * Generate HTML meta tag.
     *
     * @param string $name
     * @param string $content
     * @return string
     */
    function html_meta(string $name, string $content): string
    {
        return '<meta name="' . html_encode($name) . '" content="' . html_encode($content) . '">';
    }
}

if (!function_exists('html_csrf_token')) {
    /**
     * Generate CSRF token input.
     *
     * @return string
     */
    function html_csrf_token(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('html_nl2br')) {
    /**
     * Convert newlines to HTML line breaks.
     *
     * @param string $text
     * @param bool $isXhtml
     * @return string
     */
    function html_nl2br(string $text, bool $isXhtml = true): string
    {
        return nl2br($text, $isXhtml);
    }
}

if (!function_exists('html_br2nl')) {
    /**
     * Convert HTML line breaks to newlines.
     *
     * @param string $text
     * @return string
     */
    function html_br2nl(string $text): string
    {
        return preg_replace('/<br\s*\/?>/i', "\n", $text);
    }
}

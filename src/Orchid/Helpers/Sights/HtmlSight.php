<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class HtmlSight
{
    public static function make(
        string $name, 
        string $title = null,
        bool $sanitize = true,
        array $allowedTags = null,
        array $allowedAttributes = null,
        bool $collapsible = false,
        int $maxHeight = null
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $sanitize, $allowedTags, $allowedAttributes, $collapsible, $maxHeight) : string {
                    $html = $repository->get($name);

                    if($html === null || $html === '') {
                        return '';
                    }

                    $html = (string) $html;
                    
                    // Sanitize HTML if needed
                    if($sanitize) {
                        $html = self::sanitizeHtml($html, $allowedTags, $allowedAttributes);
                    }
                    
                    $style = '';
                    if($maxHeight !== null) {
                        $style = "style=\"max-height: {$maxHeight}px; overflow: auto;\"";
                    }
                    
                    $content = <<<HTML
<div class="html-content" {$style}>
    {$html}
</div>
HTML;

                    if($collapsible) {
                        $id = 'html-' . md5($name . $html);
                        return <<<HTML
<div class="html-sight-container">
    <button type="button" class="btn btn-sm btn-outline-secondary mb-2" 
            data-bs-toggle="collapse" data-bs-target="#{$id}">
        Show/Hide HTML
    </button>
    <div class="collapse show" id="{$id}">
        {$content}
    </div>
</div>
HTML;
                    }

                    return $content;
                }
            );
    }

    private static function sanitizeHtml(string $html, ?array $allowedTags = null, ?array $allowedAttributes = null): string
    {
        if ($allowedTags === null) {
            // Default allowed tags for safe HTML rendering
            $allowedTags = [
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'p', 'br', 'hr', 'div', 'span',
                'strong', 'b', 'em', 'i', 'u', 's',
                'ul', 'ol', 'li',
                'table', 'thead', 'tbody', 'tr', 'th', 'td',
                'a', 'img',
                'code', 'pre', 'blockquote',
                'small', 'sub', 'sup'
            ];
        }
        
        if ($allowedAttributes === null) {
            // Default allowed attributes
            $allowedAttributes = [
                'href', 'src', 'alt', 'title', 'width', 'height',
                'class', 'id', 'style', 'target', 'rel'
            ];
        }
        
        // Convert allowed tags to string for strip_tags
        $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
        
        // First, strip all tags except allowed ones
        $html = strip_tags($html, $allowedTagsString);
        
        // Remove potentially dangerous attributes using regex
        $html = preg_replace_callback('/<(\w+)([^>]*)>/', function($matches) use ($allowedAttributes) {
            $tag = $matches[1];
            $attributes = $matches[2];
            
            // Parse attributes
            $safeAttributes = '';
            if (!empty($attributes)) {
                preg_match_all('/(\w+)=("[^"]*"|\'[^\']*\'|[^"\'\s>]+)/', $attributes, $attrMatches, PREG_SET_ORDER);
                
                foreach ($attrMatches as $attrMatch) {
                    $attrName = strtolower($attrMatch[1]);
                    $attrValue = $attrMatch[2];
                    
                    // Remove quotes if present
                    if (($attrValue[0] === '"' && substr($attrValue, -1) === '"') ||
                        ($attrValue[0] === "'" && substr($attrValue, -1) === "'")) {
                        $attrValue = substr($attrValue, 1, -1);
                    }
                    
                    // Check if attribute is allowed
                    if (in_array($attrName, $allowedAttributes, true)) {
                        // Additional security checks for specific attributes
                        if ($attrName === 'href' || $attrName === 'src') {
                            // Ensure URLs are safe
                            if (!self::isSafeUrl($attrValue)) {
                                continue;
                            }
                            // Add rel="noopener noreferrer" for external links
                            if ($attrName === 'href' && self::isExternalUrl($attrValue)) {
                                $safeAttributes .= ' rel="noopener noreferrer"';
                            }
                        }
                        
                        // Escape attribute value
                        $escapedValue = htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8');
                        $safeAttributes .= " {$attrName}=\"{$escapedValue}\"";
                    }
                }
            }
            
            return "<{$tag}{$safeAttributes}>";
        }, $html);
        
        return $html;
    }
    
    private static function isSafeUrl(string $url): bool
    {
        // Check for dangerous protocols
        $dangerousProtocols = ['javascript:', 'data:', 'vbscript:', 'file:'];
        
        foreach ($dangerousProtocols as $protocol) {
            if (stripos($url, $protocol) === 0) {
                return false;
            }
        }
        
        return true;
    }
    
    private static function isExternalUrl(string $url): bool
    {
        // Check if URL is external (starts with http:// or https:// and not a relative path)
        return preg_match('/^https?:\/\//', $url) === 1;
    }
}

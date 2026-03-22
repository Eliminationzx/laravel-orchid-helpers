<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class MarkdownSight
{
    public static function make(
        string $name, 
        string $title = null,
        bool $sanitize = true,
        bool $allowHtml = false,
        bool $collapsible = false,
        int $maxHeight = null
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $sanitize, $allowHtml, $collapsible, $maxHeight) : string {
                    $markdown = $repository->get($name);

                    if($markdown === null || $markdown === '') {
                        return '';
                    }

                    $markdown = (string) $markdown;
                    
                    // Convert markdown to HTML
                    $html = self::convertMarkdownToHtml($markdown, $allowHtml);
                    
                    // Sanitize HTML if needed
                    if($sanitize && !$allowHtml) {
                        $html = self::sanitizeHtml($html);
                    }
                    
                    $style = '';
                    if($maxHeight !== null) {
                        $style = "style=\"max-height: {$maxHeight}px; overflow: auto;\"";
                    }
                    
                    $content = <<<HTML
<div class="markdown-content" {$style}>
    {$html}
</div>
HTML;

                    if($collapsible) {
                        $id = 'markdown-' . md5($name . $markdown);
                        return <<<HTML
<div class="markdown-sight-container">
    <button type="button" class="btn btn-sm btn-outline-secondary mb-2" 
            data-bs-toggle="collapse" data-bs-target="#{$id}">
        Show/Hide Content
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

    private static function convertMarkdownToHtml(string $markdown, bool $allowHtml = false): string
    {
        // Simple markdown parser for common elements
        // In a real implementation, you might use a library like parsedown
        $html = $markdown;
        
        // Headers
        $html = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $html);
        
        // Bold and italic
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
        
        // Lists
        $html = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*?<\/li>\n?)+/s', '<ul>$0</ul>', $html);
        
        // Links
        $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $html);
        
        // Code blocks
        $html = preg_replace('/```(\w+)?\n(.*?)\n```/s', '<pre><code class="language-$1">$2</code></pre>', $html);
        $html = preg_replace('/`(.*?)`/', '<code>$1</code>', $html);
        
        // Paragraphs (wrap unformatted text in paragraphs)
        $lines = explode("\n", $html);
        $result = [];
        $inParagraph = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                if ($inParagraph) {
                    $result[] = '</p>';
                    $inParagraph = false;
                }
                $result[] = '';
            } elseif (!preg_match('/^<(h[1-6]|ul|li|pre|code|a|strong|em)/', $trimmed) && 
                      !preg_match('/<\/p>$/', $trimmed)) {
                if (!$inParagraph) {
                    $result[] = '<p>';
                    $inParagraph = true;
                }
                $result[] = $line;
            } else {
                if ($inParagraph) {
                    $result[] = '</p>';
                    $inParagraph = false;
                }
                $result[] = $line;
            }
        }
        
        if ($inParagraph) {
            $result[] = '</p>';
        }
        
        return implode("\n", $result);
    }

    private static function sanitizeHtml(string $html): string
    {
        // Basic HTML sanitization - allow only safe tags
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><b><strong><i><em><u><ul><ol><li><code><pre><a>';
        
        // Strip all tags except allowed ones
        $html = strip_tags($html, $allowedTags);
        
        // Remove potentially dangerous attributes
        $html = preg_replace('/<(\w+)[^>]*>/', '<$1>', $html);
        
        // Allow href attribute for links
        $html = preg_replace_callback('/<a>(.*?)<\/a>/', function($matches) {
            // In a real implementation, you would extract and validate the URL
            return '<a href="#" rel="nofollow noopener noreferrer">' . $matches[1] . '</a>';
        }, $html);
        
        return $html;
    }
}

<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class JsonSight
{
    public static function make(
        string $name, 
        string $title = null,
        int $flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        bool $collapsible = true
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $flags, $collapsible) : string {
                    $data = $repository->get($name);

                    if($data === null) {
                        return '';
                    }

                    // If already a string, try to decode it
                    if(is_string($data)) {
                        $decoded = json_decode($data, true);
                        if(json_last_error() === JSON_ERROR_NONE) {
                            $data = $decoded;
                        }
                    }

                    // Convert to JSON if not already
                    if(!is_string($data)) {
                        $json = json_encode($data, $flags);
                        if($json === false) {
                            return '<code>Invalid JSON data</code>';
                        }
                    } else {
                        $json = $data;
                    }

                    // Escape for HTML
                    $escapedJson = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');
                    
                    if($collapsible) {
                        $id = 'json-' . md5($name . $json);
                        return <<<HTML
<div class="json-container">
    <button type="button" class="btn btn-sm btn-outline-secondary mb-2" 
            data-bs-toggle="collapse" data-bs-target="#{$id}">
        Show/Hide JSON
    </button>
    <div class="collapse" id="{$id}">
        <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow: auto;">
            <code>{$escapedJson}</code>
        </pre>
    </div>
</div>
HTML;
                    }

                    return <<<HTML
<pre class="bg-light p-3 rounded" style="max-height: 300px; overflow: auto;">
    <code>{$escapedJson}</code>
</pre>
HTML;
                }
            );
    }
}

<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Sights;

use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

class CodeSight
{
    public static function make(
        string $name, 
        string $title = null,
        string $language = 'php',
        bool $showLineNumbers = true,
        bool $collapsible = false,
        int $maxHeight = 300
    ) : Sight {
        return Sight::make($name, $title ?? attrName($name))
            ->render(
                static function(Repository $repository) use ($name, $language, $showLineNumbers, $collapsible, $maxHeight) : string {
                    $code = $repository->get($name);

                    if($code === null || $code === '') {
                        return '';
                    }

                    $code = (string) $code;
                    $escapedCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
                    
                    $lineNumbers = '';
                    if($showLineNumbers) {
                        $lines = substr_count($code, "\n") + 1;
                        $lineNumbersHtml = '';
                        for ($i = 1; $i <= $lines; $i++) {
                            $lineNumbersHtml .= "<span class=\"line-number\">{$i}</span>\n";
                        }
                        $lineNumbers = <<<HTML
<div class="line-numbers" style="float: left; text-align: right; padding-right: 10px; border-right: 1px solid #ddd; margin-right: 10px; color: #666; font-family: monospace; user-select: none;">
    {$lineNumbersHtml}
</div>
HTML;
                    }

                    $codeBlock = <<<HTML
<div class="code-container" style="position: relative;">
    {$lineNumbers}
    <pre style="margin: 0; overflow: auto; max-height: {$maxHeight}px; background-color: #f8f9fa; padding: 10px; border-radius: 4px;">
        <code class="language-{$language}">{$escapedCode}</code>
    </pre>
</div>
HTML;

                    if($collapsible) {
                        $id = 'code-' . md5($name . $code);
                        return <<<HTML
<div class="code-sight-container">
    <button type="button" class="btn btn-sm btn-outline-secondary mb-2" 
            data-bs-toggle="collapse" data-bs-target="#{$id}">
        Show/Hide Code
    </button>
    <div class="collapse show" id="{$id}">
        {$codeBlock}
    </div>
</div>
HTML;
                    }

                    return $codeBlock;
                }
            );
    }
}
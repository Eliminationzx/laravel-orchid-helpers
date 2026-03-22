<?php

declare(strict_types=1);

use Illuminate\Support\Str;

if(!function_exists('attrName')) {
    function attrName(string $key, ?string $postfix = null) : ?string
    {
        $transKey  = "validation.attributes.model.$key";
        $attribute = __($transKey);

        if($transKey === $attribute) {
            $attribute = Str::of($key)->replace(['_', '.'], ' ')->title();
        }

        if($postfix) {
            $attribute .= " $postfix";
        }

        return match(true) {
            is_array($attribute) => $key,
            $attribute instanceof \Illuminate\Support\Stringable => $attribute->toString(),
            default => $attribute,
        };
    }
}

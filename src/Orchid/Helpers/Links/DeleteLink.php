<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Helpers\Links;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;

class DeleteLink
{
    public static function make(array $attributes, string $method = 'destroy') : Button
    {
        return Button::make('Delete')
            ->type(Color::DANGER())
            ->confirm('Are you sure you want to delete the current record?')
            ->method($method, $attributes)
            ->icon('bs.trash2');
    }

    public static function makeFromModel(Model $model, array $attributes = []) : Button
    {
        return self::make([
            'morph' => $model->getMorphClass(),
            'id'    => $model->getAttribute('id'),
            ...$attributes,
        ])->can('delete', $model);
    }
}

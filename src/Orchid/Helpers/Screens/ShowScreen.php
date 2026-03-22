<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Helpers\Screens;

use Illuminate\Database\Eloquent\Model;

abstract class ShowScreen extends AbstractScreen
{
    protected Model $model;

    protected function model(Model $model) : iterable
    {
        $this->model = $model;

        return compact('model');
    }
}

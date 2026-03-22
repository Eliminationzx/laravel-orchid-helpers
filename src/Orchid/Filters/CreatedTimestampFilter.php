<?php

declare(strict_types=1);

namespace Orchid\Helpers\Orchid\Filters;

class CreatedTimestampFilter extends TimestampFilter
{
    public function __construct()
    {
        parent::__construct('created_at');
    }
}

<?php

declare(strict_types=1);

namespace OrchidHelpers\Orchid\Filters;

class CreatedTimestampFilter extends TimestampFilter
{
    public function __construct()
    {
        parent::__construct('created_at');
    }
}

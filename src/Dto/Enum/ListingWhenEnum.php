<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Dto\Enum;

enum ListingWhenEnum: string
{
    case NOW = 'NOW';
    case FUTURE = 'FUTURE';
}

<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema\Enum;

enum SubcompartmentTypeEnum: string
{
    case SUBCOMPARTMENT = 'SUBCOMPARTMENT';
    case STAND = 'STAND';
    case SUBZONE = 'SUBZONE';
    case OTHER = 'OTHER';
}

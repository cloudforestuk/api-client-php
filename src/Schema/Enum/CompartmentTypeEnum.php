<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema\Enum;

enum CompartmentTypeEnum: string
{
    case COMPARTMENT = 'COMPARTMENT';
    case PARCEL = 'PARCEL';
    case ZONE = 'ZONE';
    case OTHER = 'OTHER';
}

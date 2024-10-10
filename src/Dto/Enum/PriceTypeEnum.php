<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Dto\Enum;

enum PriceTypeEnum: string
{
    case NONE = 'NONE';
    case OFFERS = 'OFFERS';
    case EXACT = 'EXACT';
}

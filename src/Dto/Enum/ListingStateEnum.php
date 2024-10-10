<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Dto\Enum;

enum ListingStateEnum: string
{
    case DRAFT = 'DRAFT';
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
}

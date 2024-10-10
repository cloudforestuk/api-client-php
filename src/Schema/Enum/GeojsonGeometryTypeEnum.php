<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema\Enum;

enum GeojsonGeometryTypeEnum: string
{
    case POLYGON = 'Polygon';
    case POINT = 'Point';
}

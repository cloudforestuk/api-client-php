<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema\Enum;

enum GeojsonGeometryTypeEnum: string
{
    // Lower case values to match GeoJSON spec
    case Polygon = 'Polygon';
    case Point = 'Point';
}

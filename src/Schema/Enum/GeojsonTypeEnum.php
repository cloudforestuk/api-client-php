<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema\Enum;

enum GeojsonTypeEnum: string
{
    // Lower case value to match GeoJSON spec
    case Feature = 'Feature';
}

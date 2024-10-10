<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\Enum\GeojsonGeometryTypeEnum;

/**
 * GeojsonGeometrySchema defines a geometry for use with GeojsonSchema.
 *
 * @package CloudForest\Schema
 */
class GeojsonGeometrySchema
{
    /**
     * The geometry type. We only send Points and Polygons to CloudForest.
     *
     * @var GeojsonGeometryTypeEnum
     */
    public $type;

    /**
     * The coordinates, as an array of [Longitude, Latitude] tuples.
     *
     * @var array<array{float,float}>
     */
    public $coordinates = [];

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param GeojsonGeometryTypeEnum $type
     * @return void
     */
    public function __construct(GeojsonGeometryTypeEnum $type)
    {
        $this->type = $type;
    }
}

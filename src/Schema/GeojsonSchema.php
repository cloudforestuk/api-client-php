<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\GeojsonGeometrySchema;
use CloudForest\ApiClientPhp\Schema\Enum\GeojsonTypeEnum;
use CloudForest\ApiClientPhp\Schema\Enum\GeojsonGeometryTypeEnum;

/**
 * GeojsonSchema defines the shape of a Geojson geographic data structure to
 * send to the CloudForest API.
 *
 * @package CloudForest\Schema
 */
class GeojsonSchema
{
    /**
     * The Geojson type. We only send Features types to CloudForest, not
     * FeatureCollection types, so this is hardcoded to 'Feature'.
     *
     * @var GeojsonTypeEnum
     */
    public $type = GeojsonTypeEnum::Feature;

    /**
     * The Geojson geometry.
     *
     * @var GeojsonGeometrySchema
     */
    public $geometry;

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param GeojsonGeometryTypeEnum $geometryType
     * @return void
     */
    public function __construct(GeojsonGeometryTypeEnum $geometryType)
    {
        $this->geometry = new GeojsonGeometrySchema($geometryType);
    }
}

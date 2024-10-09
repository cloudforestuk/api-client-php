<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\GeojsonGeometrySchema;

enum GeojsonType: string
{
    case FEATURE = 'Feature';
}

/**
 * GeojsonSchema defines the shape of a Geojson geographic data structure to
 * send to the CloudForest Api.
 *
 * @package CloudForest\Schema
 */
class GeojsonSchema
{
    /**
     * The Geojson type. We only send Features types to CloudForest, not
     * FeatureCollection types, so this is hardcoded to 'Feature'.
     *
     * @var value-of<GeojsonType>
     */
    public $type = 'Feature';

    /**
     * The Geojson geometry.
     *
     * @var GeojsonGeometrySchema
     */
    public $geometry;

    /**
     * Constructor. Instantiate classes where required.
     * @return void
     */
    public function __construct()
    {
        $this->geometry = new GeojsonGeometrySchema();
    }
}

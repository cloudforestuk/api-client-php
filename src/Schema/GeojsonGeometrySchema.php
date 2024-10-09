<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

enum GeojsonGeometryType: string
{
    case POLYGON = 'Polygon';
    case POINT = 'Point';
}

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
     * @var value-of<GeojsonGeometryType>
     */
    public $type = 'Point';

    /**
     * The coordinates, as an array of [Longitude, Latitude] tuples.
     *
     * @var array<array{float,float}>
     */
    public $coordinates = [];
}

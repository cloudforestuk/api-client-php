<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\GeojsonSchema;
use CloudForest\ApiClientPhp\Schema\SubcompartmentSchema;
use CloudForest\ApiClientPhp\Schema\Enum\CompartmentTypeEnum;
use CloudForest\ApiClientPhp\Schema\Enum\GeojsonGeometryTypeEnum;

/**
 * CompartmentSchema defines the shape of the compartment data used to send an
 * inventory to the CloudForest Api.
 *
 * @package CloudForest\Schema
 */
class CompartmentSchema
{
    /**
     * The ID of the compartment. If known, it is a UUID string, else null.
     *
     * @var string|null
     */
    public $id = null;

    /**
     * The type of the compartment. This allows the logic unit 'compartment' to
     * also represent other physical units like parcels and zones, if necessary.
     *
     * @var CompartmentTypeEnum
     */
    public $type = CompartmentTypeEnum::COMPARTMENT;

    /**
     * The name of the compartment, or null if not known.
     *
     * @var string|null
     */
    public $name = null;

    /**
     * The number of the compartment. This is a string representation of an
     * integer and cannot be null.
     *
     * @var string
     */
    public $number;

    /**
     * Any notes about the compartment, or null if none.
     *
     * @var string|null
     */
    public $notes = null;

    /**
     * The boundary of the compartment as a Geojson polygon.
     *
     * @var GeojsonSchema
     */
    public $boundary;

    /**
     * The centroid of the compartment as a Geojson point.
     *
     * @var GeojsonSchema
     */
    public $centroid;

    /**
     * The collection of Subcompartments within this Compartment.
     *
     * @var Array<SubcompartmentSchema>
     */
    public $subcompartments = [];

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param string $number
     * @return void
     */
    public function __construct(string $number)
    {
        if (mb_strlen($number) < 1) {
            throw new \Exception('Compartment number cannot be less than 1 character');
        }

        $this->number = $number;
        $this->boundary = new GeojsonSchema(GeojsonGeometryTypeEnum::POLYGON);
        $this->centroid = new GeojsonSchema(GeojsonGeometryTypeEnum::POINT);
    }
}

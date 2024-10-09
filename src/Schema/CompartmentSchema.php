<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\GeojsonSchema;
use CloudForest\ApiClientPhp\Schema\SubcompartmentSchema;

enum CompartmentType: string
{
    case COMPARTMENT = 'COMPARTMENT';
    case PARCEL = 'PARCEL';
    case ZONE = 'ZONE';
    case OTHER = 'OTHER';
}

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
     * @var value-of<CompartmentType>
     */
    public $type = 'COMPARTMENT';

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
    public $number = '1';

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
     * Constructor. Instantiate classes where required.
     * @return void
     */
    public function __construct()
    {
        $this->boundary = new GeojsonSchema();
        $this->centroid = new GeojsonSchema();
    }
}

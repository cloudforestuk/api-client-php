<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use CloudForest\ApiClientPhp\Schema\GeojsonSchema;
use CloudForest\ApiClientPhp\Schema\InventorySchema;
use CloudForest\ApiClientPhp\Schema\Enum\SubcompartmentTypeEnum;
use CloudForest\ApiClientPhp\Schema\Enum\GeojsonGeometryTypeEnum;

/**
 * SubcompartmentSchema defines the shape of the subcompartment data used to
 * send an inventory to the CloudForest API.
 *
 * @package CloudForest\Schema
 */
class SubcompartmentSchema
{
    /**
     * The ID of the subcompartment. If known, it is a UUID string, else null.
     *
     * @var string|null
     */
    public $id = null;

    /**
     * The type of the subcompartment. This allows the logic unit
     * 'subcompartment' to also represent other physical units like stands and
     * subzones, if necessary.
     *
     * @var SubcompartmentTypeEnum
     */
    public $type = SubcompartmentTypeEnum::SUBCOMPARTMENT;

    /**
     * The name of the subcompartment, or null if not known.
     *
     * @var string|null
     */
    public $name = null;

    /**
     * The letter of the subcompartment. This is a string representation of an
     * single letter and cannot be null.
     *
     * @var string
     */
    public $letter;

    /**
     * Any notes about the subcompartment, or null if none.
     *
     * @var string|null
     */
    public $notes = null;

    /**
     * The boundary of the subcompartment as a Geojson polygon.
     *
     * @var GeojsonSchema
     */
    public $boundary;

    /**
     * The centroid of the subcompartment as a Geojson point.
     *
     * @var GeojsonSchema
     */
    public $centroid;

    /**
     * The area of thesubcompartment as a number representing hectatres, or null
     * if not known.
     *
     * @var float|null
     */
    public $area = null;

    /**
     * The inventories that have been taken for this Subcompartment. The field
     * spelling 'inventorys' is to keep a consistent pattern of plurals
     * thoughout this schema.
     *
     * @var array<InventorySchema>
     */
    public $inventorys = [];

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param string $letter
     * @return void
     * @throws \Exception
     */
    public function __construct(string $letter)
    {
        if (mb_strlen($letter) < 1) {
            throw new \Exception('Subcompartment letter cannot be less than 1 character');
        }

        $this->letter = $letter;
        $this->boundary = new GeojsonSchema(GeojsonGeometryTypeEnum::Polygon);
        $this->centroid = new GeojsonSchema(GeojsonGeometryTypeEnum::Point);
    }
}

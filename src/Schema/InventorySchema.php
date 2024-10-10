<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

/**
 * InventorySchema defines the shape of the inventory data used to send an
 * inventory to the CloudForest API.
 *
 * @package CloudForest\Schema
 */
class InventorySchema
{
    /**
     * The ID of the inventory. If known, it is a UUID string, else null.
     *
     * @var string|null
     */
    public $id = null;

    /**
     * Any notes about the inventory, or null if none.
     *
     * @var string|null
     */
    public $notes = null;

    /**
     * The year the inventory was taken as a 'YYYY' string. Cannot be null
     *
     * @var string
     */
    public $year;

    /**
     * The total volume of this inventory (and hence its parent subcompartment)
     * as a number representing m^3, or null if not known.
     *
     * @var float|null
     */
    public $volumeTotal = null;

    /**
     * The total base area of this inventory (and hence its parent
     * subcompartment) as a number representing m^2, or null if not known.
     *
     * @var float|null
     */
    public $basalAreaTotal = null;

    /**
     * The list of stratums recorded in this inventory. The spelling 'stratums'
     * is to keep a consistent pattern of plurals thoughout this schema.
     *
     * @var Array<StratumSchema>
     */
    public $stratums = [];

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param string $year
     * @return void
     * @throws \Exception
     */
    public function __construct($year)
    {
        if (mb_strlen($year) < 4) {
            throw new \Exception('Inventory year cannot be less than 4 characters');
        }

        if (mb_strlen($year) > 4) {
            throw new \Exception('Inventory year cannot be more than 4 characters');
        }

        $this->year = $year;
    }
}

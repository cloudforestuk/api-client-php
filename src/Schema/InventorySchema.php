<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

/**
 * InventorySchema defines the shape of the inventory data used to send an
 * inventory to the CloudForest Api.
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
    public $year = '2024';

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

    // @todo Stratums
}

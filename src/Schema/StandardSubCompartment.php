<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardSubCompartment
{
    /**
     * ID (guuid)
     * @var string
     */
    public $id;

    /**
     * notes
     * @var string;
     */
    public $notes = '';

    /**
     * name
     * @var string;
     */
    public $name = '';

    /**
     * boundary
     * @var string|null
     */
    public $boundary;

    /**
     * area in hectares
     * @var float|null
     */
    public $area_ha;

    /**
     * inventory array (may be for multiple years or seasons, so use array)
     * @var  StandardInventory[]
     */
    public $inventory = [];

    /**
     * array of ground truth plots
     * @var  StandardPlot[]
     */
    public $plots = [];

    /**
     * @see    Create the SubCompartment.
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * creates an inventory from the plot data
     * TODO
     * @return void
     */
    public function createSpeciesDetailsFromPlots(): void
    {
        return;
    }
}

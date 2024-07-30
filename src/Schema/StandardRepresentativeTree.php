<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardRepresentativeTree
{
    /**
     * tree details
     * @var StandardTree
     */
    public $treeDetails;

    /**
     * distribution stats of this tree
     * @var StandardTreeDistributionStats
     */
    public $distributionStats;

    /**
     * trees per hectare
     * @var float
     */
    public $treesPerHa;


    /**
     * Volume per hectare
     * @var float
     */
    public $volumePerHa;


    /**
     * @see    Create the StandardRepresentativeTree.
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Calculate the volume per hectare
     * @return float
     */
    public function calcVolumePerHa(): float
    {
        $this->volumePerHa =  $this->treeDetails->volume * $this->treesPerHa;
        return $this->volumePerHa;
    }

}

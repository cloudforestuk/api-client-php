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
     * @see    Create the API client and its modules.
     * @param  StandardTree $treeDetails     The tree details.
     * @param  StandardTreeDistributionStats $distributionStats The API secret issued by CloudForest.
     * @param  float $treesPerHa   The number of trees per hectare of this species.
     * @return void
     */
    public function __construct(StandardTree $treeDetails, StandardTreeDistributionStats $distributionStats, float $treesPerHa)
    {
        $this->treeDetails = $treeDetails;
        $this->distributionStats = $distributionStats;
        $this->treesPerHa = $treesPerHa;
        $this->volumePerHa = $this->calcVolumePerHa();
    }

    /**
     * Calculate the volume per hectare
     * @return float
     */
    public function calcVolumePerHa(): float
    {
        return $this->treeDetails->volume * $this->treesPerHa;
    }

}

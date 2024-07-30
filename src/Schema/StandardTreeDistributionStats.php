<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardTreeDistributionStats
{
    /**
     * height range
     * @var float|null
     */
    public $heightRange;

    /**
     * dbh range
     * @var float|null
     */
    public $dbhRange;

    /**
     * height variance
     * @var float|null
     */
    public $heightVariance;

    /**
     * dbh variance
     * @var float|null
     */
    public $dbhVariance;

    /**
     * @see    Create the Tree distribution statistics.
     * @return void
     */
    public function __construct()
    {
    }

}

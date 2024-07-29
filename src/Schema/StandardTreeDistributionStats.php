<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardTreeDistributionStats
{
    /**
     * height range
     * @var float
     */
    public $heightRange;

    /**
     * dbh range
     * @var float
     */
    public $dbhRange;

    /**
     * height variance
     * @var float
     */
    public $heightVariance;

    /**
     * dbh variance
     * @var float
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

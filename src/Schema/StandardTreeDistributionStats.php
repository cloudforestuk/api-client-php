<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardTreeDistributionStats
{
    /**
     * The height range of the StandardTree expressed as a 2-tuple of floats
     * in the order [min,max]
     *
     * @var array{float,float}
     */
    public $heightRange_m;

    /**
     * The dbh range of the StandardTree expressed as a 2-tuple of floats
     * in the order [min,max]
     *
     * @var array{float,float}
     */
    public $dbhRange_cm;

    /**
     * The height variance. This is the variance of the mean defined in
     * the linked StandardTree.
     * @var float|null
     */
    public $heightVariance;

    /**
     * The dbh variance. This is the variance of the mean defined in
     * the linked StandardTree.
     * @var float|null
     */
    public $dbhVariance;

    /**
     * Create the Tree distribution statistics.
     *
     * @param array{float,float} $heightRange The Tree distribution height range.
     * @param array{float,float} $dbhRange    The Tree distribution dbh range.
     * @return void
     */
    public function __construct(array $heightRange, array $dbhRange)
    {
        if (count($heightRange) != 2) {
            throw new \Exception("heightRange must be an array of two floats");
        }
        $this->heightRange_m = $heightRange;

        if (count($dbhRange) != 2) {
            throw new \Exception("heightRange must be an array of two floats");
        }
        $this->dbhRange_cm = $dbhRange;
    }

}

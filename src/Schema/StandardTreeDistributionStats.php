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
    public $heightRange;

    /**
     * The dbh range of the StandardTree expressed as a 2-tuple of floats
     * in the order [min,max]
     *
     * @var array{float,float}
     * @var float
     */
    public $dbhRange;

    /**
     * The height variance. This is the variance of the mean defined in
     * the linked StandardTree.
     * @var float
     */
    public $heightVariance;

    /**
     * The dbh variance. This is the variance of the mean defined in
     * the linked StandardTree.
     * @var float
     */
    public $dbhVariance;

    /**
     * Create the Tree distribution statistics.
     *
     * @param  array $heightRange     The Tree distribution height range.
     * @param  array $dbhRange        The Tree distribution dbh range.
     * @return void
     */
    public function __construct(array $heightRange, array $dbhRange)
    {
        if (count($heightRange) !== 2) {
            throw new \Exception("heightRange must be an array of two floats");
        }
        $this->heightRange = $heightRange;

        if (count($dbhRange) !== 2) {
            throw new \Exception("heightRange must be an array of two floats");
        }
        $this->dbhRange = $dbhRange;
    }

}

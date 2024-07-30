<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardTree
{
    /**
     * species ID
     * @todo create common list
     * @todo How about the 3-letter codes? Then we do not need to rely on IDs
     * into a common list.
     * @var int
     */
    public $speciesId;

    /**
     * Height of this tree in metres.
     *
     * If this is a representative tree, this is the mean height of the representative tree.
     * @var float
     */
    public $height;

    /**
     * dbh of this tree in metres.
     *
     * If this is a representative tree, this is the mean dbh of the representative tree.
     * @var float
     */
    public $dbh;

    /**
     * volume
     * @var float
     */
    public $volume;

    /**
     * volume calculation method: eg blue book look up
     * @var string
     */
    public $volumeCalculationMethod;

    /**
     * Create the Standard Tree.
     * @param  int $speciesId     The species ID.
     * @return void
     */
    public function __construct(int $speciesId)
    {
        $this->speciesId = $speciesId;
    }
}

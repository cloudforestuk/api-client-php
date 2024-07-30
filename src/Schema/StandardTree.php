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
<<<<<<< HEAD
     * height of this tree in m
     * if this is a representative tree, thisshould correspond to the mean height
     * @var float
     */
    public $height_m;
    /**
     * dbh of this tree in cm
     * if this is a representative tree, thisshould correspond to the mean dbh
=======
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
>>>>>>> main
     * @var float
     */
    public $dbh_cm;

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
<<<<<<< HEAD
     * @see    Create the Tree Species.
=======
     * Create the Standard Tree.
     * @param  int $speciesId     The species ID.
>>>>>>> main
     * @return void
     */
    public function __construct()
    {
    }
}

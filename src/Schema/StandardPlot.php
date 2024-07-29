<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardPlot
{
    /**
     * id
     * @var string
     */
    public $id = '';
    /**
     * notes
     * @var string
     */
    public $notes = '';
    /**
     * name
     * @var string
     */
    public $name = '';

    /**
     * TODO
     * @var string
     */
    public $centroid;

    /**
     * area
     * @var float
     */
    public $area;

    /**
     * shape
     * @var string
     */
    public $shape;

    /**
     * real trees in this plot
     * @var StandardTree[]
     */
    public $standingTrees = [];

    /**
     * @see    Create the Plot.
     * @return void
     */
    public function __construct()
    {
    }
}

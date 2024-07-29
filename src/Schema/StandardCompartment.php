<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardCompartment
{
    /**
     * ID (guuid)
     * @var string
     */
    public $id;

    /**
     * name
     * @var string;
     */
    public $name = '';

    /**
     * notes
     * @var string;
     */
    public $notes = '';

    /**
     * TODO
     * @var string
     */
    public $boundary;

    /**
     * TODO
     * @var string
     */
    public $centroidcentroid;

    /**
     * subcompartment array
     * @var StandardSubCompartment[]
     */
    public $subCompartments = [];

    /**
     * @see    Create the Compartment.
     * @return void
     */
    public function __construct()
    {
    }
}

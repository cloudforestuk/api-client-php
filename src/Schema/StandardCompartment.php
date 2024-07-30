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
     * boundary
     * @var string|null
     */
    public $boundary;

    /**
     * area in ha
     * @var float|null
     */
    public $area_ha;

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

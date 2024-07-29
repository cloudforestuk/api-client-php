<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

class StandardInventory
{
    /**
     * notes
     * @var string
     */
    public $notes = '';

    /**
     * date
     * @var string
     */
    public $date;

    //single species inventory

    /**
     * method: manual, calculated (blue book), arboreal
     * @var string
     */
    public $method;

    /**
     * species details array
     * @var StandardRepresentativeTree[]
     */
    public $speciesDetails = [];

    /**
     * @see    Create the Inventory.
     * @return void
     */
    public function __construct()
    {
    }

}

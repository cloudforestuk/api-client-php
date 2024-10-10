<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Schema;

use Exception;

/**
 * StratumSchema defines the shape of the stratum data used to send an
 * inventory to the CloudForest Api.
 *
 * @package CloudForest\Schema
 */
class StratumSchema
{
    /**
     * The ID of the stratum. If known, it is a UUID string, else null.
     *
     * @var string|null
     */
    public $id = null;

    /**
     * Any notes about the stratum, or null if none.
     *
     * @var string|null
     */
    public $notes = null;

    /**
     * The species of this stratum as a max-3-letter code. Cannot be null. A
     * stratum is unique on the tuple (species, planting year).
     *
     * Ref myForest's DB and
     * https://cdn.forestresearch.gov.uk/2022/02/pf2011_tree_species.pdf
     *
     *
     *
     * @var string
     */
    public $species;

    /**
     * The planting year of the stratum as a 'YYYY' string. Cannot be null. A
     * stratum is unique on the tuple (species, planting year).
     *
     * @var string
     */
    public $plantingYear;

    /**
     * The mean height of the trees in the stratum in meters, or null if not
     * known.
     *
     * @var float|null
     */
    public $heightMean = null;

    /**
     * The variance of the height of the trees in the stratum in meters, or null
     * if not known.
     *
     * @var float|null
     */
    public $heightVariance = null;

    /**
     * The range of the height of the trees in the stratum in meters as a pair
     * [min, max], or an empty array if not known.
     *
     * @var array{float,float}|array{}
     */
    public $heightRange = [];

    /**
     * The mean DHB of the trees in the stratum in meters, or null if not
     * known.
     *
     * @var float|null
     */
    public $dhbMean = null;

    /**
     * The variance of the DHB of the trees in the stratum in meters, or null
     * if not known.
     *
     * @var float|null
     */
    public $dhbVariance = null;

    /**
     * The range of the DHB of the trees in the stratum in meters as a pair
     * [min, max], or an empty array if not known.
     *
     * @var array{float,float}|array{}
     */
    public $dhbRange = [];

    /**
     * The density of the stratum as a number measuring trees per hectare.
     *
     * @var float|null
     */
    public $density = null;

    /**
     * The mean volume of a tree within the stratum, as a float in m^3, or null
     * if not known.
     *
     * @var float|null
     */
    public $treeVolumeMean = null;

    /**
     * The volume of the stratum within its enclosing subcompartment, as a float
     * in m^3, or null if not known.
     *
     * @var float|null
     */
    public $volumePerSubcompartment;

    /**
     * The volume of the stratum per hectare, as a float in m^3, or null if not
     * known.
     *
     * @var float|null
     */
    public $volumePerHa;

    /**
     * The basal area of the stratum within its enclosing subcompartment, as a
     * float in m^2, or null if not known.
     *
     * @var float|null
     */
    public $basalAreaPerSubcompartment;

    /**
     * The basalArea of the stratum per hectare, as a float in m^2, or null if
     * not known.
     *
     * @var float|null
     */
    public $basalAreaPerHa;

    /**
     * Constructor. Supply the required properties (those without defaults).
     * @param string $species
     * @param string $plantingYear
     * @return void
     * @throws Exception
     */
    public function __construct($species, $plantingYear)
    {
        if (mb_strlen($species) < 2) {
            throw new \Exception('Stratum species cannot be less than 2 characters');
        }

        if (mb_strlen($species) > 3) {
            throw new \Exception('Stratum species cannot be more than 3 characters');
        }

        if (mb_strlen($plantingYear) < 4) {
            throw new \Exception('Stratum plantingYear cannot be less than 4 characters');
        }

        if (mb_strlen($plantingYear) > 4) {
            throw new \Exception('Stratum plantingYear cannot be more than 4 characters');
        }

        $this->species = $species;
        $this->plantingYear = $plantingYear;
    }
}

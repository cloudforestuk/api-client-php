<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Dto;

use CloudForest\ApiClientPhp\Schema\StandardCompartment;

enum ListingState: string
{
    case DRAFT = 'DRAFT';
    case OPEN = 'OPEN';
    case CLOSED = 'CLOSED';
}

/**
 * ListingDto defines the shape of the listing data used by the CloudForest API.
 *
 * @package CloudForest
 */
class ListingDto
{
    /**
     * The primary key of the listing as a UUID. Use an empty string to create
     * a new one.
     *
     * @var string
     **/
    public $id = '';

    /**
     * The state of the listing.
     * DRAFT = Only visible to owner, cannot be used in transations.
     * OPEN = Visible to the public
     * CLOSED = Only visible to owner, still usable in transactions.
     * Currently DRAFT listings are not fully supported so only create listings
     * in an OPEN state.
     *
     * @var value-of<ListingState>
     */
    public $state = 'OPEN';

    /**
     * The title of the listing.
     *
     * @var string
     */
    public $title = '';

    /**
     * A description of the listing.
     *
     * @var string
     */
    public $description = 'A testing listing created from the CloudForest PHP ApiClient';

    /**
     * The units in which the listing is available as a freeform string. EG
     * "tonnes", "m^3".
     *
     * @var string
     */
    public $units = 'tonnes';

    /**
     * The amount of stock of the units as an integer. EG if units is m^3 and
     * there are 10 m^3 for sale then the stock is 10.
     *
     * @var int
     */
    public $stock = 1;

    /**
     * The latitude of a point representing the location of the listing. This
     * does not necessarily have to be exact, eg you could protect a valuable
     * resource by supplying the office handling the sale. It is passed
     * as a float representing decimal degrees, eg 53.12
     *
     * @var float
     */
    public $latitude = 53.12;

    /**
     * The longitude of a point representing the location of the listing.
     *
     * @see $latitude
     * @var float
     */
    public $longitude = -2.1;

    /**
     * Does this listing require a felling licence?
     *
     * @var bool
     */
    public $licenceRequired = false;

    /**
     * If a licence is required or if a licence exists then the number can be
     * supplied here as a string, on the assumption the 'number' will contain
     * characters.
     *
     * @var string
     */
    public $licenceNumber = '';

    /**
     * The inventory info for this listing in JSON format. Use this to break out
     * the listing into individual components. This allows a single listing to
     * represent a more complex structure. For example:
     * - A decking installation package (decking planks, uprights, joists and a
     *   fixing kit)
     * - A compartment of standing timber in a forest
     * For Standing Timber this is likely to be the StandardCompartment
     * specification supplied as a list. For other categories alternative data
     * structures can be used.
     * @var array<StandardCompartment>
     */
    public $inventory = [];

    /**
     * The categories the listing should appear within. In theory this supports
     * multiple categories. In practice you should set just one for now. Each
     * category is an integer ID into the CloudForest category list. Currently
     * this is not available through the ApiClient. For now, use 18 for
     * 'Standing Timber' or ask CloudForest for alternative IDs if you need a
     * different category.
     *
     * @todo Expose categories through Api to ApiClient for reuse
     * @var  array<int>
     */
    public $categories = [18];

    /**
     * The facets that describe the listing. For example, species=oak. Each
     * facet is a UUID string representing the ID of a facet created through the
     * API for this listing. This is not yet supported through the ApiClient.
     * The flow would be:
     * 1. Create a facet (eg species=oak)
     * 2. Get back a facet DTO with a UUID
     * 3. Add the UUID to this list
     *
     * @todo Implement facet support in the ApiClient
     * @var  array<string>
     */
    public $facets = [];

    /**
     * The images uploaded for the listing. Each image is a UUID string
     * representing the ID of an image uploaded through the API for this
     * listing. This is not yet supported through the ApiClient. The flow would
     * be:
     * 1. Upload an image
     * 2. Get back a image DTO with a UUID
     * 3. Add the UUID to this list
     *
     * @todo Implement image support in the ApiClient
     * @var  array<string>
     */
    public $images = [];

    /**
     * The id of the user who owns the listing as a UUID. This will be set by
     * the ApiClient from the JWT access token.
     *
     * @var string
     */
    public $userId = '';

    /**
     * The id of the company of the user who owns the listing as a UUID. This
     * will be set by the ApiClient from the JWT access token.
     *
     * @var string
     */
    public $companyId = '';

    public function __construct()
    {
        $this->title = 'Test Listing ' . date("Y-m-d H:i:s");
    }
}

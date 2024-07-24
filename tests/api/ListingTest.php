<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\TestBase;
use CloudForest\ApiClientPhp\Dto\ListingDto;
use CloudForest\ApiClientPhp\Schema\StandardCompartment;
use CloudForest\ApiClientPhp\Schema\StandardSubCompartment;

final class ListingTest extends TestBase
{
    public function testCreate(): void
    {
        // This is a short cut for now - log in directly using the stored user/pass
        // Eventually this should use an access token obtained from the OAuth exchange
        $access = $this->login();
        $this->assertIsString($access);
        $this->assertNotEmpty($access);

        $api = $this->getCloudForestClient();
        $api->setAccess($access);

        $compartment = new StandardCompartment('1', 'PHPUnit Forest', '', '', '', []);
        $subcompartment = new StandardSubCompartment('2');
        $subcompartment->name = 'PHPUnit Forest SubComp 1A';
        $compartment->subCompartments = [$subcompartment];

        $listing = new ListingDto();
        $listing->title = 'Test Listing from PHP Unit';
        $listing->description = 'This is a test Listing from PHP Unit';
        $listing->inventory = [$compartment];

        $listingUuid = $api->listing->create($listing);
        $this->assertIsString($listingUuid);
        $this->assertNotEmpty($listingUuid);

        $newListing = $api->listing->findOne($listingUuid);
        $this->assertEquals('Test Listing from PHP Unit', $newListing['title']);
        $this->assertIsArray($newListing['inventory']);
        $inventory = $newListing['inventory'];
        $this->assertCount(1, $inventory);
        $compartment = $inventory[0];
        $this->assertEquals('PHPUnit Forest', $compartment['name']);
        $this->assertIsArray($compartment['subCompartments']);
        $this->assertCount(1, $compartment['subCompartments']);
        $subcompartment = $compartment['subCompartments'][0];
        $this->assertEquals('PHPUnit Forest SubComp 1A', $subcompartment['name']);
    }
}

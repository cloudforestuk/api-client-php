<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\TestBase;
use CloudForest\ApiClientPhp\Dto\ListingDto;
use CloudForest\ApiClientPhp\Schema\CompartmentSchema;
use CloudForest\ApiClientPhp\Schema\GeojsonSchema;
use CloudForest\ApiClientPhp\Schema\InventorySchema;
use CloudForest\ApiClientPhp\Schema\StratumSchema;
use CloudForest\ApiClientPhp\Schema\SubcompartmentSchema;
use CloudForest\ApiClientPhp\Schema\Enum\CompartmentTypeEnum;
use CloudForest\ApiClientPhp\Schema\Enum\GeojsonGeometryTypeEnum;
use CloudForest\ApiClientPhp\Schema\Enum\SubcompartmentTypeEnum;

final class ListingTest extends TestBase
{
    public function testCreate(): void
    {
        // This is a short cut for now - log in directly using the stored user/pass
        // Eventually this should use an access token obtained from the OAuth exchange
        $access = $this->login();
        $this->assertIsString($access);
        $this->assertNotEmpty($access);

        // Get the API client and set the access token from above.
        $api = $this->getCloudForestClient();
        $api->setAccess($access);

        // Create an inventory structure:
        // Inventory 1, create a boundary Geojson
        $boundary = new GeojsonSchema(GeojsonGeometryTypeEnum::POLYGON);
        $boundary->geometry->coordinates = [[-1, 53.2], [-1.1, 53.3]];

        // Inventory 2, create a centroid Geojson
        $centroid = new GeojsonSchema(GeojsonGeometryTypeEnum::POINT);
        $centroid->geometry->coordinates = [-1.05, 53.25];

        // Inventory 3, create a compartment
        $compartment = new CompartmentSchema('2');
        $compartment->id = null;
        $compartment->type = CompartmentTypeEnum::COMPARTMENT;
        $compartment->name = 'PHPUnit Forest Compartment';
        $compartment->notes = 'These are the compartment notes';
        $compartment->boundary = $boundary;
        $compartment->centroid = $centroid;

        // Inventory 4, create a subcompartment
        $subcompartment = new SubcompartmentSchema('B');
        $subcompartment->id = null;
        $subcompartment->type = SubcompartmentTypeEnum::SUBCOMPARTMENT;
        $subcompartment->name = 'PHPUnit Forest Subcompartment';
        $subcompartment->notes = 'These are the subcompartment notes';
        $subcompartment->boundary = $boundary;
        $subcompartment->centroid = $centroid;
        $subcompartment->area = 12.36;

        // Inventory 5, create an inventory record for the subcompartment
        $inventory = new InventorySchema('1999');
        $inventory->id = null;
        $inventory->notes = 'These are the inventory notes';
        $inventory->volumeTotal = 1234.5;
        $inventory->basalAreaTotal = 2000.1;

        // Inventory 6, create a stratum for the inventory
        $stratum = new StratumSchema('CAR', '1976');
        $stratum->id = null;
        $stratum->volumePerSubcompartment = 1267.13;

        // Inventory 7, create the structure
        $inventory->stratums = [$stratum];
        $subcompartment->inventorys = [$inventory];
        $compartment->subcompartments = [$subcompartment];

        // Create a listing and attach the inventory
        $listing = new ListingDto();
        $listing->title = 'Test Listing from PHP Unit';
        $listing->description = 'This is a test Listing from PHP Unit';
        $listing->inventory = [$compartment];

        // Use the API to create the listing in CloudForest
        $listingUuid = $api->listing->create($listing);
        $this->assertIsString($listingUuid);
        $this->assertNotEmpty($listingUuid);

        // Fetch the listing from CloudForest using its UUID only
        $newListing = $api->listing->findOne($listingUuid);

        // Verify the returned listing has the same data as above.
        $this->assertEquals('Test Listing from PHP Unit', $newListing['title']);

        // Verify the returned listing's compartment has the same data as above.
        $this->assertIsArray($newListing['inventory']);
        $inventory = $newListing['inventory'];
        $this->assertCount(1, $inventory);
        $compartment = $inventory[0];
        $this->assertEquals('PHPUnit Forest Compartment', $compartment['name']);

        // Verify the returned listing's subcompartment has the same data as above.
        $this->assertIsArray($compartment['subcompartments']);
        $this->assertCount(1, $compartment['subcompartments']);
        $subcompartment = $compartment['subcompartments'][0];
        $this->assertEquals('PHPUnit Forest Subcompartment', $subcompartment['name']);
        $this->assertEquals('B', $subcompartment['letter']);

        // Verify the returned listing's inventory has the same data as above.
        $this->assertIsArray($subcompartment['inventorys']);
        $this->assertCount(1, $subcompartment['inventorys']);
        $inventory = $subcompartment['inventorys'][0];
        $this->assertEquals('These are the inventory notes', $inventory['notes']);
        $this->assertEquals('1999', $inventory['year']);
        $this->assertEquals('2000.1', $inventory['basalAreaTotal']);

        // Verify the returned listing's stratum has the same data as above.
        $this->assertIsArray($inventory['stratums']);
        $this->assertCount(1, $inventory['stratums']);
        $inventory = $inventory['stratums'][0];
        $this->assertEquals('CAR', $inventory['species']);
        $this->assertEquals('1976', $inventory['plantingYear']);
        $this->assertEquals('1267.13', $inventory['volumePerSubcompartment']);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\TestBase;
use CloudForest\ApiClientPhp\Dto\ListingDto;
use CloudForest\ApiClientPhp\Schema\CompartmentSchema;
use CloudForest\ApiClientPhp\Schema\CompartmentType;
use CloudForest\ApiClientPhp\Schema\GeojsonGeometrySchema;
use CloudForest\ApiClientPhp\Schema\GeojsonGeometryType;
use CloudForest\ApiClientPhp\Schema\GeojsonSchema;

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
        $boundaryGeometry = new GeojsonGeometrySchema();
        $boundaryGeometry->type = GeojsonGeometryType::POLYGON;
        $boundaryGeometry->coordinates = [
            [-1, 53.2], [-1.1, 53.3],
        ];
        $boundary = new GeojsonSchema();
        $boundary->geometry = $boundaryGeometry;

        // Inventory 2, create a centroid Geojson
        $centroidGeometry = new GeojsonGeometrySchema();
        $centroidGeometry->type = GeojsonGeometryType::POINT;
        $centroidGeometry->coordinates = [-1.05, 53.25];
        $centroid = new GeojsonSchema();
        $centroid->geometry = $boundaryGeometry;

        // Inventory 3, create a compartment
        $compartment = new CompartmentSchema();
        $compartment->id = null;
        $compartment->type = CompartmentType::COMPARTMENT;
        $compartment->name = 'PHPUnit Forest Compartment';
        $compartment->number = '1';
        $compartment->notes = 'These are the notes';
        $compartment->boundary = $boundary;
        $compartment->centroid = $centroid;

        // @todo: Subcompartments...
        //$subcompartment = new StandardSubCompartment('2');
        //$subcompartment->name = 'PHPUnit Forest SubComp 1A';
        //$compartment->subCompartments = [$subcompartment];

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
        $this->assertIsArray($newListing['inventory']);
        $inventory = $newListing['inventory'];
        $this->assertCount(1, $inventory);
        $compartment = $inventory[0];
        $this->assertEquals('PHPUnit Forest Compartment', $compartment['name']);

        // @todo subcompartments
        //$this->assertIsArray($compartment['subCompartments']);
        //$this->assertCount(1, $compartment['subCompartments']);
        //$subcompartment = $compartment['subCompartments'][0];
        //$this->assertEquals('PHPUnit Forest SubComp 1A', $subcompartment['name']);
    }
}

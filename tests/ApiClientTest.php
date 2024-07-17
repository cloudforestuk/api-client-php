<?php

declare(strict_types=1);

use CloudForest\ApiClient;
use PHPUnit\Framework\TestCase;

final class ApiClientTest extends TestCase
{
    public function testMarketplaceUrl(): void
    {
        $client = new ApiClient('', '', getenv('CLOUDFOREST_HOST'), '');

        $url = $client->getMarketplaceURL();

        $this->assertSame('this will fail', $url);
    }
}
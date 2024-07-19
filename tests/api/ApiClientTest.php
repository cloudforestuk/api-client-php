<?php

declare(strict_types=1);

namespace Tests\Api;

use Tests\TestBase;

final class ApiClientTest extends TestBase
{
    public function testGetMarketplaceUrl(): void
    {
        $host = getenv('CLOUDFOREST_HOST');
        $api = $this->getCloudForestClient();
        $url = $api->getMarketplaceURL();
        $this->assertSame($host, $url);
    }

    public function testGetConnectionUrl(): void
    {
        $host = getenv('CLOUDFOREST_HOST');
        $id = getenv('CLOUDFOREST_CLIENT_ID');
        $callback = "http://www.example.com/completeConnection";
        $expected = $host
            . '/connect/begin?clientId='
            . $id
            . '&callback='
            . urlencode($callback);

        $api = $this->getCloudForestClient();
        $url = $api->getConnectionURL($callback);

        $this->assertSame($expected, $url);
    }
}

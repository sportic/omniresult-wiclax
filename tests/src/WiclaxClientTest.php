<?php

namespace Sportic\Omniresult\Wiclax\Tests;

use Sportic\Omniresult\Wiclax\WiclaxClient;

class WiclaxClientTest extends AbstractTest
{
    public function test_supportsDetect()
    {
        $client = new WiclaxClient();
        self::assertInstanceOf(WiclaxClient::class, $client);
        self::assertFalse($client->supportsDetect());
        self::assertTrue($client->supportsDetectFromSource());
    }
}

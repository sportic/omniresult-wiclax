<?php

namespace Sportic\Omniresult\Wiclax\Tests\RequestDetectors;

use PHPUnit\Framework\TestCase;
use Sportic\Omniresult\Common\RequestDetector\DetectorResult;
use Sportic\Omniresult\Common\RequestDetector\Detectors\AbstractSourceDetector;
use Sportic\Omniresult\Wiclax\RequestDetectors\SourceDetector;

class SourceDetectorTest extends TestCase
{
    /**
     * @param $url
     * @param $result
     * @dataProvider data_detect
     * @return void
     */
    public function test_detect($url, $isValid, $params)
    {
        $crawler = $crawler ?? AbstractSourceDetector::generateCrawler($url);
        $result = SourceDetector::detect($crawler);

        self::assertInstanceOf(DetectorResult::class, $result);
        self::assertEquals($isValid, $result->isValid());
        self::assertEquals($params, $result->getParams());
    }

    public function data_detect(): array
    {
        return [
            ['https://www.example.com', false, []],
            [
                'https://liniadesosire.ro/rezultate/crosul-osut-2024/',
                true,
                ['src' => 'https://liniadesosire.ro/wp-content/glive-results/crosul-osut-2024/Crosul OSUT 2024.clax']
            ],
        ];
    }
}

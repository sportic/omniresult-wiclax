<?php

namespace Sportic\Omniresult\Wiclax\Tests\Parsers;

use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\Parsers\ResultsPage as PageParser;
use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage as PageScraper;

/**
 * Class EventPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
class ResultsPageTest extends AbstractPageTest
{
    public function testGenerate()
    {
        $parametersParsed = static::initParserFromFixturesJson(
            new PageParser(),
            (new PageScraper()),
            'ResultsPage/default'
        );

        /** @var Result $record */
        $records = $parametersParsed->getRecords();
        self::assertCount(48, $records);

        $record = $records[10];

        self::assertInstanceOf(Result::class, $record);
        self::assertEquals('Pentek', $record->getFirstName());
        self::assertEquals('Raul', $record->getLastName());

        self::assertEquals('5911.884', $record->getTime());

        self::assertEquals('39', $record->getPosGender());
        self::assertEquals(null, $record->getStatus());


        $record = $records[47];
        self::assertEquals('DNF', $record->getStatus());
    }

    /**
     * @inheritdoc
     */
    protected static function getNewScraper()
    {
        $parameters = ['eventId' => '77', 'raceId' => '184', 'page' => 2];
        $scraper = new PageScraper();
        $scraper->initialize($parameters);
        return $scraper;
    }

    /**
     * @inheritdoc
     */
    protected static function getNewParser()
    {
        return new PageParser();
    }
}

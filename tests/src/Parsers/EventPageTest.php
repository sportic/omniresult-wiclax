<?php

namespace Sportic\Omniresult\Wiclax\Tests\Parsers;

use Sportic\Omniresult\Common\Models\Event;
use Sportic\Omniresult\Common\Models\Race;
use Sportic\Omniresult\Wiclax\Scrapers\EventPage as PageScraper;
use Sportic\Omniresult\Wiclax\Parsers\EventPage as PageParser;

/**
 * Class EventPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
class EventPageTest extends AbstractPageTest
{
    public function testGenerate()
    {
        $parametersParsed = static::initParserFromFixturesJson(
            new PageParser(),
            (new PageScraper()),
            'EventPage/default'
        );

        /** @var Event $record */
        $record = $parametersParsed->getRecord();

        self::assertInstanceOf(Event::class, $record);
        self::assertEquals('Timisoara City Marathon', $record->getName());

        /** @var Race[] $races */
        $races = $parametersParsed->getRecords();
        self::assertCount(5, $races);

        $lastRace = reset($races);
        self::assertInstanceOf(Race::class, $lastRace);
        self::assertEquals('5Km', $lastRace->getName());

        $categories = $lastRace->getParameter('categories');
        self::assertCount(8, $categories);

        self::assertEquals('1169,1168,1167,1166,1165,1164,1163,1162', $lastRace->getParameter('categoriesString'));
    }

    /**
     * @inheritdoc
     */
    protected static function getNewScraper()
    {
        $parameters = ['id' => 'cozia-mountain-run-6/individual/-bf626f0882/1281/'];
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

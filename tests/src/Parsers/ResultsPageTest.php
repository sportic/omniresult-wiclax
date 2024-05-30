<?php

namespace Sportic\Omniresult\Wiclax\Tests\Parsers;

use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Wiclax\Parsers\Resultspage as PageParser;
use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage as PageScraper;
use Sportic\Omniresult\Wiclax\Tests\Fixtures\Results\ResultsFiles;

/**
 * Class EventPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
class ResultsPageTest extends AbstractPageTest
{
    public function test_get_categories_none()
    {
        $scraper = new PageScraper();
        $scraper->initialize(['event' => '77', 'race' => '184']);

        /** @var ListContent $parametersParsed */
        $parametersParsed = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::noCategories()
        );
        self::assertSame([], $parametersParsed->get('categories'));
    }

    public function test_get_categories()
    {
        $scraper = new PageScraper();
        $scraper->initialize(['event' => '77', 'race' => 'Cros 10km']);

        /** @var ListContent $parametersParsed */
        $parametersParsed = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::multipleCategories()
        );
        $categories = $parametersParsed->get('categories');
        self::assertIsArray($categories);
        self::assertCount(6, $categories);

        /** @var RaceCategory $category */
        $category = reset($categories);
        self::assertInstanceOf(RaceCategory::class, $category);
        self::assertSame('1417', $category->getId());
        self::assertSame('14-17 ani', $category->getName());

        $category = end($categories);
        self::assertInstanceOf(RaceCategory::class, $category);
        self::assertSame('+60 ani', $category->getName());
        self::assertSame('60+', $category->getId());
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

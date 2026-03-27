<?php

namespace Sportic\Omniresult\Wiclax\Tests\Parsers;

use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\Parsers\ExporterApi as PageParser;
use Sportic\Omniresult\Wiclax\Scrapers\ExporterApi as PageScraper;
use Sportic\Omniresult\Wiclax\Tests\Fixtures\Results\ResultsFiles;
use Symfony\Component\BrowserKit\Response;

/**
 * Class ExporterApiTest
 * @package Sportic\Omniresult\Wiclax\Tests\Parsers
 */
class ExporterApiTest extends AbstractPageTest
{
    public function test_get_all_results()
    {
        $scraper = $this->makeScraper();

        /** @var ListContent $content */
        $content = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::exporterApi()
        );

        self::assertInstanceOf(ListContent::class, $content);
        $records = $content->get('records');
        self::assertIsArray($records);
        self::assertCount(10, $records);
    }

    public function test_filter_by_race()
    {
        $scraper = $this->makeScraper('2.5k');

        /** @var ListContent $content */
        $content = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::exporterApi()
        );

        $records = $content->get('records');
        self::assertCount(5, $records);
    }

    public function test_result_fields()
    {
        $scraper = $this->makeScraper('2.5k');

        /** @var ListContent $content */
        $content = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::exporterApi()
        );

        $records = $content->get('records');
        /** @var Result $first */
        $first = reset($records);

        self::assertInstanceOf(Result::class, $first);
        self::assertSame('3110', $first->getId());
        self::assertSame('3110', $first->getBib());
        self::assertSame('DANIEL', $first->getFirstName());
        self::assertSame('SCROB', $first->getLastName());
        self::assertSame('male', $first->getGender());
        self::assertSame('Romania', $first->getCountry());
        self::assertSame('1982', $first->getYob());
        self::assertSame('Clubul Sportiv Comunitar Sibiu', $first->getClub());
        self::assertSame('active', $first->getStatus());
        self::assertSame(1, $first->getPosGen());
        self::assertSame(1, $first->getPosCategory());
    }

    public function test_result_with_splits_and_segments()
    {
        $scraper = $this->makeScraper('21K by SportGuru');

        /** @var ListContent $content */
        $content = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::exporterApi()
        );

        $records = $content->get('records');
        /** @var Result $first */
        $first = reset($records);

        self::assertInstanceOf(Result::class, $first);
        self::assertSame('380', $first->getId());

        $splits = $first->getSplits();
        self::assertCount(2, $splits);
    }

    public function test_pagination()
    {
        $scraper = $this->makeScraper();

        /** @var ListContent $content */
        $content = static::initParserFromFixturesJson(
            new PageParser(),
            $scraper,
            ResultsFiles::exporterApi()
        );

        $pagination = $content->get('pagination');
        self::assertSame(1, $pagination['current']);
        self::assertSame(1, $pagination['all']);
    }

    /**
     * @param string|null $race
     * @return PageScraper
     */
    protected function makeScraper(?string $race = null): PageScraper
    {
        $params = ['request' => new Response(file_get_contents(ResultsFiles::exporterApi()))];
        if ($race !== null) {
            $params['race'] = $race;
        }
        $scraper = new PageScraper();
        $scraper->initialize($params);
        return $scraper;
    }

    /**
     * @inheritdoc
     */
    protected static function getNewScraper()
    {
        $scraper = new PageScraper();
        $scraper->initialize([
            'request' => new Response(file_get_contents(ResultsFiles::exporterApi())),
        ]);
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

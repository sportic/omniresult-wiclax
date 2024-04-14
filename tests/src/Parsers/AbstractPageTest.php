<?php

namespace Sportic\Omniresult\Wiclax\Tests\Parsers;

use Sportic\Omniresult\Common\Content\GenericContent;
use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Content\RecordContent;
use Sportic\Omniresult\Wiclax\Parsers\AbstractParser;
use Sportic\Omniresult\Wiclax\Scrapers\AbstractScraper;
use Sportic\Omniresult\Wiclax\Parsers\EventPage as EventPageParser;
use Sportic\Omniresult\Wiclax\Tests\AbstractTest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AbstractPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
abstract class AbstractPageTest extends AbstractTest
{
    protected static $parameters;

    /**
     * @var GenericContent|ListContent|RecordContent
     */
    protected static $parametersParsed;

    /**
     * @param AbstractParser $parser
     * @param AbstractScraper $scrapper
     * @param $fixturePath
     * @return mixed
     */
    public static function initParserFromFixtures($parser, $scrapper, $fixturePath)
    {
        $crawler = new Crawler(null, $scrapper->getCrawlerUri());
        $crawler->addContent(
            file_get_contents(
                TEST_FIXTURE_PATH . DS . 'Parsers' . DS . $fixturePath . '.html'
            ),
            'text/html;charset=utf-8'
        );

        $parser->setScraper($scrapper);
        $parser->setCrawler($crawler);

        return $parser->getContent();
    }


    /**
     * @param \Sportic\Omniresult\Wiclax\Parsers\AbstractParser $parser
     * @param \Sportic\Omniresult\Wiclax\Scrapers\AbstractScraper $scrapper
     * @param $fixturePath
     * @return mixed
     */
    public static function initParserFromFixturesJson($parser, $scrapper, $fixturePath)
    {
        $fixturePath = is_file($fixturePath)
            ? $fixturePath
            : TEST_FIXTURE_PATH . DS . 'Parsers' . DS . $fixturePath;

        $response = new Response(file_get_contents($fixturePath));
        $parser->setScraper($scrapper);
        $parser->initialize(['response' => $response]);

        return $parser->getContent();
    }

    /**
     * @param $fixturePath
     * @return mixed
     */
    public static function getParametersFixtures($fixturePath)
    {
        return unserialize(
            file_get_contents(TEST_FIXTURE_PATH . DS . 'Parsers' . DS . $fixturePath . '.serialized')
        );
    }
}

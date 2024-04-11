<?php

namespace Sportic\Omniresult\Wiclax\Tests\Scrapers;

use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ResultPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
class ResultsPageTest extends AbstractPageTest
{
    public function testGetCrawlerUri()
    {
        $crawler = $this->getCrawler();

        static::assertInstanceOf(Crawler::class, $crawler);

        static::assertSame(
            'https://app.Wiclax.ro:8443/FinishLine.Application/races/results?page=0&pageSize=9000&searchCriteria=&raceID=1184',
            $crawler->getUri()
        );
    }

    public function testGetCrawlerHtml()
    {
        $scrapper = $this->generateScraper();

        static::assertInstanceOf(ResultsPage::class, $scrapper);
        $scrapper->execute();
        $content = $scrapper->getClient()->getResponse()->getContent();

        static::assertStringContainsString('Muresan', $content);
//        file_put_contents(TEST_FIXTURE_PATH . '/Parsers/ResultsPage/default.json', $content);
    }

    /**
     * @param array $parameters
     * @return Crawler
     */
    protected function getCrawler($parameters = [])
    {
        $scraper = $this->generateScraper($parameters);
        return $scraper->getCrawler();
    }

    /**
     * @param array $parameters
     * @return ResultsPage
     */
    protected function generateScraper($parameters = [])
    {
        $default = [
            'eventId' => '77',
            'raceId' => '184',
            'page' => '2'
        ];
        $params = count($parameters) ? $parameters : $default;
        $params['raceClient'] = new \Sportic\Omniresult\Wiclax\WiclaxClient();
        $scraper = new ResultsPage();
        $scraper->initialize($params);
        return $scraper;
    }
}

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
            'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax',
            $crawler->getUri()
        );
    }

    public function testGetCrawlerHtml()
    {
        $scrapper = $this->generateScraper();

        static::assertInstanceOf(ResultsPage::class, $scrapper);
        $scrapper->execute();
        $content = $scrapper->getClient()->getResponse()->getContent();

        static::assertStringContainsString('PLOSCAR', $content);
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
            'event' => 'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax',
            'race' => 'Cros 10k',
            'page' => '2'
        ];
        $params = count($parameters) ? $parameters : $default;
        $params['raceClient'] = new \Sportic\Omniresult\Wiclax\WiclaxClient();
        $scraper = new ResultsPage();
        $scraper->initialize($params);
        return $scraper;
    }
}

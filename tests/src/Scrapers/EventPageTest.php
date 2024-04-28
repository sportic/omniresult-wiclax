<?php

namespace Sportic\Omniresult\Wiclax\Tests\Scrapers;

use Sportic\Omniresult\Wiclax\Scrapers\EventPage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class EventPageTest
 * @package Sportic\Omniresult\Wiclax\Tests\Scrapers
 */
class EventPageTest extends AbstractPageTest
{

    public function testGetCrawlerUri()
    {
        $crawler = $this->getCrawler();

        static::assertInstanceOf(Crawler::class, $crawler);

        static::assertSame(
            'https://app.Wiclax.ro:8443/FinishLine.Application/sportevents/getsportevent?id=77',
            $crawler->getUri()
        );
    }

    public function testGetCrawlerHtml()
    {
        $scrapper = $this->generateScraper();

        static::assertInstanceOf(EventPage::class, $scrapper);
        $scrapper->execute();
        $content = $scrapper->getClient()->getResponse()->getContent();

        static::assertStringContainsString('14-19 ani', $content);
        static::assertStringContainsString('KRSTIC', $content);
//        file_put_contents(TEST_FIXTURE_PATH . '/Parsers/EventPage/default.json', $content);
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
     * @return EventPage
     */
    protected function generateScraper($parameters = [])
    {
        $default = [
            'event' => 'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax'
        ];
        $params = count($parameters) ? $parameters : $default;
        $params['raceClient'] = new \Sportic\Omniresult\Wiclax\WiclaxClient();
        $scraper = new EventPage();
        $scraper->initialize($params);
        return $scraper;
    }
}

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

        static::assertStringContainsString('PC 2', $content);
        static::assertStringContainsString('42Km Feminin Open', $content);
//        file_put_contents(TEST_FIXTURE_PATH . '/Parsers/EventPage/default.json', $content);
    }

    public function testInitializeFromId()
    {
        $scraper = new EventPage();
        $scraper->initialize(['eventId' => '77']);

        self::assertEquals('77', $scraper->getEventId());
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
            'eventId' => '77',
        ];
        $params = count($parameters) ? $parameters : $default;
        $params['raceClient'] = new \Sportic\Omniresult\Wiclax\WiclaxClient();
        $scraper = new EventPage();
        $scraper->initialize($params);
        return $scraper;
    }
}

<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class AbstractScraper
 * @package Sportic\Omniresult\Trackmyrace\Scrapers
 */
abstract class AbstractScraper extends \Sportic\Omniresult\Common\Scrapers\AbstractScraper
{
    /**
     * @inheritdoc
     */
    protected function generateCrawler()
    {
        $client = $this->getClient();
        $crawler = $client->request(
            'GET',
            $this->getCrawlerUri()
        );

        return $crawler;
    }

    /**
     * @return array
     */
    protected function generateParserData()
    {
        $this->getRequest();

        return [
            'scraper' => $this,
            'response' => $this->getClient()->getResponse(),
        ];
    }



    /**
     * @return string
     */
    abstract public function getCrawlerUri(): string;/** @noinspection PhpMethodNamingConventionInspection */

}

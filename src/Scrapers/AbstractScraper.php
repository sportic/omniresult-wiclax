<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use ByTIC\GouttePhantomJs\Clients\ClientFactory;
use Goutte\Client;
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

    /** @noinspection PhpMissingParentCallCommonInspection
     * @return Client
     */
    protected function generateClient()
    {
        return ClientFactory::getGoutteClient(
            HttpClient::create(['verify_peer' => false, 'verify_host' => false])
        );
    }

    /**
     * @return string
     */
    abstract public function getCrawlerUri();/** @noinspection PhpMethodNamingConventionInspection */

    /**
     * @return string
     */
    protected function getCrawlerUriHost()
    {
        return 'https://app.Wiclax.ro:8443';
    }
}

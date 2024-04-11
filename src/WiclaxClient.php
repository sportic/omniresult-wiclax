<?php

namespace Sportic\Omniresult\Wiclax;

use Sportic\Omniresult\Common\Parsers\AbstractParser;
use Sportic\Omniresult\Common\RequestDetector\HasDetectorTrait;
use Sportic\Omniresult\Common\TimingClient;
use Sportic\Omniresult\Wiclax\Scrapers\EventPage;
use Sportic\Omniresult\Wiclax\Scrapers\ResultPage;
use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage;

/**
 * Class WiclaxClient
 * @package Sportic\Omniresult\Wiclax
 */
class WiclaxClient extends TimingClient
{
    use HasDetectorTrait;

    /**
     * @param $parameters
     * @return AbstractParser|Parsers\EventPage
     */
    public function event($parameters)
    {
        return $this->executeScrapper(EventPage::class, $parameters);
    }

    /**
     * @param $parameters
     * @return AbstractParser|Parsers\ResultsPage
     */
    public function results($parameters)
    {
        $parameters['raceClient'] = $this;
        return $this->executeScrapper(ResultsPage::class, $parameters);
    }

    /**
     * @param $parameters
     * @return AbstractParser|Parsers\ResultPage
     */
    public function result($parameters)
    {
        return $this->executeScrapper(ResultPage::class, $parameters);
    }
}

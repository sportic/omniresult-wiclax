<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use Sportic\Omniresult\Wiclax\Parsers\EventPage as Parser;

/**
 * Class CompanyPage
 * @package Sportic\Omniresult\Endu\Scrapers
 *
 * @method Parser execute()
 */
class EventPage extends AbstractScraper
{

    /**
     * @throws \Sportic\Omniresult\Common\Exception\InvalidRequestException
     */
    protected function doCallValidation()
    {
        $this->validate('eventId');
    }

    /**
     * @return string
     */
    public function getCrawlerUri()
    {
        return $this->getCrawlerUriHost()
            . '/FinishLine.Application/sportevents/getsportevent?id='
            . $this->getEventId();
    }
}

<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Wiclax\Parsers\EventPage as Parser;

/**
 * Class CompanyPage
 * @package Sportic\Omniresult\Wiclax\Scrapers
 *
 * @method Parser execute()
 */
class ResultsPage extends AbstractScraper
{
    /**
     * @throws \Sportic\Omniresult\Common\Exception\InvalidRequestException
     */
    protected function doCallValidation()
    {
        $this->validate('event','race');
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->getParameter('event');
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->getParameter('race');
    }


    /**
     * @inheritdoc
     */
    protected function generateParserData()
    {
        $data = parent::generateParserData();

        $data['race'] = $this->getRace();
        return $data;
    }

        /**
     * @return string
     */
    public function getCrawlerUri(): string
    {
        return $this->getEvent();
    }
}

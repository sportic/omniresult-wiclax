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
     * @return mixed
     */
    public function getEventId()
    {
        return $this->getParameter('eventId');
    }

    /**
     * @return mixed
     */
    public function getRaceId()
    {
        return $this->getParameter('raceId');
    }

    /**
     * @return RaceCategory[]
     */
    public function getRaceCategories()
    {
        if (!$this->hasParameter('raceCategories')) {
            /** @var ListContent $eventReturn */
            $eventReturn = $this->getParameter('raceClient')->event(['eventId' => $this->getEventId()])->getContent();
            $races = $eventReturn->getRecords();
            $race = $races[$this->getRaceId()];
            $this->setParameter('raceCategories', $race->getParameter('categories'));
        }
        return $this->getParameter('raceCategories', []);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->getParameter('page', 1);
    }

    /**
     * @param $page
     */
    public function setPage($page)
    {
        $page =  !empty($page) ? $page : 1;
        $this->setParameter('page', $page);
    }

    /**
     * @return mixed
     */
    public function getRaceCategoryId()
    {
        $selected = array_slice($this->getRaceCategories(), $this->getPage() -1 , 1);
        if (!count($selected)) {
            return 0;
        }
        return reset($selected)->getId();
    }

    /**
     * @inheritdoc
     */
    protected function generateParserData()
    {
        $data = parent::generateParserData();

        $data['page'] = $this->getPage();
        $data['raceCategories'] = $this->getRaceCategories();
        return $data;
    }

        /**
     * @return string
     */
    public function getCrawlerUri()
    {
        return $this->getCrawlerUriHost()
            . '/FinishLine.Application/races/results?page=0&pageSize=9000&searchCriteria='
            . '&raceID=' . $this->getRaceCategoryId();
    }
}

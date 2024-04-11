<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use Nip\Utility\Arr;
use Sportic\Omniresult\Common\Content\ParentListContent;
use Sportic\Omniresult\Common\Models\Event;
use Sportic\Omniresult\Common\Models\Race;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Wiclax\Parsers\Traits\HasJsonConfigTrait;

/**
 * Class EventPage
 * @package Sportic\Omniresult\Endu\Parsers
 */
class EventPage extends AbstractParser
{
    use HasJsonConfigTrait;

    protected $returnContent = [];

    /**
     * @return array
     */
    protected function generateContent()
    {
        $configArray = $this->getConfigArray();

        $params = [
            'record' => $this->parseEvent($configArray['sportEvent']),
            'records' => $this->parseRaces($configArray['numberCategories'])
        ];

        $this->parseRacesCategories($params['records'], $configArray['races']);

        return $params;
    }

    /**
     * @param $config
     * @return Event
     */
    public function parseEvent($config)
    {
        $event = new Event([
            'id' => $config['id'],
            'name' => $config['name'],
            'city' => $config['location'],
        ]);

        $event->setDateFromFormat(DATE_RFC3339_EXTENDED, $config['eventDate']);
        return $event;
    }

    /**
     * @param $config
     * @return Race[]
     */
    public function parseRaces($config)
    {
        $racesArray = $config;
        $return = [];
        foreach ($racesArray as $raceItem) {
            $return[$raceItem['id']] = $this->parseRace($raceItem);
        }
        return $return;
    }

    /**
     * @param $raceItem
     * @return Race
     */
    protected function parseRace($raceItem)
    {
        $config = [
            'id' => $raceItem['id'],
            'name' => $raceItem['name'],
            'externalId' => $raceItem['externalId'],
            'rangeStart' => $raceItem['rangeStart'],
            'rangeStop' => $raceItem['rangeStop'],
        ];

        return new Race($config);
    }

    /**
     * @param Race[] $races
     * @param $categoriesArray
     */
    protected function parseRacesCategories($races, $categoriesArray)
    {
        $raceCategories = [];
        foreach ($categoriesArray as $categoryArray) {
            $category = new RaceCategory([
                'id' => $categoryArray['id'],
                'name' => $categoryArray['name'],
                'gender' => $categoryArray['gender'],
                'ageStart' => $categoryArray['ageStart'],
                'ageEnd' => $categoryArray['ageEnd']
            ]);

            $raceCategories[$categoryArray['numberCategory']['id']][$category->getId()] = $category;
        }
        foreach ($races as $race) {
            $categories = (isset($raceCategories[$race->getId()]))
                ? $raceCategories[$race->getId()]
                : [];

            $categoriesString = implode(',', array_keys($categories));
            $race->setParameter('categories', $categories);
            $race->setParameter('categoriesString', $categoriesString);
        }
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getContentClassName()
    {
        return ParentListContent::class;
    }

    /**
     * @inheritdoc
     */
    public function getModelClassName()
    {
        return Race::class;
    }
}

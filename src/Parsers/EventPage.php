<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use Nip\Utility\Arr;
use SimpleXMLElement;
use Sportic\Omniresult\Common\Content\ParentListContent;
use Sportic\Omniresult\Common\Models\Event;
use Sportic\Omniresult\Common\Models\Race;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Wiclax\Parsers\Traits\HasXmlTrait;

/**
 * Class EventPage
 * @package Sportic\Omniresult\Endu\Parsers
 */
class EventPage extends AbstractParser
{
    use HasXmlTrait;

    protected $returnContent = [];

    /**
     * @return array
     */
    protected function generateContent()
    {
        $xmlObject = $this->getXmlObject();

        $params = [
            'record' => $this->parseEvent($xmlObject->attributes()),
            'records' => $this->parseRaces($xmlObject->xpath('Parcours')),
        ];

//        $this->parseRacesCategories($params['records'], $xmlObject['races']);

        return $params;
    }

    /**
     * @param $config
     * @return Event
     */
    public function parseEvent(SimpleXMLElement $config)
    {
        $event = new Event([
            'id' => $config['cle'] ?? uniqid(),
            'name' => $config['nom'] ?? null,
        ]);

        $event->setDateFromFormat(DATE_RFC3339_EXTENDED, (string) $config['derSvg']);
        $event->setParameter('organizer', (string) $config['organisateur']);
        return $event;
    }

    /**
     * @param array $racesArray
     * @return Race[]
     */
    public function parseRaces($racesArray)
    {
        if (count($racesArray) !== 1) {
            return [];
        }
        $racesArray = $racesArray[0];
        $racesArray = $racesArray->xpath('Pcs');
        foreach ($racesArray as $raceItem) {
            $name = (string) $raceItem['nom'];
            $return[$name] = $this->parseRace($raceItem);
        }
        return $return;
    }

    /**
     * @param $raceItem
     * @return Race
     */
    protected function parseRace($raceItem)
    {
        $name = (string) $raceItem['nom'];
        $config = [
            'id' => $name,
            'name' =>$name,
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

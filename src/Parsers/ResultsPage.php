<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\Helper;
use Sportic\Omniresult\Wiclax\Parsers\Traits\HasJsonConfigTrait;
use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage as Scraper;

/**
 * Class ResultsPage
 * @package Sportic\Omniresult\Wiclax\Parsers
 *
 * @method Scraper getScraper()
 */
class ResultsPage extends AbstractParser
{
    use HasJsonConfigTrait;

    protected $header = null;

    /**
     * @return array
     */
    protected function generateContent()
    {
        $configArray = $this->getConfigArray();
        $categories = $this->getParameter('raceCategories', []);
        $results = $this->parseResults($configArray, $categories);

        return [
            'pagination' => [
                'current' => $this->getParameter('page', 1),
                'all' => count($categories)
            ],
            'records' => $results
        ];
    }

    /**
     * @param $resultsArray
     * @param $categories
     * @return array
     */
    protected function parseResults($resultsArray, $categories)
    {
        $return = [];
        foreach ($resultsArray as $resultArray) {
            $return[] = $this->parseResult($resultArray, $categories);
        }
        return $return;
    }

    /**
     * @param $config
     * @param $categories
     * @return Result
     */
    protected function parseResult($config, $categories)
    {
        $parameters = [
            'firstName' => $config['firstName'],
            'lastName' => $config['lastName'],
        ];

        /** @var RaceCategory $category */
        $category = isset($categories[$config['raceID']]) ? $categories[$config['raceID']] : [];
        if ($category instanceof RaceCategory) {
            $parameters['category'] = $category->getName();
            $parameters['gender'] = $this->parseGenderFromCategory($category);
        }

        $parameters['posCategory'] = $config['raceStanding'];
        $parameters['posGender'] = $config['raceCategoryStanding'];

        $parameters['time'] = Helper::durationToSeconds($config['duration']);
        $parameters['bib'] = $config['raceNumber'];
        $parameters['id'] = $config['raceParticipantID'];

        $parameters['status'] = $this->parseStatus($config);

        return new Result($parameters);
    }

    /**
     * @param RaceCategory $category
     * @return string
     */
    protected function parseGenderFromCategory($category)
    {
        $listName = strtolower($category->getParameter('gender'));
        if (strpos($listName, 'female') === 0) {
            return 'female';
        }
        if (strpos($listName, 'male') === 0) {
            return 'male';
        }
        return '';
    }

    /**
     * @param $config
     * @return string|null
     */
    protected function parseStatus($config)
    {
        if ($config['hasAbandoned'] == true) {
            return 'DNF';
        }

        if ($config['hasStarted'] === false) {
            return 'DNS';
        }

        if ($config['finishTime'] === null) {
            return 'DNF';
        }

        if ($config['duration'] === 0) {
            return 'DNF';
        }

        return null;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getContentClassName()
    {
        return ListContent::class;
    }

    /**
     * @inheritdoc
     */
    public function getModelClassName()
    {
        return Result::class;
    }
}

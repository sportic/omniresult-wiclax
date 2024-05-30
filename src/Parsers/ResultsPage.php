<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use SimpleXMLElement;
use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\Athlete;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Common\Models\Split;
use Sportic\Omniresult\Common\Models\SplitCollection;
use Sportic\Omniresult\Wiclax\Helper;
use Sportic\Omniresult\Wiclax\Parsers\Traits\HasXmlTrait;
use Sportic\Omniresult\Wiclax\Scrapers\ResultsPage as Scraper;

/**
 * Class ResultsPage
 * @package Sportic\Omniresult\Wiclax\Parsers
 *
 * @method Scraper getScraper()
 */
class ResultsPage extends AbstractParser
{
    use HasXmlTrait;

    protected ?array $categories = null;

    protected ?array $athletes = null;

    protected ?SplitCollection $timingPoints = null;

    /**
     * @return array
     */
    protected function generateContent(): array
    {
        $results = $this->getResults();

        return [
            'pagination' => [
                'current' => $this->getParameter('page', 1),
                'all' => 1
            ],
            'records' => $results
        ];
    }

    protected function getAthlete(string $id)
    {
        $athletes = $this->checkAtheletes();
        return $athletes[$id] ?? null;
    }

    protected function checkAtheletes(): ?array
    {
        if ($this->athletes === null) {
            $configArray = $this->getXmlObject();
            $athletesXml = $configArray->xpath('//Etapes/Etape/Engages/E');
            $athletes = $this->parseAthletes($athletesXml);
            $this->athletes = $athletes;
        }
        return $this->athletes;
    }

    protected function parseAthletes($athletesXml): array
    {
        $athletes = [];
        foreach ($athletesXml as $athleteXml) {
            $raceName = (string)$athleteXml['p'];
            if ($raceName == $this->getParameter('race')) {
                $athlete = $this->parseAthlete($athleteXml);
                $athletes[$athlete->getId()] = $athlete;
            }
        }
        return $athletes;
    }


    protected function parseAthlete(SimpleXMLElement $athleteXml): Athlete
    {
        $athlete = new Athlete();
        $bib = (string)$athleteXml['d'];
        $athlete->setId($bib);

        $athleteName = (string)$athleteXml['n'];
        $athleteName = str_replace([urldecode('%C2%A0')], ' ', $athleteName);
        $athlete->setFullNameLF($athleteName);

        $athlete->setYob((string)$athleteXml['a']);
        $athlete->setGender($this->parseAthleteGender((string)$athleteXml['x']));
        $athlete->setCountry((string)$athleteXml['na']);

        $categoryName = (string)$athleteXml['ca'];
        $category = $this->parseAthleteCategory($categoryName);

        if ($this->getScraper()->isGenderCategoryMerge()) {
            $gender = $athlete->getGender();
            $categoryName = trim(ucfirst($gender) . ' ' . $category->getName());
            $category->setName($categoryName);
        }
        $athlete->setCategory($category);
        return $athlete;
    }

    protected function parseAthleteCategory($categoryName)
    {
        $category = new RaceCategory();
        $category->setId('general');
        $category->setName('General');

        if (empty($categoryName)) {
            return $category;
        }

        $foundCategory = $this->getCategory($categoryName);
        if ($foundCategory) {
            $category = clone $foundCategory;
            return $category;
        }
        $category->setName($categoryName);
        $category->setId($categoryName);
        return $category;
    }

    protected function getResults(): array
    {
        $configArray = $this->getXmlObject();
        $resultsXml = $configArray->xpath('//Etapes/Etape/Resultats/R');
        return $this->parseResults($resultsXml);
    }

    protected function parseResults($resultsXml)
    {
        $results = [];
        $athletes = $this->checkAtheletes();
        $posGen = 1;
        foreach ($resultsXml as $resultXml) {
            $result = $this->parseResult($resultXml);
            $bib = $result->getId();
            $athlete = $athletes[$bib] ?? null;
            if ($athlete === null) {
                continue;
            }
            $result->setPosGen($posGen++);
            $result->populateFromAthlete($athlete);
            $results[$result->getId()] = $result;
            unset($athletes[$bib]);
        }
        foreach ($athletes as $athlete) {
            $result = new Result();
            $result->setId($athlete->getId());
            $result->populateFromAthlete($athlete);
            $result->setStatus('DNS');
            $results[$result->getId()] = $result;
        }
        return $results;
    }

    /**
     * @param $config
     * @return Result
     */
    protected function parseResult($config): Result
    {
        $result = new Result();
        $bib = (string)$config['d'];
        $result->setId($bib);
        $result->setBib($bib);

        $time = (string) $config['t'];
        $result->setStatus($this->parseStatus($time));

        $result->setTimeGross(Helper::durationToSeconds($time));
        $result->setTime(Helper::durationToSeconds($config['re']));
        //$config['b'] // day time of finish;

        $timingPoints = $this->getTimingPoints();
        foreach ($timingPoints as $split) {
            $split = clone $split;
            $splitTime = (string)$config['p' . $split->getId()];
            $split->setParameters(['timeFromStart' => Helper::durationToSeconds($splitTime)]);
            $result->getSplits()->add($split, $split->getId());
        }
        return $result;
    }

    /**
     * @param $config
     * @return string|null
     */
    protected function parseStatus($time)
    {
        switch ($time) {
            case 'DNS':
                return 'DNS';
            case 'DNF':
            case 'Withdrawal':
                return 'DNF';
            case 'Disqualified':
                return 'DSQ';
        }
        return 'active';
    }

    protected function getCategory($id): ?RaceCategory
    {
        $categories = $this->getCategories();
        return $categories[$id] ?? null;
    }

    /**
     * @return RaceCategory[]
     */
    protected function getCategories(): array
    {
        return $this->checkCategories();
    }

    protected function checkCategories()
    {
        if ($this->categories === null) {
            $configArray = $this->getXmlObject();
            $categoriesXml = $configArray->xpath('//Categories/G/C');
            $categories = $this->parseCategories($categoriesXml);
            $this->categories = $categories;
        }
        return $this->categories;
    }

    protected function parseCategories($categoriesXml): array
    {
        $categories = [];
        $raceName = $this->getScraper()->getRace();
        foreach ($categoriesXml as $categoryXml) {
            if ($this->parseCategoryInRace($categoryXml, $raceName) === false) {
                continue;
            }
            $category = $this->parseCategory($categoryXml);
            $categories[$category->getId()] = $category;
        }
        return $categories;
    }

    protected function parseCategoryInRace($categoryXml, $raceName)
    {
        if (empty($raceName)) {
            return true;
        }
        $categoryRace = (string)$categoryXml['crs'];
        if (empty($categoryRace)) {
            return true;
        }
        if ($categoryRace == $raceName) {
            return true;
        }
        $categories = explode('¤', $categoryRace);
        $categories = array_map('trim', $categories);
        return in_array($raceName, $categories);
    }

    protected function parseCategory($categoryXml)
    {
        $category = new RaceCategory();
        $category->setId((string)$categoryXml['abr']);
        $category->setName((string)$categoryXml['nom']);
        return $category;
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

    protected function parseAthleteGender(string $param)
    {
        if ($param == 'M') {
            return 'male';
        }
        if ($param == 'F') {
            return 'female';
        }
        return null;
    }

    protected function getTimingPoints()
    {
        if ($this->timingPoints === null) {
            $this->timingPoints = $this->generateTimingPoints();
        }
        return $this->timingPoints;
    }

    protected function generateTimingPoints()
    {
        $timingPoints = new SplitCollection();

        $configArray = $this->getXmlObject();
        $resultsXml = $configArray->xpath('//Etapes/Etape/Pointages/Pointage');
        foreach ($resultsXml as $resultXml) {
            $timingPoint = $this->parseTimingPoint($resultXml);
            if ($timingPoint === null) {
                continue;
            }
            $timingPoints->add($timingPoint, $timingPoint->getId());
        }
        return $timingPoints;
    }

    protected function parseTimingPoint($resultXml)
    {
        $races = explode(',', (string)$resultXml['pcs']);
        if (!in_array($this->getParameter('race'),$races)) {
            return null;
        }
        $split = new Split();
        $split->setId((string)$resultXml['id']);
        $split->setName((string)$resultXml['nom']);
        return $split;
    }

}

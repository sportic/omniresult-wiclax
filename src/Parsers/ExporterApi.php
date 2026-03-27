<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use Sportic\Omniresult\Common\Content\ListContent;
use Sportic\Omniresult\Common\Models\RaceCategory;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Common\Models\Split;
use Sportic\Omniresult\Wiclax\Helper;
use Sportic\Omniresult\Wiclax\Scrapers\ExporterApi as Scraper;

/**
 * Class ExporterApi
 * @package Sportic\Omniresult\Wiclax\Parsers
 *
 * @method Scraper getScraper()
 */
class ExporterApi extends AbstractParser
{
    /**
     * @return array
     */
    protected function generateContent(): array
    {
        $data = json_decode($this->getResponse()->getContent(), true);
        $rows = $data['rows'] ?? [];
        $race = $this->getScraper() !== null
            ? $this->getScraper()->getRace()
            : $this->getParameter('race');

        $results = $this->parseResults($rows, $race);

        return [
            'pagination' => [
                'current' => 1,
                'all' => 1,
            ],
            'records' => $results,
        ];
    }

    /**
     * @param array $rows
     * @param string|null $race
     * @return Result[]
     */
    protected function parseResults(array $rows, ?string $race): array
    {
        $results = [];
        foreach ($rows as $row) {
            if ($race !== null && ($row['race'] ?? '') !== $race) {
                continue;
            }
            $result = $this->parseResult($row);
            $results[$result->getId()] = $result;
        }
        return $results;
    }

    /**
     * @param array $row
     * @return Result
     */
    protected function parseResult(array $row): Result
    {
        $result = new Result();
        $bib = (string)($row['bib'] ?? $row['realbib'] ?? '');
        $result->setId($bib);
        $result->setBib($bib);

        $result->setFirstName($row['firstname'] ?? '');
        $result->setLastName($row['lastname'] ?? '');
        $result->setCountry($row['nationality'] ?? null);
        $result->setGender($this->parseGender($row['sex'] ?? null));
        $result->setYob($row['year'] ?? null);
        $result->setClub($row['team'] ?? null);

        $time = $row['time'] ?? '';
        $result->setStatus($this->parseStatus($time));
        $result->setTimeGross(Helper::durationToSeconds($time));
        $result->setTime(Helper::durationToSeconds($row['chiptime'] ?? $time));

        $result->setPosGen($row['rank'] ?? 0);
        $result->setPosCategory($row['rankbycat'] ?? 0);

        $category = $this->parseCategory($row['category'] ?? '');
        $result->setCategory($category);

        $this->parseSplits($result, $row['splits'] ?? []);
        $this->parseSegments($result, $row['segments'] ?? []);

        return $result;
    }

    /**
     * @param string $categoryName
     * @return RaceCategory
     */
    protected function parseCategory(string $categoryName): RaceCategory
    {
        $category = new RaceCategory();
        $category->setId($categoryName ?: 'general');
        $category->setName($categoryName ?: 'General');
        return $category;
    }

    /**
     * @param Result $result
     * @param array $splits
     */
    protected function parseSplits(Result $result, array $splits): void
    {
        foreach ($splits as $i => $split) {
            $splitObj = new Split();
            $splitObj->setId((string)($i + 1));
            $splitObj->setName($split['name'] ?? '');
            $splitObj->setParameters(['timeFromStart' => Helper::durationToSeconds($split['time'] ?? '')]);
            $result->getSplits()->add($splitObj, $splitObj->getId());
        }
    }

    /**
     * @param Result $result
     * @param array $segments
     */
    protected function parseSegments(Result $result, array $segments): void
    {
        foreach ($segments as $i => $segment) {
            $splitObj = new Split();
            $splitObj->setId('seg_' . ($i + 1));
            $splitObj->setName($segment['name'] ?? '');
            $splitObj->setParameters(['time' => Helper::durationToSeconds($segment['time'] ?? '')]);
            $result->getSplits()->add($splitObj, $splitObj->getId());
        }
    }

    /**
     * @param string|null $sex
     * @return string|null
     */
    protected function parseGender(?string $sex): ?string
    {
        if ($sex === 'M') {
            return 'male';
        }
        if ($sex === 'F') {
            return 'female';
        }
        return null;
    }

    /**
     * @param string $time
     * @return string
     */
    protected function parseStatus(string $time): string
    {
        switch (strtoupper($time)) {
            case 'DNS':
                return 'DNS';
            case 'DNF':
            case 'WITHDRAWAL':
                return 'DNF';
            case 'DISQUALIFIED':
            case 'DSQ':
                return 'DSQ';
        }
        return 'active';
    }

    /**
     * @inheritdoc
     */
    protected function getContentClassName(): string
    {
        return ListContent::class;
    }

    /**
     * @inheritdoc
     */
    public function getModelClassName(): string
    {
        return Result::class;
    }
}

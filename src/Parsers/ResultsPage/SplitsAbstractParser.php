<?php

namespace Sportic\Omniresult\Wiclax\Parsers\ResultsPage;

use SimpleXMLElement;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Common\Models\Split;
use Sportic\Omniresult\Common\Models\SplitCollection;
use Sportic\Omniresult\Common\Utility\ParametersTrait;
use Sportic\Omniresult\Wiclax\Helper;

abstract class SplitsAbstractParser
{
    public const XML_TIME_PREFIX = 'p';

    use ParametersTrait;

    protected ?SplitCollection $splitCollection = null;

    protected ?SimpleXMLElement $xmlObject = null;

    public function setXmlObject(mixed $getXmlObject)
    {
        $this->xmlObject = $getXmlObject;
    }

    public function populateResult(Result $result, $config)
    {
        $splitCollection = $this->getSplitCollection();
        foreach ($splitCollection as $split) {
            $resultSplit = $this->generateResultSplit($config, $split);
            $result->getSplits()->add($resultSplit, $resultSplit->getId());
        }
    }

    protected function generateResultSplit($resultXml, $split): Split
    {
        $split = clone $split;
        $split->setParameters($this->generateResultSplitParams($resultXml, $split));

        return $split;
    }

    protected function generateResultSplitParams($resultXml, $split): array
    {
        $splitTime = (string)$resultXml[static::XML_TIME_PREFIX . $split->getId()];
        return ['timeFromStart' => Helper::durationToSeconds($splitTime)];
    }

    protected function getSplitCollection(): ?SplitCollection
    {
        if ($this->splitCollection === null) {
            $this->splitCollection = $this->generateSplitCollection();
        }
        return $this->splitCollection;
    }

    protected function generateSplitCollection(): SplitCollection
    {
        $splitCollection = new SplitCollection();
        $resultsXml = $this->xmlObject->xpath($this->generateSplitCollectionXmlPath());
        foreach ($resultsXml as $resultXml) {
            $timingPoint = $this->generateSplitObject($resultXml);
            if ($timingPoint === null) {
                continue;
            }
            $splitCollection->add($timingPoint, $timingPoint->getId());
        }
        return $splitCollection;
    }

    abstract protected function generateSplitCollectionXmlPath();

    protected function generateSplitObject($resultXml)
    {
        $races = explode(',', (string)$resultXml['pcs']);
        if (!in_array($this->getParameter('race'), $races)) {
            return null;
        }
        $split = new Split();
        $split->setId((string)$resultXml['id']);
        $split->setName((string)$resultXml['nom']);
        return $split;
    }
}

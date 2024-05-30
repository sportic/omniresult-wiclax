<?php

namespace Sportic\Omniresult\Wiclax\Parsers\ResultsPage;

use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\Helper;

class SplitsSegmentsParser extends SplitsAbstractParser
{
    public const XML_TIME_PREFIX = 's';
    protected function generateResultSplitParams($resultXml, $split): array
    {
        $params = parent::generateResultSplitParams($resultXml, $split);
        $params['time'] = $params['timeFromStart'];
        unset($params['timeFromStart']);
        return $params;
    }
    protected function generateSplitCollectionXmlPath(): string
    {
        return '//Etapes/Etape/Segments/S';
    }
}

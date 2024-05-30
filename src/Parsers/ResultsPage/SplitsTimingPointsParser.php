<?php

namespace Sportic\Omniresult\Wiclax\Parsers\ResultsPage;

class SplitsTimingPointsParser extends SplitsAbstractParser
{

    protected function generateSplitCollectionXmlPath(): string
    {
        return '//Etapes/Etape/Pointages/Pointage';
    }
}

<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use Sportic\Omniresult\Wiclax\Parsers\ResultPage as Parser;

/**
 * Class CompanyPage
 * @package Sportic\Omniresult\Wiclax\Scrapers
 *
 * @method Parser execute()
 */
class ResultPage extends AbstractScraper
{

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->getParameter('uid');
    }

    /**
     * @throws \Sportic\Omniresult\Common\Exception\InvalidRequestException
     */
    protected function doCallValidation()
    {
        $this->validate('uid');
    }

    /**
     * @inheritdoc
     */
    protected function generateParserData(): array
    {
        return [
            'uid' => $this->getUid(),
            'scraper' => $this,
        ];
    }

}

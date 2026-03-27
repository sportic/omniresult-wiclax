<?php

namespace Sportic\Omniresult\Wiclax\Scrapers;

use Symfony\Component\BrowserKit\Response;
use Sportic\Omniresult\Wiclax\Parsers\ExporterApi as Parser;

/**
 * Class ExporterApi
 * @package Sportic\Omniresult\Wiclax\Scrapers
 *
 * @method Parser execute()
 */
class ExporterApi extends AbstractScraper
{
    /**
     * @throws \Sportic\Omniresult\Common\Exception\InvalidRequestException
     */
    protected function doCallValidation()
    {
        $this->validate('request');
    }

    /**
     * @return Response
     */
    public function getApiRequest(): Response
    {
        return $this->getParameter('request');
    }

    /**
     * @return string|null
     */
    public function getRace(): ?string
    {
        return $this->getParameter('race');
    }

    /**
     * Bypass HTTP call – data is already available via the request parameter.
     *
     * @return null
     */
    protected function generateRequest()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function generateParserData(): array
    {
        return [
            'scraper' => $this,
            'response' => $this->getApiRequest(),
            'race' => $this->getRace(),
        ];
    }

    /**
     * Not used for this scraper; data comes from the request parameter.
     *
     * @return string
     */
    public function getCrawlerUri(): string
    {
        return '';
    }
}

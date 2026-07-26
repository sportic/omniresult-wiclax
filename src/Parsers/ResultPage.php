<?php

namespace Sportic\Omniresult\Wiclax\Parsers;

use Sportic\Omniresult\Common\Content\RecordContent;
use Sportic\Omniresult\Common\Models\Result;

/**
 * Class ResultPage
 * @package Sportic\Omniresult\Wiclax\Parsers
 */
class ResultPage extends AbstractParser
{
    protected $returnContent = [];

    /**
     * @inheritdoc
     */
    protected function generateContent(): array
    {
        $this->returnContent['id'] = $this->getParameter('uid');

        $params = ['record' => new Result($this->returnContent)];
        return $params;
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritdoc
     */
    protected function getContentClassName()
    {
        return RecordContent::class;
    }

    /**
     * @inheritdoc
     */
    public function getModelClassName()
    {
        return Result::class;
    }
}

<?php

namespace Sportic\Omniresult\Wiclax\RequestDetectors;

use Sportic\Omniresult\Common\RequestDetector\Detectors\AbstractSourceDetector;

/**
 * Class RequestDetector
 * @package Sportic\Omniresult\Wiclax
 */
class SourceDetector extends AbstractSourceDetector
{
    protected function doInvestigation()
    {
        $this->getResult()->setValid(false);

        $this->crawler->filter('iframe')->each(function ($node) {
            $src = $node->attr('src');
            if (strpos($src, '.clax') !== false) {
                $this->getResult()->setValid(true);
                $this->getResult()->setParams(['src' => $src]);
                $this->getResult()->setAction('event');
            }
        });
    }
}

<?php

namespace Sportic\Omniresult\Wiclax\RequestDetectors;

use Nip\Utility\Str;
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
            $this->doInvestigationIframeSrc($src);
        });
    }

    protected function doInvestigationIframeSrc($src)
    {
        if (strpos($src, '.clax') === false) {
            return;
        }
        $this->getResult()->setValid(true);
        $src = str_replace('/wp-content/glive/g-live.html?f=', '', $src);
        $src = Str::beforeLast($src, 'clax') . 'clax';
        $this->getResult()->setParams(['src' => $src]);
        $this->getResult()->setAction('event');
    }
}

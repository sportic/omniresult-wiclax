<?php

namespace Sportic\Omniresult\Wiclax;

/**
 * Class Helper
 * @package Sportic\Omniresult\Wiclax
 */
class Helper extends \Sportic\Omniresult\Common\Helper
{
    /**
     * @param $duration
     * @return float|int
     */
    public static function durationToSeconds($duration)
    {
        return $duration / 1000;
    }
}

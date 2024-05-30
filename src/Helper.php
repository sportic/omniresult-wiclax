<?php

namespace Sportic\Omniresult\Wiclax;

use Nip\Utility\Time;

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
        $duration = str_replace(
            ['h', '\'', ','],
            [':', ':', '.'],
            $duration
        );
        if (substr_count($duration, ':') < 2) {
            return 0;
        }
        return Time::fromString($duration)->getSeconds();
    }
}

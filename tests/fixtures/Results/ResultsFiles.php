<?php

namespace Sportic\Omniresult\Wiclax\Tests\Fixtures\Results;

class ResultsFiles
{
    public static function noCategories(): string
    {
        return static::getPath('one-race-no-categories.clax');
    }

    public static function multipleCategories(): string
    {
        return static::getPath('multiple-races-categories.clax');
    }

    private static function getPath(string $string): string
    {
        return __DIR__ . '/files/' . $string;
    }
}


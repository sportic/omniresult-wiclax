<?php

use Nip\Utility\Time;

function exampleIsValidEventUrl(string $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false
        && preg_match('#^https?://.+\.clax(?:\?.*)?$#i', $url) === 1;
}

function exampleFormatDuration(int|float|string|null $seconds): string
{
    if (!is_numeric($seconds) || (int) $seconds <= 0) {
        return '—';
    }

    return Time::fromSeconds((int) $seconds)->getDefaultString();
}

function exampleBuildQuery(array $parameters): string
{
    return http_build_query(
        array_filter($parameters, static fn($value) => $value !== null)
    );
}

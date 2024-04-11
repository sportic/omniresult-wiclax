<?php

require '../vendor/autoload.php';

$parameters = [
    'eventId' => '77'
];

$client = new \Sportic\Omniresult\LiniaDeSosire\LiniaDeSosireClient();
$resultsParser = $client->event($parameters);
$resultsData = $resultsParser->getContent();
$races = $resultsData->getRecords();

var_dump($resultsData->getRecord());

foreach ($races as $id => $race) {
    var_dump($race);
}

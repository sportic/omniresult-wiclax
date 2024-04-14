<?php

require '../vendor/autoload.php';

$parameters = [
    'event' => 'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax'
];

$client = new \Sportic\Omniresult\Wiclax\WiclaxClient();
$resultsParser = $client->event($parameters);
$resultsData = $resultsParser->getContent();
$races = $resultsData->getRecords();

var_dump($resultsData->getRecord());

foreach ($races as $id => $race) {
    var_dump($race);
}

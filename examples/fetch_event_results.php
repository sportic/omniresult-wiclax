<?php

require '../vendor/autoload.php';

$parameters = [
    'event' => 'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax',
    'race' => 'Cros 10k'
];

$client = new \Sportic\Omniresult\Wiclax\WiclaxClient();
$resultsParser = $client->results($parameters);
$resultsData = $resultsParser->getContent();
$results = $resultsData->getRecords();


foreach ($results as $id => $result) {
    var_dump($result);
}

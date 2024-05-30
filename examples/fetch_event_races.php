<?php

require '../vendor/autoload.php';

$parameters = [
    'event' => 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax'
];

$client = new \Sportic\Omniresult\Wiclax\WiclaxClient();
$resultsParser = $client->event($parameters);
$resultsData = $resultsParser->getContent();
$races = $resultsData->getRecords();

echo '<pre>';
var_dump($resultsData->getRecord());
echo '<br /><br />';
foreach ($races as $id => $race) {
    var_dump($race);
}

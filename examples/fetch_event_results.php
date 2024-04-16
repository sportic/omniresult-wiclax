<?php

use Nip\Utility\Time;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\WiclaxClient;

require '../vendor/autoload.php';

$parameters = [
    'event' => 'https://liniadesosire.ro/wp-content/glive-results/timisoara-sportguru-21k-2024/Timisoara%20Sportguru%2021K.clax',
    'race' => 'Cros 10k',
    'genderCategoryMerge' => '1'
];

$client = new WiclaxClient();
$resultsParser = $client->results($parameters);
$resultsData = $resultsParser->getContent();

/** @var Result[] $results */
$results = $resultsData->getRecords();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<table class="table">
    <thead>
    <tr>
        <th>#GEN</th>
        <th>#GND</th>
        <th>#CAT</th>
        <th>BIB</th>
        <th>Name</th>
        <th>Category</th>
        <th>Gender</th>
        <th>Status</th>
        <th>Time</th>
        <th>Gross Time</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $id => $result) { ?>
    <tr>
        <td>
            <?= $result->getPosGen(); ?>
        </td>
        <td>
            <?= $result->getPosGender(); ?>
        </td>
        <td>
            <?= $result->getPosCategory(); ?>
        </td>
        <td>
            <?= $result->getBib(); ?>
        </td>
        <td>
            <?= $result->getFullName(); ?>
        </td>
        <td>
            <?= $result->getCategory()?->getName(); ?>
        </td>
        <td>
            <?= $result->getGender(); ?>
        </td>
        <td>
            <?= $result->getStatus(); ?>
        </td>
        <td>
            <?= Time::fromSeconds($result->getTime())->getDefaultString(); ?>
        </td>
        <td>
            <?= Time::fromSeconds($result->getTimeGross())->getDefaultString(); ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8">
            Results: <?= count($results); ?>
        </td>
    </tr>
</table>
</body>
</html>

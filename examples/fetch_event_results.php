<?php

use Nip\Utility\Time;
use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\WiclaxClient;

require '../vendor/autoload.php';

$parameters = [
    'event' => 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax',
    'race' => 'Half 70.3 Individual',
    'genderCategoryMerge' => '1'
];

$client = new WiclaxClient();
$resultsParser = $client->results($parameters);
$resultsData = $resultsParser->getContent();

/** @var Result[] $results */
$results = $resultsData->getRecords();
$firstResult = reset($results);
$splitCollection = $firstResult->getSplits();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        <?php foreach ($splitCollection as $split) { ?>
            <th><?= $split->getName(); ?></th>
        <?php } ?>
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
                <strong>
                    <?= $result->getFirstName(); ?>
                </strong>
                <?= $result->getLastName(); ?>
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
            <?php foreach ($splitCollection as $split) { ?>
                <td>
                <span class="font-monospace bg-light border p-1" style="font-size: 80%">
                    <?= Time::fromSeconds($split->getTime())->getDefaultString(); ?>
                </span>
                    <span class="font-monospace bg-light border p-1" style="font-size: 80%">
                    <?= Time::fromSeconds($split->getTimeFromStart())->getDefaultString(); ?>
                </span>
                </td>
            <?php } ?>
            <td>
                <span class="font-monospace bg-light border p-1" style="font-size: 80%">
                    <?= Time::fromSeconds($result->getTime())->getDefaultString(); ?>
                </span>
            </td>
            <td>
                <span class="font-monospace bg-light border p-1" style="font-size: 80%">
                    <?= Time::fromSeconds($result->getTimeGross())->getDefaultString(); ?>
                </span>
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

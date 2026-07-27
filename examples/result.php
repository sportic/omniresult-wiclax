<?php
/**
 * examples/result.php
 *
 * If no parameters: show a form to enter event, race and uid values.
 * If parameters are provided: fetch the race results and display one athlete result.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use Sportic\Omniresult\Common\Models\Split;
use Sportic\Omniresult\Wiclax\WiclaxClient;

$event = trim($_GET['event'] ?? '');
$race = trim($_GET['race'] ?? '');
$uid = trim($_GET['uid'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = max(1, (int) ($_GET['perPage'] ?? 50));
$genderCategoryMerge = (int) ($_GET['genderCategoryMerge'] ?? 0) === 1 ? 1 : 0;
$error = null;
$result = null;
$exampleUrl = 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax';

if ($event !== '' || $race !== '' || $uid !== '') {
    if ($event === '' || $race === '' || $uid === '') {
        $error = 'Event URL, race name and athlete uid are required.';
    } elseif (!exampleIsValidEventUrl($event)) {
        $error = 'Invalid event URL. Please enter a valid Wiclax event file URL.';
    } else {
        try {
            $client = new WiclaxClient();
            $content = $client->results([
                'event' => $event,
                'race' => $race,
                'genderCategoryMerge' => $genderCategoryMerge,
            ])->getContent();
            $records = $content->getRecords();
            $result = $records[$uid] ?? null;

            if ($result === null) {
                $error = 'No result found for uid "' . $uid . '".';
            }
        } catch (\Throwable $exception) {
            $error = 'Failed to fetch result: ' . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wiclax – Result</title>
    <style>
        body { font-family: sans-serif; max-width: 760px; margin: 40px auto; }
        input[type=text] { width: 100%; padding: 8px; font-size: 1rem; box-sizing: border-box; }
        button { margin-top: 10px; padding: 8px 20px; font-size: 1rem; cursor: pointer; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f0f0f0; width: 40%; }
        .back { margin-bottom: 20px; display: inline-block; }
        .request-details { background: #f8f8f8; border: 1px solid #ddd; border-radius: 4px;
                           padding: 12px 16px; margin: 20px 0; font-size: 0.9rem; }
        .request-details h3 { margin: 0 0 8px; font-size: 1rem; }
        .request-details dl { margin: 0; display: grid; grid-template-columns: 120px 1fr; gap: 4px 12px; }
        .request-details dt { font-weight: bold; color: #555; }
        .request-details dd { margin: 0; word-break: break-all; }
    </style>
</head>
<body>
<h1>Wiclax – Athlete Result</h1>

<a class="back" href="<?= $event !== '' && $race !== '' ? 'race.php?' . http_build_query(array_filter([
    'event' => $event,
    'race' => $race,
    'page' => $page,
    'perPage' => $perPage,
    'genderCategoryMerge' => $genderCategoryMerge === 1 ? 1 : null,
], static fn($value) => $value !== null)) : 'index.php' ?>">
    ← Back
</a>

<?php if ($event !== '' || $race !== '' || $uid !== ''): ?>
<div class="request-details">
    <h3>Request sent by crawler</h3>
    <dl>
        <dt>Method</dt><dd>GET</dd>
        <dt>Event URL</dt><dd><a href="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?></a></dd>
        <dt>Race</dt><dd><?= htmlspecialchars($race, ENT_QUOTES, 'UTF-8') ?></dd>
        <dt>UID</dt><dd><?= htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') ?></dd>
    </dl>
</div>
<?php endif; ?>

<?php if ($result === null): ?>
<form method="get">
    <label for="event"><strong>Event file URL:</strong></label><br><br>
    <input type="text" id="event" name="event"
           placeholder="<?= htmlspecialchars($exampleUrl, ENT_QUOTES, 'UTF-8') ?>"
           value="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>">
    <br><br>
    <label for="race"><strong>Race name:</strong></label><br><br>
    <input type="text" id="race" name="race"
           placeholder="e.g. Half 70.3 Individual"
           value="<?= htmlspecialchars($race, ENT_QUOTES, 'UTF-8') ?>">
    <br><br>
    <label for="uid"><strong>Athlete UID / BIB:</strong></label><br><br>
    <input type="text" id="uid" name="uid"
           placeholder="e.g. 107"
           value="<?= htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') ?>">
    <br>
    <input type="hidden" name="page" value="<?= $page ?>">
    <input type="hidden" name="perPage" value="<?= $perPage ?>">
    <?php if ($genderCategoryMerge === 1): ?>
        <input type="hidden" name="genderCategoryMerge" value="1">
    <?php endif; ?>
    <button type="submit">Load Result</button>
</form>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($result !== null): ?>
    <h2>Result for athlete #<?= htmlspecialchars($uid, ENT_QUOTES, 'UTF-8') ?></h2>
    <table>
        <tr><th>Name</th><td><?= htmlspecialchars((string) $result->getFullName(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>BIB</th><td><?= htmlspecialchars((string) $result->getBib(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Gender</th><td><?= htmlspecialchars((string) $result->getGender(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Category</th><td><?= htmlspecialchars((string) $result->getCategory()?->getName(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Nationality</th><td><?= htmlspecialchars((string) $result->getCountry(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Position (Gen)</th><td><?= htmlspecialchars((string) $result->getPosGen(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Position (Cat)</th><td><?= htmlspecialchars((string) $result->getPosCategory(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Position (Gender)</th><td><?= htmlspecialchars((string) $result->getPosGender(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars((string) $result->getStatus(), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Net Time</th><td><?= htmlspecialchars(exampleFormatDuration($result->getTime()), ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Gun Time</th><td><?= htmlspecialchars(exampleFormatDuration($result->getTimeGross()), ENT_QUOTES, 'UTF-8') ?></td></tr>
    </table>

    <?php if (count($result->getSplits()) > 0): ?>
        <h3>Splits</h3>
        <table>
            <thead>
            <tr>
                <th>Checkpoint</th>
                <th>Time</th>
                <th>Time From Start</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result->getSplits() as $split): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $split->getName(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(exampleFormatDuration($split->getTime()), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(exampleFormatDuration($split->getTimeFromStart()), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>

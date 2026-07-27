<?php
/**
 * examples/event.php
 *
 * If no parameters: show a form to enter an event file URL.
 * If event is provided: scrape the event file and list all races.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use Sportic\Omniresult\Wiclax\WiclaxClient;

$event = trim($_GET['event'] ?? '');
$error = null;
$eventRecord = null;
$races = null;
$exampleUrl = 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax';

if ($event !== '') {
    if (!exampleIsValidEventUrl($event)) {
        $error = 'Invalid event URL. Please enter a valid Wiclax event file URL.';
        $event = '';
    } else {
        try {
            $client = new WiclaxClient();
            $content = $client->event(['event' => $event])->getContent();
            $eventRecord = $content->getRecord();
            $races = $content->getRecords();
        } catch (\Throwable $exception) {
            $error = 'Failed to fetch event: ' . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wiclax – Event</title>
    <style>
        body { font-family: sans-serif; max-width: 760px; margin: 40px auto; }
        input[type=text] { width: 100%; padding: 8px; font-size: 1rem; box-sizing: border-box; }
        button { margin-top: 10px; padding: 8px 20px; font-size: 1rem; cursor: pointer; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f0f0f0; }
        a.btn { display: inline-block; padding: 5px 14px; background: #0066cc; color: #fff;
                text-decoration: none; border-radius: 3px; font-size: 0.9rem; }
        a.btn:hover { background: #0055aa; }
        .back { margin-bottom: 20px; display: inline-block; }
        .request-details { background: #f8f8f8; border: 1px solid #ddd; border-radius: 4px;
                           padding: 12px 16px; margin: 20px 0; font-size: 0.9rem; }
        .request-details h3 { margin: 0 0 8px; font-size: 1rem; }
        .request-details dl { margin: 0; display: grid; grid-template-columns: 80px 1fr; gap: 4px 12px; }
        .request-details dt { font-weight: bold; color: #555; }
        .request-details dd { margin: 0; word-break: break-all; }
    </style>
</head>
<body>
<h1>Wiclax – Event Results</h1>

<a class="back" href="index.php">← Back to event URL entry</a>

<?php if ($event !== ''): ?>
<div class="request-details">
    <h3>Request sent by crawler</h3>
    <dl>
        <dt>Method</dt><dd>GET</dd>
        <dt>URL</dt><dd><a href="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?></a></dd>
    </dl>
</div>
<?php endif; ?>

<?php if ($races === null): ?>
<form method="get">
    <label for="event"><strong>Event file URL:</strong></label><br><br>
    <input type="text" id="event" name="event"
           placeholder="<?= htmlspecialchars($exampleUrl, ENT_QUOTES, 'UTF-8') ?>"
           value="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>">
    <br>
    <button type="submit">Load Event</button>
</form>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($races !== null): ?>
    <h2><?= htmlspecialchars((string) $eventRecord?->getName(), ENT_QUOTES, 'UTF-8') ?: 'Event' ?></h2>
    <?php if (count($races) === 0): ?>
        <p>No races found.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Race name</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $index = 1; ?>
            <?php foreach ($races as $race): ?>
                <tr>
                    <td><?= $index++ ?></td>
                    <td><?= htmlspecialchars((string) $race->getName(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="btn"
                           href="race.php?<?= exampleBuildQuery(['event' => $event, 'race' => $race->getName()]) ?>">
                            View results
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>

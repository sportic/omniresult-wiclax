<?php
/**
 * examples/race.php
 *
 * If no parameters: show a form to enter event and race details.
 * If event and race are provided: list race results with pagination controls.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

use Sportic\Omniresult\Common\Models\Result;
use Sportic\Omniresult\Wiclax\WiclaxClient;

function racePaginationUrl(int $targetPage, string $event, string $race, int $perPage, int $genderCategoryMerge): string
{
    $parameters = [
        'event' => $event,
        'race' => $race,
        'page' => $targetPage,
        'perPage' => $perPage,
    ];

    if ($genderCategoryMerge === 1) {
        $parameters['genderCategoryMerge'] = 1;
    }

    return '?' . exampleBuildQuery($parameters);
}

$event = trim($_GET['event'] ?? '');
$race = trim($_GET['race'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = max(1, (int) ($_GET['perPage'] ?? 50));
$genderCategoryMerge = (int) ($_GET['genderCategoryMerge'] ?? 0) === 1 ? 1 : 0;
$error = null;
$results = null;
$paginatedResults = [];
$splitHeaders = [];
$exampleUrl = 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax';

if ($event !== '' || $race !== '') {
    if ($event === '' || $race === '') {
        $error = 'Event URL and race name are required.';
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

            $results = array_values($content->getRecords());

            $total = count($results);
            $totalPages = max(1, (int) ceil($total / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            $paginatedResults = array_slice($results, $offset, $perPage);

            foreach ($paginatedResults as $resultItem) {
                foreach ($resultItem->getSplits() as $split) {
                    $splitHeaders[$split->getId()] = $split->getName();
                }
            }
        } catch (\Throwable $exception) {
            $error = 'Failed to fetch race results: ' . $exception->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wiclax – Race</title>
    <style>
        body { font-family: sans-serif; max-width: 1180px; margin: 40px auto; }
        input[type=text], select { padding: 6px 8px; font-size: 1rem; }
        input[type=text] { width: 100%; box-sizing: border-box; }
        button { margin-top: 10px; padding: 8px 20px; font-size: 1rem; cursor: pointer; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        a.btn { display: inline-block; padding: 4px 12px; background: #0066cc; color: #fff;
                text-decoration: none; border-radius: 3px; font-size: 0.85rem; }
        a.btn:hover { background: #0055aa; }
        a.btn.disabled { background: #aaa; pointer-events: none; }
        .back { margin-bottom: 20px; display: inline-block; }
        .request-details { background: #f8f8f8; border: 1px solid #ddd; border-radius: 4px;
                           padding: 12px 16px; margin: 20px 0; font-size: 0.9rem; }
        .request-details h3 { margin: 0 0 8px; font-size: 1rem; }
        .request-details dl { margin: 0; display: grid; grid-template-columns: 120px 1fr; gap: 4px 12px; }
        .request-details dt { font-weight: bold; color: #555; }
        .request-details dd { margin: 0; word-break: break-all; }
        .pagination { margin-top: 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .pagination-info { color: #555; font-size: 0.9rem; }
        .per-page-form { display: inline-flex; align-items: center; gap: 6px; font-size: 0.9rem; }
        .checkbox-row { margin-top: 12px; }
        .time-cell small { color: #666; font-size: 0.8rem; display: block; font-weight: bold; }
    </style>
</head>
<body>
<h1>Wiclax – Race Results</h1>

<a class="back" href="<?= $event !== '' ? 'event.php?' . http_build_query(['event' => $event]) : 'index.php' ?>">
    ← Back
</a>

<?php if ($event !== '' || $race !== ''): ?>
<div class="request-details">
    <h3>Request sent by crawler</h3>
    <dl>
        <dt>Method</dt><dd>GET</dd>
        <dt>Event URL</dt><dd><a href="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?></a></dd>
        <dt>Race</dt><dd><?= htmlspecialchars($race, ENT_QUOTES, 'UTF-8') ?></dd>
        <dt>Category merge</dt><dd><?= $genderCategoryMerge === 1 ? 'enabled' : 'disabled' ?></dd>
    </dl>
</div>
<?php endif; ?>

<?php if ($results === null): ?>
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
    <br>
    <label for="perPage"><strong>Results per page:</strong></label>
    <select id="perPage" name="perPage">
        <?php foreach ([10, 25, 50, 100] as $opt): ?>
            <option value="<?= $opt ?>" <?= $opt === $perPage ? 'selected' : '' ?>><?= $opt ?></option>
        <?php endforeach; ?>
    </select>
    <div class="checkbox-row">
        <label>
            <input type="checkbox" name="genderCategoryMerge" value="1" <?= $genderCategoryMerge === 1 ? 'checked' : '' ?>>
            Merge gender into category labels
        </label>
    </div>
    <br>
    <button type="submit">Load Race</button>
</form>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($results !== null): ?>
    <?php
    $total = count($results);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $offsetStart = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
    $offsetEnd = min($page * $perPage, $total);
    ?>
    <h2>Results for <?= htmlspecialchars($race, ENT_QUOTES, 'UTF-8') ?></h2>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a class="btn" href="<?= racePaginationUrl(1, $event, $race, $perPage, $genderCategoryMerge) ?>">« First</a>
            <a class="btn" href="<?= racePaginationUrl($page - 1, $event, $race, $perPage, $genderCategoryMerge) ?>">‹ Prev</a>
        <?php else: ?>
            <a class="btn disabled">« First</a>
            <a class="btn disabled">‹ Prev</a>
        <?php endif; ?>

        <span class="pagination-info">
            Page <?= $page ?> of <?= $totalPages ?>
            &nbsp;|&nbsp;
            Showing <?= $offsetStart ?>–<?= $offsetEnd ?> of <?= $total ?> results
        </span>

        <?php if ($page < $totalPages): ?>
            <a class="btn" href="<?= racePaginationUrl($page + 1, $event, $race, $perPage, $genderCategoryMerge) ?>">Next ›</a>
            <a class="btn" href="<?= racePaginationUrl($totalPages, $event, $race, $perPage, $genderCategoryMerge) ?>">Last »</a>
        <?php else: ?>
            <a class="btn disabled">Next ›</a>
            <a class="btn disabled">Last »</a>
        <?php endif; ?>

        <form class="per-page-form" method="get">
            <input type="hidden" name="event" value="<?= htmlspecialchars($event, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="race" value="<?= htmlspecialchars($race, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="page" value="1">
            <?php if ($genderCategoryMerge === 1): ?>
                <input type="hidden" name="genderCategoryMerge" value="1">
            <?php endif; ?>
            <label for="perPageSwitch">Per page:</label>
            <select id="perPageSwitch" name="perPage" onchange="this.form.submit()">
                <?php foreach ([10, 25, 50, 100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $opt === $perPage ? 'selected' : '' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (count($paginatedResults) === 0): ?>
        <p>No results found.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Pos Gen</th>
                <th>Pos Cat</th>
                <th>Pos Gender</th>
                <th>BIB</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <?php foreach ($splitHeaders as $splitName): ?>
                    <th><?= htmlspecialchars((string) $splitName, ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
                <th>Time</th>
                <th>Gross Time</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paginatedResults as $result): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $result->getPosGen(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $result->getPosCategory(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $result->getPosGender(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $result->getBib(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $result->getFullName(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <small style="font-weight: bold;">
                            Gender: <?= htmlspecialchars((string) $result->getGender(), ENT_QUOTES, 'UTF-8') ?>
                        </small>
                        <br>
                        <?= htmlspecialchars((string) $result->getCategory()?->getName(), ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td><?= htmlspecialchars((string) $result->getStatus(), ENT_QUOTES, 'UTF-8') ?></td>
                    <?php
                    $resultSplits = [];
                    foreach ($result->getSplits() as $split) {
                        $resultSplits[$split->getId()] = $split;
                    }
                    ?>
                    <?php foreach ($splitHeaders as $splitId => $splitName): ?>
                        <?php $split = $resultSplits[$splitId] ?? null; ?>
                        <td class="time-cell">
                            <?php if ($split !== null): ?>
                                <?= htmlspecialchars(exampleFormatDuration($split->getTime()), ENT_QUOTES, 'UTF-8') ?>
                                <small><?= htmlspecialchars(exampleFormatDuration($split->getTimeFromStart()), ENT_QUOTES, 'UTF-8') ?></small>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td><?= htmlspecialchars(exampleFormatDuration($result->getTime()), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(exampleFormatDuration($result->getTimeGross()), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($result->getId()): ?>
                            <a class="btn"
                               href="result.php?<?= exampleBuildQuery([
                                   'event' => $event,
                                   'race' => $race,
                                   'uid' => $result->getId(),
                                   'page' => $page,
                                   'perPage' => $perPage,
                                   'genderCategoryMerge' => $genderCategoryMerge === 1 ? 1 : null,
                               ]) ?>">
                                Detail
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination" style="margin-top:12px;">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= racePaginationUrl(1, $event, $race, $perPage, $genderCategoryMerge) ?>">« First</a>
                <a class="btn" href="<?= racePaginationUrl($page - 1, $event, $race, $perPage, $genderCategoryMerge) ?>">‹ Prev</a>
            <?php else: ?>
                <a class="btn disabled">« First</a>
                <a class="btn disabled">‹ Prev</a>
            <?php endif; ?>

            <span class="pagination-info">Page <?= $page ?> of <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a class="btn" href="<?= racePaginationUrl($page + 1, $event, $race, $perPage, $genderCategoryMerge) ?>">Next ›</a>
                <a class="btn" href="<?= racePaginationUrl($totalPages, $event, $race, $perPage, $genderCategoryMerge) ?>">Last »</a>
            <?php else: ?>
                <a class="btn disabled">Next ›</a>
                <a class="btn disabled">Last »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>

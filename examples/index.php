<?php
$exampleUrl = 'https://liniadesosire.ro/wp-content/glive-results/transfier-2023/Transfier%202023.clax';

require_once __DIR__ . '/helpers.php';

/**
 * examples/index.php
 *
 * Entry point: ask for a Wiclax event file URL, validate it and redirect to event.php.
 *
 * Usage: php -S localhost:8080 -t examples/
 */

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');

    if (!exampleIsValidEventUrl($url)) {
        $error = 'Invalid URL. Please enter a valid Wiclax event file URL '
            . '(e.g. ' . $exampleUrl . ')';
    } else {
        header('Location: event.php?event=' . urlencode($url));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wiclax – Event URL</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 60px auto; }
        input[type=text] { width: 100%; padding: 8px; font-size: 1rem; box-sizing: border-box; }
        button { margin-top: 10px; padding: 8px 20px; font-size: 1rem; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<h1>Wiclax Explorer</h1>
<form method="post">
    <label for="url"><strong>Event file URL:</strong></label><br><br>
    <input type="text" id="url" name="url"
           placeholder="<?= htmlspecialchars($exampleUrl, ENT_QUOTES, 'UTF-8') ?>"
           value="<?= htmlspecialchars($_POST['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <br>
    <button type="submit">Go</button>
</form>
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
</body>
</html>

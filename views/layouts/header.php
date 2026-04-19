<?php
$pageTitle = $pageTitle ?? 'Eventify';
$hideNav = $hideNav ?? false;
$flash = function_exists('getFlash') ? getFlash() : null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> | Eventify</title>
    <meta name="app-url" content="<?= h(APP_URL) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,500,0,0">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body>

<?php if (!$hideNav): ?>
<header class="navbar">
    <a class="logo" href="<?= APP_URL ?>/index.php">Event<span>ify</span></a>
    <nav>
        <a href="<?= APP_URL ?>/index.php?page=events">Events</a>
        <?php if (isLoggedIn()): ?>
            <a href="<?= APP_URL ?>/index.php?page=logout">Logout</a>
        <?php else: ?>
            <a href="<?= APP_URL ?>/index.php?page=login">Login</a>
        <?php endif; ?>
    </nav>
</header>
<?php endif; ?>

<?php if (!empty($flash) && !empty($flash['message'])): ?>
<div class="container" style="padding-top:1rem">
    <div class="alert alert-<?= h($flash['type'] ?? 'success') ?>"><?= h($flash['message']) ?></div>
</div>
<?php endif; ?>
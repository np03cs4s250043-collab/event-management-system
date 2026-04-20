if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/event-management-system');
}

if (!defined('APP_URL')) {
    define('APP_URL', BASE_PATH);
}

if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', APP_URL . '/public/uploads/');
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float|int|string $amount): string
{
    return 'Rs ' . number_format((float) $amount, 2);
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . getCsrfTokenField() . '">';
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function validateCSRF(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return true;
    }

    $sessionToken = $_SESSION[CSRF_TOKEN_KEY] ?? '';
    $inputToken = $_POST['csrf_token'] ?? $_POST['_csrf_token'] ?? '';

    return !empty($sessionToken) && !empty($inputToken) && hash_equals($sessionToken, $inputToken);
}
@Pratik
footer.php
<footer class="footer">
    <div class="footer-inner">
        <div class="logo">Eventify</div>
        <p>&copy; <?= date('Y') ?> Eventify. All rights reserved.</p>
    </div>
</footer>

<script src="<?= APP_URL ?>/public/js/search.js"></script>
</body>
</html>
header.php
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
काजी — 1:01 PM
header.php
<?php
$pageTitle = $pageTitle ?? 'Eventify';
$hideNav = $hideNav ?? false;
$currentPage = $_GET['page'] ?? 'home';
$isLoggedIn = function_exists('isLoggedIn') ? isLoggedIn() : false;
$role = $_SESSION['role'] ?? (function_exists('currentRole') ? currentRole() : 'guest');


message.txt
3 KB



footer.php
<?php $hideNav = $hideNav ?? false; ?>

<?php if (!$hideNav): ?>
    <footer class="footer">
        <div class="footer-inner">
            <div class="logo">Eventify</div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                <a href="<?= h(APP_URL) ?>/index.php">Home</a>
                <a href="<?= h(APP_URL) ?>/index.php?page=events">Events</a>
                <a href="<?= h(APP_URL) ?>/index.php?page=login">Login</a>
            </div>
            <div style="font-size:0.8rem;">&copy; <?= date('Y') ?> Eventify. All rights reserved.</div>
        </div>
    </footer>
<?php endif; ?>

<script src="<?= h(APP_URL) ?>/public/js/search.js?v=20260420"></script>
</body>
</html>
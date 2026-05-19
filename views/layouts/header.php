<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-url" content="<?= h(APP_URL) ?>">
    <title><?= h($pageTitle ?? 'Eventify') ?> - Eventify</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php if (!isset($hideNav) || !$hideNav): ?>
        <header class="navbar">
            <a href="<?= APP_URL ?>/index.php" class="logo">Event<span>ify</span></a>
            <nav class="hide-mobile">
                <a href="<?= APP_URL ?>/index.php?page=events"
                    class="<?= ($currentPage ?? '') === 'events' ? 'active' : '' ?>">Browse Events</a>
                <?php if (isLoggedIn() && currentRole() === 'organizer'): ?>
                    <a href="<?= APP_URL ?>/index.php?page=organizer/dashboard">My Dashboard</a>
                <?php elseif (isLoggedIn() && currentRole() === 'admin'): ?>
                    <a href="<?= APP_URL ?>/index.php?page=admin/dashboard">Admin</a>
                <?php elseif (isLoggedIn() && currentRole() === 'attendee'): ?>
                    <a href="<?= APP_URL ?>/index.php?page=attendee/dashboard">My Dashboard</a>
                    <a href="<?= APP_URL ?>/index.php?page=attendee/bookings">My Bookings</a>
                    <a href="mailto:support@eventify.com">Support</a>
                <?php endif; ?>
            </nav>
            <div class="actions">
                <?php if (isLoggedIn()): ?>
                    <span class="user-pill hide-mobile"><?= h($_SESSION['full_name'] ?? '') ?></span>
                    <a href="<?= APP_URL ?>/index.php?page=logout" class="btn btn-ghost-dark">Logout</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/index.php?page=login" class="btn btn-ghost-dark">Login</a>
                    <a href="<?= APP_URL ?>/index.php?page=register" class="btn btn-primary btn-sm">Sign Up Free</a>
                <?php endif; ?>
            </div>
        </header>
    <?php endif; ?>
    <?php $flash = getFlash();
    if ($flash): ?>
        <script>
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);
                requestAnimationFrame(() => toast.classList.add('show'));
                setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
            }
            showToast(<?= json_encode($flash['message']) ?>);
        </script>
    <?php endif; ?>
=======
<?php
$pageTitle = $pageTitle ?? 'Eventify';
$hideNav = $hideNav ?? false;
$currentPage = $_GET['page'] ?? 'home';
$isLoggedIn = function_exists('isLoggedIn') ? isLoggedIn() : false;
$role = $_SESSION['role'] ?? (function_exists('currentRole') ? currentRole() : 'guest');
$fullName = $_SESSION['full_name'] ?? '';
$flash = function_exists('getFlash') ? getFlash() : null;

$dashboardPath = match ($role) {
	'admin' => 'admin/dashboard',
	'organizer' => 'organizer/dashboard',
	'attendee' => 'attendee/dashboard',
	default => 'home'
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="app-url" content="<?= h(APP_URL) ?>">
	<title><?= h($pageTitle) ?> | Eventify</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0">
	<link rel="stylesheet" href="<?= h(APP_URL) ?>/public/css/style.css?v=20260420">
</head>
<body>
<?php if (!$hideNav): ?>
	<header class="navbar">
		<a class="logo" href="<?= h(APP_URL) ?>/index.php">Event<span>ify</span></a>
		<nav>
			<a href="<?= h(APP_URL) ?>/index.php" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
			<a href="<?= h(APP_URL) ?>/index.php?page=events" class="<?= $currentPage === 'events' ? 'active' : '' ?>">Events</a>
			<?php if ($isLoggedIn): ?>
				<a href="<?= h(APP_URL) ?>/index.php?page=<?= h($dashboardPath) ?>" class="<?= str_contains($currentPage, 'dashboard') ? 'active' : '' ?>">Dashboard</a>
			<?php endif; ?>
		</nav>
		<div class="actions">
			<?php if ($isLoggedIn): ?>
				<span style="color:rgba(255,255,255,0.75);font-size:0.85rem;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
					<?= h($fullName ?: 'Account') ?>
				</span>
				<a class="btn btn-primary btn-sm" href="<?= h(APP_URL) ?>/index.php?page=logout">Logout</a>
			<?php else: ?>
				<a class="btn btn-outline btn-sm" href="<?= h(APP_URL) ?>/index.php?page=login">Login</a>
				<a class="btn btn-primary btn-sm" href="<?= h(APP_URL) ?>/index.php?page=register">Register</a>
			<?php endif; ?>
		</div>
	</header>
<?php endif; ?>

<main class="page-main">

<?php if (!empty($flash['message'])): ?>
	<div class="container" style="padding-top:1rem;">
		<div class="alert alert-<?= h($flash['type'] ?? 'success') ?>">
			<?= h($flash['message']) ?>
		</div>
	</div>
<?php endif; ?>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168

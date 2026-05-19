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

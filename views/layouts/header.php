<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-url" content="<?= h(APP_URL) ?>">
    <title><?= h($pageTitle ?? 'Eventify') ?> - Eventify</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php if (!isset($hideNav) || !$hideNav): ?>
        <header class="navbar">
            <div style="display:flex;align-items:center;gap:2rem">
                <a href="<?= APP_URL ?>/index.php" class="logo">Event<span>ify</span></a>
                <nav class="hide-mobile">
                    <a href="<?= APP_URL ?>/index.php?page=events"
                        class="<?= ($currentPage ?? '') === 'events' ? 'active' : '' ?>">Events</a>
                    <?php if (isLoggedIn() && currentRole() === 'organizer'): ?>
                        <a href="<?= APP_URL ?>/index.php?page=organizer/dashboard">My Dashboard</a>
                    <?php elseif (isLoggedIn() && currentRole() === 'admin'): ?>
                        <a href="<?= APP_URL ?>/index.php?page=admin/dashboard">Admin</a>
                    <?php elseif (isLoggedIn() && currentRole() === 'attendee'): ?>
                        <a href="<?= APP_URL ?>/index.php?page=attendee/dashboard">My Dashboard</a>
                    <?php endif; ?>
                </nav>
            </div>
            <div class="actions">
                <?php if (isLoggedIn()): ?>
                    <span style="color:rgba(255,255,255,0.6);font-size:0.8rem"><?= h($_SESSION['full_name'] ?? '') ?></span>
                    <a href="<?= APP_URL ?>/index.php?page=logout" class="btn btn-outline"
                        style="color:white;border-color:rgba(255,255,255,0.2);padding:0.4rem 1rem;font-size:0.75rem">Logout</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/index.php?page=login" class="btn btn-outline"
                        style="color:white;border-color:rgba(255,255,255,0.2);padding:0.4rem 1rem;font-size:0.75rem">Login</a>
                    <a href="<?= APP_URL ?>/index.php?page=register" class="btn btn-primary btn-sm">Register</a>
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
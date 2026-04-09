<?php
// controllers/AuthController.php
declare(strict_types=1);

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/csrf_helper.php';
require_once __DIR__ . '/../core/session_helper.php';
require_once __DIR__ . '/../models/User.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'logout';

if ($action === 'login' && isPost()) {
    handleLogin();
} elseif ($action === 'register' && isPost()) {
    handleRegister();
} elseif ($action === 'logout') {
    handleLogout();
} else {
    header('Location: /ems/index.php?page=login');
    exit;
}

// ---------------------------------------------------------------

function handleLogin(): void
{
    validateCsrfToken($_POST['csrf_token'] ?? '');

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        setFlash('error', 'Email and password are required.');
        header('Location: /ems/index.php?page=login');
        exit;
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        setFlash('error', 'Invalid email or password.');
        header('Location: /ems/index.php?page=login');
        exit;
    }

    if (!$user['is_active']) {
        setFlash('error', 'Your account has been suspended. Please contact support.');
        header('Location: /ems/index.php?page=login');
        exit;
    }

    // Regenerate session ID on login (session fixation protection)
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user']    = [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ];

    // Redirect based on role
    $redirect = match($user['role']) {
        'admin'     => '/ems/index.php?page=admin.dashboard',
        'organizer' => '/ems/index.php?page=organizer.dashboard',
        default     => '/ems/index.php?page=attendee.dashboard',
    };

    header('Location: ' . $redirect);
    exit;
}

function handleRegister(): void
{
    validateCsrfToken($_POST['csrf_token'] ?? '');

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $role     = in_array($_POST['role'] ?? '', ['attendee', 'organizer', 'vendor']) ? $_POST['role'] : 'attendee';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        setFlash('error', 'Name, email, and password are required.');
        header('Location: /ems/index.php?page=register');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
        header('Location: /ems/index.php?page=register');
        exit;
    }

    if (strlen($password) < 8) {
        setFlash('error', 'Password must be at least 8 characters.');
        header('Location: /ems/index.php?page=register');
        exit;
    }

    if ($password !== $confirm) {
        setFlash('error', 'Passwords do not match.');
        header('Location: /ems/index.php?page=register');
        exit;
    }
    echo "Hello";

    $userModel = new User();

    if ($userModel->findByEmail($email)) {
        setFlash('error', 'An account with this email already exists.');
        header('Location: /ems/index.php?page=register');
        exit;
    }

    $userId = $userModel->create([
        'name'     => $name,
        'email'    => $email,
        'phone'    => $phone,
        'password' => $password,
        'role'     => $role,
    ]);

    setFlash('success', 'Account created successfully! Please log in.');
    header('Location: /ems/index.php?page=login');
    exit;
}

function handleLogout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: /ems/index.php?page=login');
    exit;
}
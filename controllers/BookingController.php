<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    public function loginPage(): void {
        $pageTitle = 'Login';
        $hideNav = true;
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) { setFlash('error', 'Invalid request.'); redirect(APP_URL . '/index.php?page=login'); }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $userModel = new User();
            $user = $userModel->login($email, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
                $dest = match($user['role']) {
                    'admin' => '/index.php?page=admin/dashboard',
                    'organizer' => '/index.php?page=organizer/dashboard',
                    default => '/index.php'
                };
                redirect(APP_URL . $dest);
            } else {
                $error = 'Invalid email or password.';
            }
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/login.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function registerPage(): void {
        $pageTitle = 'Register';
        $hideNav = true;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) { setFlash('error', 'Invalid request.'); redirect(APP_URL . '/index.php?page=register'); }
            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'attendee';

            if (!$name) $errors[] = 'Full name is required.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
            if (!$phone) $errors[] = 'Phone number is required.';
            if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
            if ($password !== $confirm) $errors[] = 'Passwords do not match.';
            if (!in_array($role, ['attendee','organizer'])) $errors[] = 'Invalid role.';

            if (empty($errors)) {
                $userModel = new User();
                if ($userModel->emailExists($email)) {
                    $errors[] = 'Email already registered.';
                } else {
                    $userModel->register($name, $email, $phone, $password, $role);
                    setFlash('success', 'Registration successful! Please login.');
                    redirect(APP_URL . '/index.php?page=login');
                }
            }
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/register.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        header('Location: ' . APP_URL . '/index.php');
        exit;
    }
}
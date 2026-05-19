<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/mail_helper.php';

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

                if (!empty($_POST['remember'])) {
                    $lifetime = 60 * 60 * 24 * 30; // 30 days
                    $_SESSION['remember_lifetime'] = $lifetime;
                    $params = session_get_cookie_params();
                    setcookie(
                        session_name(),
                        session_id(),
                        time() + $lifetime,
                        $params['path'],
                        $params['domain'],
                        $params['secure'],
                        $params['httponly']
                    );
                }

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

    public function forgotPasswordPage(): void {
        $pageTitle = 'Forgot Password';
        $hideNav   = true;
        $error     = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) {
                setFlash('error', 'Invalid request.');
                redirect(APP_URL . '/index.php?page=forgot-password');
            }

            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
                $db   = getDB();
                $stmt = $db->prepare('SELECT id, name FROM users WHERE email = ? AND is_active = 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Invalidate any previous unused tokens for this user
                    $db->prepare('UPDATE password_reset_tokens SET is_used = 1 WHERE user_id = ? AND is_used = 0')
                       ->execute([$user['id']]);

                    $otp     = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $hash    = password_hash($otp, PASSWORD_BCRYPT);
                    $expires = date('Y-m-d H:i:s', time() + 600); // 10 minutes

                    $db->prepare('INSERT INTO password_reset_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
                       ->execute([$user['id'], $hash, $expires]);

                    $sent = sendOtpEmail($email, $user['name'], $otp);

                    if ($sent) {
                        $_SESSION['pw_reset_email']   = $email;
                        $_SESSION['pw_reset_user_id'] = $user['id'];
                        setFlash('success', 'OTP sent! Check your inbox.');
                        redirect(APP_URL . '/index.php?page=verify-otp');
                    } else {
                        $error = 'Failed to send OTP. Please try again.';
                    }
                } else {
                    // Generic message to avoid user enumeration
                    setFlash('success', 'If that email is registered, an OTP has been sent.');
                    redirect(APP_URL . '/index.php?page=forgot-password');
                }
            }
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/forgot_password.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function verifyOtpPage(): void {
        $pageTitle = 'Verify OTP';
        $hideNav   = true;
        $error     = '';

        if (empty($_SESSION['pw_reset_user_id'])) {
            redirect(APP_URL . '/index.php?page=forgot-password');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) {
                setFlash('error', 'Invalid request.');
                redirect(APP_URL . '/index.php?page=verify-otp');
            }

            $otp    = trim($_POST['otp'] ?? '');
            $userId = (int) $_SESSION['pw_reset_user_id'];
            $db     = getDB();

            $stmt = $db->prepare(
                'SELECT id, token_hash FROM password_reset_tokens
                 WHERE user_id = ? AND is_used = 0 AND expires_at > NOW()
                 ORDER BY created_at DESC LIMIT 1'
            );
            $stmt->execute([$userId]);
            $record = $stmt->fetch();

            if ($record && password_verify($otp, $record['token_hash'])) {
                // Mark token used and allow password reset
                $db->prepare('UPDATE password_reset_tokens SET is_used = 1 WHERE id = ?')
                   ->execute([$record['id']]);

                $_SESSION['pw_reset_verified'] = true;
                redirect(APP_URL . '/index.php?page=reset-password');
            } else {
                $error = 'Invalid or expired OTP. Please try again.';
            }
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/verify_otp.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function resetPasswordPage(): void {
        $pageTitle = 'Reset Password';
        $hideNav   = true;
        $error     = '';

        if (empty($_SESSION['pw_reset_verified']) || empty($_SESSION['pw_reset_user_id'])) {
            redirect(APP_URL . '/index.php?page=forgot-password');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCSRF()) {
                setFlash('error', 'Invalid request.');
                redirect(APP_URL . '/index.php?page=reset-password');
            }

            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                $userId = (int) $_SESSION['pw_reset_user_id'];
                $hash   = password_hash($password, PASSWORD_BCRYPT);
                getDB()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                       ->execute([$hash, $userId]);

                unset($_SESSION['pw_reset_email'], $_SESSION['pw_reset_user_id'], $_SESSION['pw_reset_verified']);

                setFlash('success', 'Password reset successfully! Please log in.');
                redirect(APP_URL . '/index.php?page=login');
            }
        }

        require_once __DIR__ . '/../views/layouts/header.php';
        require_once __DIR__ . '/../views/auth/reset_password.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }
}

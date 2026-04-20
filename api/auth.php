<?php
/**
 * Auth API
 *
 * POST   ?action=login          — Authenticate & get token
 * POST   ?action=register       — Create new account
 * POST   ?action=logout         — Revoke current token
 * GET    ?action=check_email    — Check email availability
 * GET    ?action=me             — Get current user (requires token)
 */

$method = getRequestMethod();
$action = $_GET['action'] ?? '';

switch ($action) {

    // ── Login ───────────────────────────────────────────────
    case 'login':
        if ($method !== 'POST') jsonResponse(405, ['error' => 'Method not allowed. Use POST.']);

        $input    = jsonInput();
        $email    = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (!$email || !$password) {
            jsonResponse(422, ['error' => 'Email and password are required.']);
        }

        $userModel = new User();
        $user = $userModel->login($email, $password);

        if (!$user) {
            jsonResponse(401, ['error' => 'Invalid email or password.']);
        }

        $token = generateApiToken($user['user_id']);
        unset($user['password_hash']);

        jsonResponse(200, [
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'        => (int) $user['user_id'],
                'name'      => $user['full_name'],
                'email'     => $user['email'],
                'phone'     => $user['phone'] ?? null,
                'role'      => $user['role'],
                'is_active' => (bool) $user['is_active'],
            ]
        ]);
        break;

    // ── Register ────────────────────────────────────────────
    case 'register':
        if ($method !== 'POST') jsonResponse(405, ['error' => 'Method not allowed. Use POST.']);

        $input    = jsonInput();
        $name     = trim($input['name'] ?? '');
        $email    = trim($input['email'] ?? '');
        $phone    = trim($input['phone'] ?? '');
        $password = $input['password'] ?? '';
        $role     = $input['role'] ?? 'attendee';

        $errors = [];
        if (!$name)                                  $errors[] = 'Name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (!$phone)                                 $errors[] = 'Phone number is required.';
        if (strlen($password) < 6)                   $errors[] = 'Password must be at least 6 characters.';
        if (!in_array($role, ['attendee', 'organizer'])) $errors[] = 'Role must be attendee or organizer.';

        if (!empty($errors)) {
            jsonResponse(422, ['error' => 'Validation failed.', 'details' => $errors]);
        }

        $userModel = new User();
        if ($userModel->emailExists($email)) {
            jsonResponse(409, ['error' => 'Email already registered.']);
        }

        $userModel->register($name, $email, $phone, $password, $role);

        // Auto-login: fetch the newly created user and issue a token
        $user  = $userModel->login($email, $password);
        $token = generateApiToken($user['user_id']);

        jsonResponse(201, [
            'message' => 'Registration successful.',
            'token'   => $token,
            'user'    => [
                'id'    => (int) $user['user_id'],
                'name'  => $user['full_name'],
                'email' => $user['email'],
                'phone' => $phone,
                'role'  => $user['role'],
            ]
        ]);
        break;

    // ── Logout ──────────────────────────────────────────────
    case 'logout':
        if ($method !== 'POST') jsonResponse(405, ['error' => 'Method not allowed. Use POST.']);

        $token = getBearerToken();
        if ($token) {
            revokeApiToken($token);
        }
        jsonResponse(200, ['message' => 'Logged out successfully.']);
        break;

    // ── Check email availability ────────────────────────────
    case 'check_email':
        if ($method !== 'GET') jsonResponse(405, ['error' => 'Method not allowed. Use GET.']);

        $email = trim($_GET['email'] ?? '');
        if (!$email) {
            jsonResponse(422, ['error' => 'Email parameter is required.']);
        }

        $userModel = new User();
        jsonResponse(200, ['available' => !$userModel->emailExists($email)]);
        break;

    // ── Get current user ────────────────────────────────────
    case 'me':
        if ($method !== 'GET') jsonResponse(405, ['error' => 'Method not allowed. Use GET.']);

        $user = requireApiAuth();
        jsonResponse(200, [
            'user' => [
                'id'        => (int) $user['user_id'],
                'name'      => $user['full_name'],
                'email'     => $user['email'],
                'phone'     => $user['phone'] ?? null,
                'role'      => $user['role'],
                'is_active' => (bool) $user['is_active'],
                'created_at'=> $user['created_at'],
            ]
        ]);
        break;

    // ── Default ─────────────────────────────────────────────
    default:
        jsonResponse(400, [
            'error'   => 'Unknown action.',
            'actions' => ['login', 'register', 'logout', 'check_email', 'me']
        ]);
}
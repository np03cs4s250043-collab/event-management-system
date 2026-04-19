<?php
// Handles user management (admin) and profile actions (self)
declare(strict_types=1);

// Get request method, user ID from URL, and action
$method = $GLOBALS['_method'];
$id     = $GLOBALS['_resource_id'] !== null ? (int)$GLOBALS['_resource_id'] : null;
$action = $_GET['_action'] ?? null;

$authUser = requireAuth();

// Toggle user active status (admin only)
if ($method === 'PATCH' && $id && $action === 'toggle') {
    requireApiRole($authUser, 'admin');
    $userModel = new User();
    $target = $userModel->findById($id);

     // Check if user exists
    if (!$target) jsonError('User not found.', 404);
    $userModel->toggleActive($id);
    jsonSuccess(['message' => 'User active status toggled.', 'user_id' => $id]);
}

// Get all users (admin only)
if ($method === 'GET' && $id === null) {
    requireApiRole($authUser, 'admin');
    $userModel = new User();
    $limit  = min((int)($_GET['limit'] ?? 50), 100);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $users  = $userModel->getAll($limit, $offset);
    $total  = $userModel->countAll();

    // Strip password hashes
    $users = array_map(function($u) {
        unset($u['password_hash']);
        return $u;
    }, $users);

    jsonSuccess(['users' => $users, 'total' => $total, 'limit' => $limit, 'offset' => $offset]);
}

// Get single user (admin or self)
if ($method === 'GET' && $id !== null) {
    if ($authUser['role'] !== 'admin' && (int)$authUser['id'] !== $id) {
        jsonError('Forbidden.', 403);
    }
    $userModel = new User();
    $user = $userModel->findById($id);
    if (!$user) jsonError('User not found.', 404);
    unset($user['password_hash']);
    jsonSuccess(['user' => $user]);
}


if ($method === 'PUT' && $id !== null) {
    if ($authUser['role'] !== 'admin' && (int)$authUser['id'] !== $id) {
        jsonError('Forbidden.', 403);
    }
    $body = getJsonBody();
    $name  = trim($body['name'] ?? '');
    $phone = trim($body['phone'] ?? '');

    if (empty($name)) {
        jsonError('Name is required.', 422);
    }

    $userModel = new User();
    $target = $userModel->findById($id);
    if (!$target) jsonError('User not found.', 404);

    $userModel->update($id, ['name' => $name, 'phone' => $phone]);
    jsonSuccess(['message' => 'User updated.', 'user_id' => $id]);
}

jsonError('Method not allowed on /users', 405);

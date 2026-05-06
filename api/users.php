<?php
/**
 * Users API
 *
 * GET                            — Current user profile 
 * PUT                            — Update own profile 
 *
 * GET    ?action=list            — Get all users (admin, ?search, ?role, ?page, ?limit)
 * GET    ?action=detail&id=X    — Get single user detail (admin only)
 * POST   ?action=toggle&id=X   — Activate/deactivate user (admin only)
 */

$method = getRequestMethod();
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$userModel = new User();

if ($action) {
    switch ($action) {

        // Admin: list users
        case 'list':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $search = trim($_GET['search'] ?? '');
            $role   = $_GET['role'] ?? '';
            $page   = max(1, (int) ($_GET['page'] ?? 1));
            $limit  = min(50, max(1, (int) ($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $total = $userModel->countAll($search, $role);
            $users = $userModel->getAll($search, $role, $limit, $offset);

            // Strip password hashes
            $users = array_map(function ($u) {
                unset($u['password_hash']);
                return $u;
            }, $users);

            jsonResponse(200, [
                'users'      => $users,
                'pagination' => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => max(1, (int) ceil($total / $limit)),
                ]
            ]);
            break;

         // Get single user detail (admin only)
        case 'detail':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            if (!$id) jsonResponse(422, ['error' => 'User id is required.']);

            $target = $userModel->findById($id);
            if (!$target) jsonResponse(404, ['error' => 'User not found.']);
            unset($target['password_hash']); // Hide password

            jsonResponse(200, ['user' => $target]);
            break;

        // Toggle user active status (admin only)
        case 'toggle':
            if ($method !== 'POST') jsonResponse(405, ['error' => 'Use POST.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            // Get user ID from query
            $targetId = $id ?: (int) (jsonInput()['user_id'] ?? 0);
            if (!$targetId) jsonResponse(422, ['error' => 'User id is required.']);

            // Prevent admin from disabling themselves
            if ($targetId === (int) $user['user_id']) {
                jsonResponse(400, ['error' => 'Cannot toggle your own account.']);
            }

            $target = $userModel->findById($targetId);
            if (!$target) jsonResponse(404, ['error' => 'User not found.']);

            $userModel->toggleStatus($targetId);
            $updated = $userModel->findById($targetId);
            unset($updated['password_hash']);

            jsonResponse(200, [
                'message' => 'User status updated.',
                'user'    => $updated,
            ]);
            break;

        default:
            jsonResponse(400, ['error' => 'Unknown action.', 'actions' => ['list', 'detail', 'toggle']]);
    }
    exit;
}

switch ($method) {

    // Get current logged-in user profile
    case 'GET':
        $user = requireApiAuth();
        jsonResponse(200, [
            'user' => [
                'id'         => (int) $user['user_id'],
                'name'       => $user['full_name'],
                'email'      => $user['email'],
                'phone'      => $user['phone'] ?? null,
                'role'       => $user['role'],
                'is_active'  => (bool) $user['is_active'],
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'] ?? null,
            ]
        ]);
        break;

     // Update profile
    case 'PUT':
        $user  = requireApiAuth();
        $input = jsonInput(); // Get JSON input
        $db    = getDB();

        // Use existing values if not provided
        $name  = trim($input['name'] ?? $user['full_name']);
        $phone = trim($input['phone'] ?? ($user['phone'] ?? ''));

        // Update basic info
        $stmt = $db->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
        $stmt->execute([$name, $phone, $user['user_id']]);

        // Password update logic
        if (!empty($input['password']) && !empty($input['current_password'])) {

            // Get current password hash
            $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ?');
            $stmt->execute([$user['user_id']]);
            $hash = $stmt->fetchColumn();

            // Verify current password
            if (!password_verify($input['current_password'], $hash)) {
                jsonResponse(400, ['error' => 'Current password is incorrect.']);
            }

             // Validate new password length
            if (strlen($input['password']) < 6) {
                jsonResponse(422, ['error' => 'New password must be at least 6 characters.']);
            }

            // Hash and update new password
            $newHash = password_hash($input['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$newHash, $user['user_id']]);
        }

        // Fetch updated user
        $updated = $userModel->findById($user['user_id']);
        unset($updated['password_hash']);

        jsonResponse(200, ['message' => 'Profile updated.', 'user' => $updated]);
        break;

    // Invalid method
    default:
        jsonResponse(405, ['error' => 'Method not allowed.']);
}

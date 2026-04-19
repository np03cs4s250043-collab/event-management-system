<div class="main-content">
    <div class="top-bar"><h1>Manage Users</h1></div>
    <div class="filter-bar">
        <form action="" method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%">
            <input type="hidden" name="page" value="admin/users">
            <div class="input-icon" style="max-width:320px;flex:1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" class="form-input" placeholder="Search by name or email..." value="<?= h($search) ?>">
            </div>
            <select name="role" class="form-input form-select" style="max-width:180px">
                <option value="">All Roles</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="organizer" <?= $role === 'organizer' ? 'selected' : '' ?>>Organizer</option>
                <option value="attendee" <?= $role === 'attendee' ? 'selected' : '' ?>>Attendee</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        </form>
    </div>
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Registered</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td style="font-weight:600"><?= h($u['full_name']) ?></td>
            <td><?= h($u['email']) ?></td>
            <td><span class="status-badge" style="background:var(--surface-container-high);color:var(--on-surface);text-transform:capitalize"><?= h($u['role']) ?></span></td>
            <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
            <td><span class="status-badge status-<?= $u['is_active'] ? 'active' : 'inactive' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/toggle_user" style="display:inline">
                    <?= csrfField() ?><input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                    <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-danger' : 'btn-success' ?>"><?= $u['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($pg['total_pages'] > 1): ?>
    <div class="pagination"><?php for ($i = 1; $i <= $pg['total_pages']; $i++): ?>
        <a href="?page=admin/users&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?></div>
    <?php endif; ?>
</div>
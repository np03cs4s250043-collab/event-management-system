<div class="main-content">
    <div class="top-bar"><h1>Admin Dashboard</h1></div>

    <div class="stat-grid">
        <div class="stat-card stat-blue"><div class="stat-label">Total Users</div><div class="stat-value"><?= $stats['users'] ?></div><span class="material-symbols-outlined stat-icon">group</span></div>
        <div class="stat-card stat-purple"><div class="stat-label">Total Events</div><div class="stat-value"><?= $stats['events'] ?></div><span class="material-symbols-outlined stat-icon">event</span></div>
        <div class="stat-card stat-green"><div class="stat-label">Total Bookings</div><div class="stat-value"><?= $stats['bookings'] ?></div><span class="material-symbols-outlined stat-icon">confirmation_number</span></div>
        <div class="stat-card stat-coral"><div class="stat-label">Total Revenue</div><div class="stat-value"><?= formatPrice($stats['revenue']) ?></div><span class="material-symbols-outlined stat-icon">payments</span></div>
    </div>

    <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1rem">Pending Events (<?= count($pending) ?>)</h2>
    <?php if (empty($pending)): ?>
    <div style="text-align:center;padding:3rem;color:var(--secondary);background:white;border-radius:0.75rem">
        <span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem">check_circle</span>
        <p>No pending events to review.</p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Title</th><th>Organizer</th><th>Category</th><th>Date</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($pending as $ev): ?>
        <tr>
            <td style="font-weight:600"><?= h($ev['title']) ?></td>
            <td><?= h($ev['organizer_name']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($ev['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($ev['category']) ?></span></td>
            <td><?= date('M d, Y', strtotime($ev['event_date'])) ?></td>
            <td><?= formatPrice($ev['ticket_price']) ?></td>
            <td style="display:flex;gap:0.5rem">
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/approve" style="display:inline">
                    <?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>"><input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                </form>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/approve" style="display:inline">
                    <?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>"><input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
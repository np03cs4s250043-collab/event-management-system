<div class="main-content">
    <div class="top-bar">
        <h1>Welcome, <?= h($_SESSION['full_name'] ?? '') ?></h1>
        <a href="<?= APP_URL ?>/index.php?page=events" class="btn btn-primary btn-sm"><span class="material-symbols-outlined" style="font-size:1rem">explore</span> Browse Events</a>
    </div>

    <div class="stat-grid">
        <div class="stat-card stat-blue"><div class="stat-label">Upcoming Events</div><div class="stat-value"><?= $stats['upcoming'] ?></div><span class="material-symbols-outlined stat-icon">event</span></div>
        <div class="stat-card stat-purple"><div class="stat-label">Past Events</div><div class="stat-value"><?= $stats['past'] ?></div><span class="material-symbols-outlined stat-icon">history</span></div>
        <div class="stat-card stat-coral"><div class="stat-label">Total Spent</div><div class="stat-value"><?= formatPrice($stats['total_spent']) ?></div><span class="material-symbols-outlined stat-icon">payments</span></div>
    </div>

    <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1rem">Upcoming Bookings</h2>
    <?php if (empty($upcoming)): ?>
    <div style="text-align:center;padding:3rem;color:var(--secondary);background:white;border-radius:0.75rem">
        <span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem">event_busy</span>
        <p>No upcoming bookings. <a href="<?= APP_URL ?>/index.php?page=events" style="color:var(--primary)">Browse events</a></p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Event</th><th>Category</th><th>Date</th><th>Venue</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($upcoming as $b): ?>
        <tr>
            <td style="font-weight:600"><?= h($b['title']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($b['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($b['category']) ?></span></td>
            <td><?= date('M d', strtotime($b['event_date'])) ?></td>
            <td><?= h($b['venue']) ?></td>
            <td style="font-weight:600"><?= formatPrice($b['total_amount']) ?></td>
            <td><span class="status-badge status-<?= strtolower($b['status']) ?>"><?= h($b['status']) ?></span></td>
            <td>
                <?php if ($b['status'] === 'confirmed'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=attendee/cancel" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Cancel this booking?">Cancel</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
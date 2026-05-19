<div class="main-content">
    <div class="top-bar"><h1>My Bookings</h1></div>
    <div class="tabs">
        <a href="<?= APP_URL ?>/index.php?page=attendee/bookings&type=upcoming" class="tab <?= $type === 'upcoming' ? 'active' : '' ?>" data-booking-tab="upcoming">Upcoming</a>
        <a href="<?= APP_URL ?>/index.php?page=attendee/bookings&type=past" class="tab <?= $type === 'past' ? 'active' : '' ?>" data-booking-tab="past">Past</a>
    </div>
    <div data-bookings-content>
    <?php if (empty($bookings)): ?>
    <div style="text-align:center;padding:3rem;color:var(--secondary);background:white;border-radius:0.75rem">
        <span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem">inbox</span>
        <p>No <?= h($type) ?> bookings.</p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Event</th><th>Category</th><th>Date</th><th>Venue</th><th>Tickets</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td style="font-weight:600"><?= h($b['title']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($b['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($b['category']) ?></span></td>
            <td><?= date('M d, Y', strtotime($b['event_date'])) ?></td>
            <td><?= h($b['venue']) ?></td>
            <td><?= $b['quantity'] ?></td>
            <td style="font-weight:600"><?= formatPrice($b['total_amount']) ?></td>
            <td><span class="status-badge status-<?= strtolower($b['status']) ?>"><?= h($b['status']) ?></span></td>
            <td>
                <?php if ($type === 'upcoming' && $b['status'] === 'confirmed'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=attendee/cancel" style="display:inline">
                    <?= csrfField() ?><input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Cancel this booking?">Cancel</button>
                </form>
                <?php elseif ($type === 'past' && $b['status'] === 'confirmed'): ?>
                <a href="<?= APP_URL ?>/index.php?page=attendee/rate&event_id=<?= $b['event_id'] ?>" class="btn btn-sm" style="background:var(--surface-container-high);color:var(--on-surface)">Rate Event</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    </div>
</div>

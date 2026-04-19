<div class="main-content">
    <div class="top-bar"><h1>All Bookings</h1></div>
    <table class="data-table">
        <thead><tr><th>Ref</th><th>Event</th><th>Attendee</th><th>Category</th><th>Qty</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td style="font-weight:600;font-family:monospace"><?= h($b['booking_ref']) ?></td>
            <td><?= h($b['title']) ?></td>
            <td><?= h($b['attendee_name']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($b['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($b['category']) ?></span></td>
            <td><?= $b['quantity'] ?></td>
            <td style="font-weight:600"><?= formatPrice($b['total_amount']) ?></td>
            <td><span class="status-badge status-<?= strtolower($b['status']) ?>"><?= h($b['status']) ?></span></td>
            <td><?= date('M d, Y', strtotime($b['booked_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($pg['total_pages'] > 1): ?>
    <div class="pagination"><?php for ($i = 1; $i <= $pg['total_pages']; $i++): ?><a href="?page=admin/bookings&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?></div>
    <?php endif; ?>
</div>
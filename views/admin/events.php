<div class="main-content">
    <div class="top-bar"><h1>Manage Events</h1></div>
    <div class="filter-bar">
        <form action="" method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%">
            <input type="hidden" name="page" value="admin/events">
            <div class="input-icon" style="max-width:260px;flex:1">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" class="form-input" placeholder="Search events..." value="<?= h($search) ?>">
            </div>
            <select name="category" class="form-input form-select" style="max-width:160px">
                <option value="">All Categories</option>
                <?php foreach (['Concert','Conference','Workshop','Webinar','Sports','Festival','Exhibition','Networking','Music Events','Football','Cricket'] as $c): ?>
                <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-input form-select" style="max-width:150px">
                <option value="">All Status</option>
                <?php foreach (['draft','published','cancelled','completed'] as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        </form>
    </div>
    <table class="data-table">
        <thead><tr><th>Event</th><th>Organizer</th><th>Category</th><th>Date</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
        <tr>
            <td style="font-weight:600"><?= h($ev['title']) ?></td>
            <td><?= h($ev['organizer_name']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($ev['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($ev['category']) ?></span></td>
            <td><?= date('M d, Y', strtotime($ev['event_date'])) ?></td>
            <td><?= formatPrice($ev['ticket_price']) ?></td>
            <td><span class="status-badge status-<?= strtolower($ev['status']) ?>"><?= h($ev['status']) ?></span></td>
            <td style="display:flex;gap:0.5rem;flex-wrap:wrap">
                <?php if ($ev['status'] === 'draft'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/approve" style="display:inline"><?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>"><input type="hidden" name="action" value="approve"><button class="btn btn-success btn-sm">Approve</button></form>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/approve" style="display:inline"><?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>"><input type="hidden" name="action" value="reject"><button class="btn btn-danger btn-sm">Reject</button></form>
                <?php endif; ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/delete_event" style="display:inline"><?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>"><button class="btn btn-sm" style="background:var(--surface-container-high)" data-confirm="Delete this event?">Delete</button></form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($pg['total_pages'] > 1): ?>
    <div class="pagination"><?php for ($i = 1; $i <= $pg['total_pages']; $i++): ?><a href="?page=admin/events&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&status=<?= urlencode($status) ?>&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?></div>
    <?php endif; ?>
</div>
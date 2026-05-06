<?php $maxRev = max(array_column($revenueByEvent, 'revenue') ?: [1]); ?>
<div class="main-content">
    <div class="top-bar"><h1>Revenue Report</h1></div>

    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="stat-card stat-coral"><div class="stat-label">Total Revenue</div><div class="stat-value"><?= formatPrice($stats['revenue']) ?></div><span class="material-symbols-outlined stat-icon">payments</span></div>
        <div class="stat-card stat-green"><div class="stat-label">Total Bookings</div><div class="stat-value"><?= $stats['bookings'] ?></div><span class="material-symbols-outlined stat-icon">confirmation_number</span></div>
        <div class="stat-card stat-blue"><div class="stat-label">Total Events</div><div class="stat-value"><?= $stats['events'] ?></div><span class="material-symbols-outlined stat-icon">event</span></div>
    </div>

    <div style="background:white;border-radius:0.75rem;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:2rem">
        <h3 style="font-weight:700;margin-bottom:1.5rem">Revenue by Event</h3>
        <div style="display:flex;flex-direction:column;gap:1rem">
            <?php foreach ($revenueByEvent as $r): ?>
            <div>
                <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.35rem">
                    <span style="font-weight:600"><?= h($r['title']) ?></span>
                    <span style="font-weight:700;color:var(--primary)"><?= formatPrice($r['revenue']) ?> (<?= $r['tickets'] ?> tickets)</span>
                </div>
                <div style="width:100%;height:8px;background:var(--surface-container-low);border-radius:4px;overflow:hidden">
                    <div style="height:100%;background:linear-gradient(90deg,var(--primary),var(--primary-container));border-radius:4px;width:<?= $maxRev > 0 ? round($r['revenue'] / $maxRev * 100) : 0 ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <h3 style="font-weight:700;margin-bottom:1rem">All Transactions</h3>
    <table class="data-table">
        <thead><tr><th>Event</th><th>Tickets Sold</th><th>Revenue</th></tr></thead>
        <tbody>
        <?php foreach ($revenueByEvent as $r): ?>
        <tr>
            <td style="font-weight:600"><?= h($r['title']) ?></td>
            <td><?= $r['tickets'] ?></td>
            <td style="font-weight:700;color:var(--primary)"><?= formatPrice($r['revenue']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
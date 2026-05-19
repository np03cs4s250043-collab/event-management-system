<?php
$statusLabel = [
    'pending_admin' => ['Awaiting admin', 'background:#FEF3C7;color:#92400E'],
    'countered'     => ['Admin countered — action needed', 'background:#DBEAFE;color:#1E40AF'],
    'agreed'        => ['Agreed — event published', 'background:#D1FAE5;color:#065F46'],
    'rejected'      => ['Rejected', 'background:#FEE2E2;color:#991B1B'],
];
$fmtPct = fn($v) => rtrim(rtrim(number_format((float)$v, 2), '0'), '.');
?>
<div class="main-content">
    <div class="top-bar"><h1>My Commission Offers</h1></div>

    <?php if (empty($rows)): ?>
    <div style="text-align:center;padding:4rem;color:var(--secondary);background:white;border-radius:1rem;box-shadow:0 4px 20px rgba(0,0,0,0.03)">
        <span class="material-symbols-outlined" style="font-size:3.5rem;opacity:0.3;margin-bottom:1rem">handshake</span>
        <p style="font-size:1.05rem">No commission offers yet. <a href="<?= APP_URL ?>/index.php?page=organizer/create" style="color:var(--primary);font-weight:700;text-decoration:none">Create an event</a> to submit one.</p>
    </div>
    <?php else: ?>

    <div style="display:flex;flex-direction:column;gap:1rem">
    <?php foreach ($rows as $r): $label = $statusLabel[$r['status']] ?? [$r['status'], '']; ?>
        <div style="background:white;border-radius:0.75rem;padding:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);border:1px solid rgba(0,0,0,0.04)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem">
                <div>
                    <h3 style="font-weight:800;margin-bottom:0.25rem;font-size:1.05rem"><?= h($r['event_title']) ?></h3>
                    <div style="font-size:0.8rem;color:var(--secondary)">Ticket price: <?= formatPrice($r['ticket_price'] ?? 0) ?> · Capacity: <?= (int)$r['max_capacity'] ?></div>
                </div>
                <span style="padding:0.35rem 0.75rem;border-radius:999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;<?= $label[1] ?>"><?= h($label[0]) ?></span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1rem;padding:1rem;background:var(--surface-container-low,#f8f9fa);border-radius:0.5rem">
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Your offer</div>
                    <div style="font-size:1.25rem;font-weight:800"><?= $fmtPct($r['organizer_offer_percent']) ?>%</div>
                </div>
                <?php if ($r['admin_counter_percent'] !== null): ?>
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Admin counter</div>
                    <div style="font-size:1.25rem;font-weight:800;color:var(--primary)"><?= $fmtPct($r['admin_counter_percent']) ?>%</div>
                </div>
                <?php endif; ?>
                <?php if ($r['agreed_percent'] !== null): ?>
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Agreed</div>
                    <div style="font-size:1.25rem;font-weight:800;color:#065F46"><?= $fmtPct($r['agreed_percent']) ?>%</div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($r['organizer_note'])): ?>
            <div style="font-size:0.85rem;margin-bottom:0.5rem"><strong>Your note:</strong> <?= h($r['organizer_note']) ?></div>
            <?php endif; ?>
            <?php if (!empty($r['admin_note'])): ?>
            <div style="font-size:0.85rem;margin-bottom:0.5rem;color:var(--secondary)"><strong>Admin note:</strong> <?= h($r['admin_note']) ?></div>
            <?php endif; ?>

            <?php if ($r['status'] === 'countered'): ?>
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
                <form method="POST" action="<?= APP_URL ?>/index.php?page=organizer/commission" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="event_id" value="<?= (int)$r['event_id'] ?>">
                    <button type="submit" name="action" value="accept_counter" class="btn btn-success btn-sm">Accept <?= $fmtPct($r['admin_counter_percent']) ?>%</button>
                </form>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=organizer/commission" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="event_id" value="<?= (int)$r['event_id'] ?>">
                    <button type="submit" name="action" value="reject_counter" class="btn btn-danger btn-sm" data-confirm="Reject this counter and cancel the event?">Reject counter</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

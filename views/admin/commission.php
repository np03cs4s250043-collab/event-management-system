<?php
$statusLabel = [
    'pending_admin' => ['Awaiting your response', 'background:#FEF3C7;color:#92400E'],
    'countered'     => ['Countered — waiting on organizer', 'background:#DBEAFE;color:#1E40AF'],
    'agreed'        => ['Agreed', 'background:#D1FAE5;color:#065F46'],
    'rejected'      => ['Rejected', 'background:#FEE2E2;color:#991B1B'],
];
?>
<div class="main-content">
    <div class="top-bar"><h1>Commission Negotiations</h1></div>

    <?php if (empty($rows)): ?>
    <div style="text-align:center;padding:4rem;color:var(--secondary);background:white;border-radius:1rem;box-shadow:0 4px 20px rgba(0,0,0,0.03)">
        <span class="material-symbols-outlined" style="font-size:3.5rem;opacity:0.3;margin-bottom:1rem">handshake</span>
        <p style="font-size:1.05rem">No negotiations yet.</p>
    </div>
    <?php else: ?>

    <div style="display:flex;flex-direction:column;gap:1rem">
    <?php foreach ($rows as $r): $label = $statusLabel[$r['status']] ?? [$r['status'], '']; ?>
        <div style="background:white;border-radius:0.75rem;padding:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);border:1px solid rgba(0,0,0,0.04)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;margin-bottom:1rem">
                <div>
                    <h3 style="font-weight:800;margin-bottom:0.25rem;font-size:1.05rem"><?= h($r['event_title']) ?></h3>
                    <div style="font-size:0.85rem;color:var(--secondary)">Organizer: <strong><?= h($r['organizer_name']) ?></strong> · <?= h($r['organizer_email']) ?></div>
                    <div style="font-size:0.8rem;color:var(--secondary);margin-top:0.25rem">Ticket price: <?= formatPrice($r['ticket_price'] ?? 0) ?> · Capacity: <?= (int)$r['max_capacity'] ?></div>
                </div>
                <span style="padding:0.35rem 0.75rem;border-radius:999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;<?= $label[1] ?>"><?= h($label[0]) ?></span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1rem;padding:1rem;background:var(--surface-container-low,#f8f9fa);border-radius:0.5rem">
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Organizer offer</div>
                    <div style="font-size:1.25rem;font-weight:800"><?= h(rtrim(rtrim(number_format((float)$r['organizer_offer_percent'],2), '0'), '.')) ?>%</div>
                </div>
                <?php if ($r['admin_counter_percent'] !== null): ?>
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Your counter</div>
                    <div style="font-size:1.25rem;font-weight:800;color:var(--primary)"><?= h(rtrim(rtrim(number_format((float)$r['admin_counter_percent'],2), '0'), '.')) ?>%</div>
                </div>
                <?php endif; ?>
                <?php if ($r['agreed_percent'] !== null): ?>
                <div>
                    <div style="font-size:0.7rem;color:var(--secondary);text-transform:uppercase;letter-spacing:0.05em">Agreed</div>
                    <div style="font-size:1.25rem;font-weight:800;color:#065F46"><?= h(rtrim(rtrim(number_format((float)$r['agreed_percent'],2), '0'), '.')) ?>%</div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($r['organizer_note'])): ?>
            <div style="font-size:0.85rem;margin-bottom:0.5rem"><strong>Organizer note:</strong> <?= h($r['organizer_note']) ?></div>
            <?php endif; ?>
            <?php if (!empty($r['admin_note'])): ?>
            <div style="font-size:0.85rem;margin-bottom:0.5rem;color:var(--secondary)"><strong>Your note:</strong> <?= h($r['admin_note']) ?></div>
            <?php endif; ?>

            <?php if ($r['status'] === 'pending_admin'): ?>
            <form method="POST" action="<?= APP_URL ?>/index.php?page=admin/commission" style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:flex-end;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
                <?= csrfField() ?>
                <input type="hidden" name="event_id" value="<?= (int)$r['event_id'] ?>">
                <div style="flex:1;min-width:140px">
                    <label style="font-size:0.75rem;color:var(--secondary);display:block;margin-bottom:0.25rem">Counter %</label>
                    <input type="number" name="counter_percent" class="form-input" min="0" max="100" step="0.01" placeholder="e.g. 8" style="margin:0">
                </div>
                <div style="flex:2;min-width:180px">
                    <label style="font-size:0.75rem;color:var(--secondary);display:block;margin-bottom:0.25rem">Note (optional)</label>
                    <input type="text" name="admin_note" maxlength="255" class="form-input" style="margin:0">
                </div>
                <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept <?= h(rtrim(rtrim(number_format((float)$r['organizer_offer_percent'],2), '0'), '.')) ?>%</button>
                <button type="submit" name="action" value="counter" class="btn btn-primary btn-sm">Counter</button>
                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" data-confirm="Reject this offer and cancel the event?">Reject</button>
            </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>
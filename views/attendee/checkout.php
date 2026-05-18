<section class="container" style="padding:3rem 1.5rem">
    <div style="max-width:600px;margin:0 auto;margin-bottom:1rem">
        <a href="<?= APP_URL ?>/index.php?page=event&id=<?= $eventId ?>" style="color:var(--secondary);font-size:0.85rem;text-decoration:none">&larr; Back to Event</a>
    </div>
    <div class="checkout-grid">
        <div class="checkout-card">
            <h2 style="font-size:1.25rem;font-weight:800;margin-bottom:1.5rem">Order Summary</h2>
            <div style="display:flex;flex-direction:column;gap:1rem">
                <div style="display:flex;justify-content:space-between;font-size:0.9rem"><span style="color:var(--secondary)">Event</span><span style="font-weight:600"><?= h($event['title']) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:0.9rem"><span style="color:var(--secondary)">Date</span><span><?= date('M d, Y', strtotime($event['event_date'])) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:0.9rem"><span style="color:var(--secondary)">Time</span><span><?= date('h:i A', strtotime($event['event_time'])) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:0.9rem"><span style="color:var(--secondary)">Venue</span><span><?= h($event['venue']) ?></span></div>
                <hr style="border:none;border-top:1px solid rgba(0,0,0,0.06)">
                <div style="display:flex;justify-content:space-between;font-size:0.9rem"><span style="color:var(--secondary)">Tickets</span><span><?= $qty ?> &times; <?= formatPrice($event['ticket_price']) ?></span></div>
                <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:800;padding-top:0.5rem;border-top:2px solid rgba(0,0,0,0.06)"><span>Total</span><span style="color:var(--primary)"><?= formatPrice($total) ?></span></div>
            </div>
        </div>
        <div class="checkout-card">
            <h2 style="font-size:1.25rem;font-weight:800;margin-bottom:1.5rem">Payment</h2>

            <!-- eSewa Option -->
            <div style="padding:1rem;background:#f0fdf4;border-radius:0.5rem;display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;border:2px solid #60BB46">
                <div style="width:40px;height:40px;background:#60BB46;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <span class="material-symbols-outlined" style="color:white;font-size:1.2rem">payments</span>
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.9rem;color:#1a6b2a">Pay with eSewa</div>
                    <div style="font-size:0.75rem;color:#4a7c59">Nepal&rsquo;s #1 digital wallet &mdash; fast &amp; secure</div>
                </div>
            </div>

            <form action="<?= APP_URL ?>/index.php?page=esewa/initiate" method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                <input type="hidden" name="quantity" value="<?= $qty ?>">
                <button type="submit" style="width:100%;padding:1rem;font-size:1rem;font-weight:700;background:#60BB46;color:white;border:none;border-radius:0.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem">
                    <span class="material-symbols-outlined">open_in_new</span>
                    Pay <?= formatPrice($total) ?> via eSewa
                </button>
            </form>

            <p style="text-align:center;font-size:0.75rem;color:var(--secondary);margin-top:1rem">
                <span class="material-symbols-outlined" style="font-size:0.875rem;vertical-align:middle">lock</span>
                You will be redirected to eSewa to complete the payment securely.
            </p>
        </div>
    </div>
</section>
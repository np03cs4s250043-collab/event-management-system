<section style="display:flex;align-items:center;justify-content:center;min-height:60vh;padding:2rem">
    <div style="text-align:center;max-width:420px;width:100%">
        <div style="width:72px;height:72px;background:#60BB46;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;animation:pulse 1.5s infinite">
            <span class="material-symbols-outlined" style="font-size:2rem;color:white">payments</span>
        </div>
        <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:0.5rem">Redirecting to eSewa</h2>
        <p style="color:var(--secondary);margin-bottom:2rem;font-size:0.9rem">Please wait while we redirect you securely to eSewa to complete your payment.</p>

        <form id="esewaForm" method="POST" action="<?= EsewaHelper::getPaymentUrl() ?>">
            <?php foreach ($esewaData as $key => $value): ?>
                <input type="hidden" name="<?= h($key) ?>" value="<?= h((string)$value) ?>">
            <?php endforeach; ?>
            <button type="submit" style="background:#60BB46;border:none;color:white;padding:0.875rem 2rem;border-radius:0.5rem;font-size:1rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:0.5rem;width:100%;justify-content:center">
                <span class="material-symbols-outlined">open_in_new</span>
                Pay with eSewa
            </button>
        </form>

        <p style="font-size:0.75rem;color:var(--secondary);margin-top:1.5rem">
            <span class="material-symbols-outlined" style="font-size:0.875rem;vertical-align:middle">lock</span>
            Secured by eSewa &mdash; Amount: <?= formatPrice($esewaData['total_amount']) ?>
        </p>
    </div>
</section>
<style>
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(96,187,70,0.4); }
    50%       { box-shadow: 0 0 0 12px rgba(96,187,70,0); }
}
</style>
<script>
    setTimeout(() => document.getElementById('esewaForm').submit(), 1500);
</script>

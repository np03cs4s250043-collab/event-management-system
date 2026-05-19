<div class="auth-wrapper">
    <div class="auth-card">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">pin</span></div>
            <h2>Eventify</h2>
            <p class="subtitle">Enter OTP</p>
        </div>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <p style="font-size:.85rem;color:var(--color-text-secondary);margin:0 0 1.5rem;line-height:1.6">
            A 6-digit OTP was sent to <strong><?= h($_SESSION['pw_reset_email'] ?? 'your email') ?></strong>.
            It expires in 10 minutes.
        </p>

        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="otp">One-Time Password</label>
                <div class="input-icon">
                    <span class="material-symbols-outlined">password</span>
                    <input type="text" id="otp" name="otp" class="form-input"
                        placeholder="123456" maxlength="6" pattern="\d{6}"
                        autocomplete="one-time-code" required
                        style="letter-spacing:.35rem;font-size:1.25rem;font-weight:700">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"
                style="width:100%;padding:1rem;font-family:'Plus Jakarta Sans';font-size:1rem">
                Verify OTP
            </button>
        </form>

        <div style="text-align:center;margin-top:1.25rem">
            <a href="<?= APP_URL ?>/index.php?page=forgot-password"
                style="font-size:.8rem;color:var(--color-text-secondary);text-decoration:none">
                Didn't receive it? Resend OTP
            </a>
        </div>
        <div style="text-align:center;margin-top:.75rem">
            <a href="<?= APP_URL ?>/index.php?page=login"
                style="font-size:.8rem;color:var(--color-text-secondary);text-decoration:none">
                &larr; Back to Login
            </a>
        </div>
    </div>
</div>

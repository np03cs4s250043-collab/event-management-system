<div class="auth-wrapper">
    <div class="auth-card">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">lock_reset</span></div>
            <h2>Eventify</h2>
            <p class="subtitle">Forgot Password</p>
        </div>

        <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <p style="font-size:.85rem;color:var(--color-text-secondary);margin:0 0 1.5rem;line-height:1.6">
            Enter your registered email address and we'll send you a 6-digit OTP to reset your password.
        </p>

        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <span class="material-symbols-outlined">mail</span>
                    <input type="email" id="email" name="email" class="form-input"
                        placeholder="hello@eventify.com" required
                        value="<?= h($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"
                style="width:100%;padding:1rem;font-family:'Plus Jakarta Sans';font-size:1rem">
                Send OTP
            </button>
        </form>

        <div style="text-align:center;margin-top:1.5rem">
            <a href="<?= APP_URL ?>/index.php?page=login"
                style="font-size:.8rem;color:var(--color-text-secondary);text-decoration:none">
                &larr; Back to Login
            </a>
        </div>
    </div>
</div>

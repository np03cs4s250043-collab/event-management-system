<div class="auth-wrapper">
    <div class="auth-card">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">lock_open</span></div>
            <h2>Eventify</h2>
            <p class="subtitle">Set New Password</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <p style="font-size:.85rem;color:var(--color-text-secondary);margin:0 0 1.5rem;line-height:1.6">
            Choose a strong new password for your account.
        </p>

        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-icon password-field">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" id="password" name="password" class="form-input"
                        placeholder="••••••••••" minlength="6" required>
                    <button type="button" class="password-toggle" data-toggle-password data-target="password"
                        aria-label="Show password">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-icon password-field">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                        placeholder="••••••••••" minlength="6" required>
                    <button type="button" class="password-toggle" data-toggle-password data-target="confirm_password"
                        aria-label="Show password">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"
                style="width:100%;padding:1rem;font-family:'Plus Jakarta Sans';font-size:1rem">
                Reset Password
            </button>
        </form>
    </div>
</div>

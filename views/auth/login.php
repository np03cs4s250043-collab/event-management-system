<div class="auth-wrapper">
    <div class="auth-card">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">confirmation_number</span></div>
            <h2>Eventify</h2>
            <p class="subtitle">Welcome Back</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <span class="material-symbols-outlined">mail</span>
                    <input type="email" id="email" name="email" class="form-input" placeholder="name@company.com"
                        required value="<?= h($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <label for="password">Password</label>
                    <a href="#" class="frogot-password" style="color:var(--primary);text-decoration:none;">Forgot
                        Password?</a>
                </div>
                <div class="input-icon password-field">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                        required>
                    <button type="button" class="password-toggle" data-toggle-password data-target="password"
                        aria-label="Show password">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
            </div>
            <div style="display:inline-flex;align-items:center;gap:0 6px;">
                <input type="checkbox" style="accent-color: var(--primary-container);cursor:pointer;">
                <label style="font-size: 0.8rem;letter-spacing:-0.01em;line-height:1.5;font-weight:500;color:var(--on-surface);">Keep me signed in</label>
            </div>
            <div style="margin:1.25rem 0">
                <button type="submit" class="btn btn-primary" style="width:100%;padding:0.875rem">Login</button>
            </div>
        </form>
        <div style="text-align:center;margin-top:2rem">
            <p style="font-size:0.875rem;color:var(--secondary)">New to the premiere? <a
                    href="<?= APP_URL ?>/index.php?page=register"
                    style="color:var(--primary);font-weight:700;text-decoration:none">Register</a></p>
        </div>
        <div style="text-align:center;margin-top:1rem">
            <a href="<?= APP_URL ?>/index.php"
                style="font-size:0.75rem;color:var(--secondary);text-decoration:none">&larr; Back to Homepage</a>
        </div>
    </div>
</div>
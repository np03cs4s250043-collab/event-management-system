<div class="auth-wrapper">
    <div class="auth-card">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">confirmation_number</span></div>
            <h2>Eventify</h2>
            <p class="subtitle">Welcome Back</p>
        </div>
        <div class="auth-tabs">
            <a href="<?= APP_URL ?>/index.php?page=login" class="active">Login</a>
            <a href="<?= APP_URL ?>/index.php?page=register">Register</a>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <span class="material-symbols-outlined">mail</span>
                    <input type="email" id="email" name="email" class="form-input" placeholder="hello@eventify.com"
                        required value="<?= h($_POST['email'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.375rem">
                    <label for="password" style="margin-bottom:0">Password</label>
                    <a href="#" style="color:var(--color-coral);font-weight:600;font-size:.75rem;text-decoration:none">Forgot password?</a>
                </div>
                <div class="input-icon password-field">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" id="password" name="password" class="form-input" placeholder="••••••••••"
                        required>
                    <button type="button" class="password-toggle" data-toggle-password data-target="password"
                        aria-label="Show password">
                        <span class="material-symbols-outlined">visibility</span>
                    </button>
                </div>
            </div>
            <label style="display:flex;align-items:center;gap:.625rem;margin-bottom:1.25rem;cursor:pointer">
                <input type="checkbox" id="remember" name="remember" value="1" style="width:16px;height:16px;accent-color:var(--color-crimson);cursor:pointer">
                <span style="font-size:.8rem;color:var(--color-text-secondary);font-weight:500">Keep me signed in</span>
            </label>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:1rem;font-family:'Plus Jakarta Sans';font-size:1rem">Sign In</button>
        </form>
        <div style="text-align:center;margin-top:1.5rem">
            <p style="font-size:.8rem;color:var(--color-text-secondary)">Don't have an account?
                <a href="<?= APP_URL ?>/index.php?page=register"
                    style="color:var(--color-crimson);font-weight:700;text-decoration:none">Sign Up</a>
            </p>
        </div>
        <div style="text-align:center;margin-top:.75rem">
            <a href="<?= APP_URL ?>/index.php"
                style="font-size:.7rem;color:var(--color-text-secondary);text-decoration:none">&larr; Back to Homepage</a>
        </div>
    </div>
</div>

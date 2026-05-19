<div class="auth-wrapper">
    <div class="auth-card" style="max-width:520px">
        <div class="brand">
            <div class="brand-icon"><span class="material-symbols-outlined">confirmation_number</span></div>
            <h2>Eventify</h2>
<<<<<<< HEAD
            <p class="subtitle">Create your account</p>
        </div>
        <div class="auth-tabs">
            <a href="<?= APP_URL ?>/index.php?page=login">Login</a>
            <a href="<?= APP_URL ?>/index.php?page=register" class="active">Register</a>
        </div>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error"><?= implode('<br>', array_map('h', $errors)) ?></div>
        <?php endif; ?>
=======
            <p class="subtitle">Create Your Account</p>
        </div>
        <?php if (!empty($errors)): ?><div class="alert alert-error"><?= implode('<br>', array_map('h', $errors)) ?></div><?php endif; ?>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Full Name</label>
<<<<<<< HEAD
                <div class="input-icon">
                    <span class="material-symbols-outlined">person</span>
                    <input type="text" name="full_name" class="form-input" placeholder="Alex Rivera" required value="<?= h($_POST['full_name'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-icon">
                    <span class="material-symbols-outlined">mail</span>
                    <input type="email" name="email" class="form-input" placeholder="hello@eventify.com" required data-email-check value="<?= h($_POST['email'] ?? '') ?>">
                </div>
=======
                <div class="input-icon"><span class="material-symbols-outlined">person</span>
                <input type="text" name="full_name" class="form-input" placeholder="John Doe" required value="<?= h($_POST['full_name'] ?? '') ?>"></div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-icon"><span class="material-symbols-outlined">mail</span>
                <input type="email" name="email" class="form-input" placeholder="name@company.com" required data-email-check value="<?= h($_POST['email'] ?? '') ?>"></div>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
                <div class="email-feedback"></div>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
<<<<<<< HEAD
                <div class="input-icon">
                    <span class="material-symbols-outlined">phone</span>
                    <input type="text" name="phone" class="form-input" placeholder="+977 98XXXXXXXX" required value="<?= h($_POST['phone'] ?? '') ?>">
                </div>
=======
                <div class="input-icon"><span class="material-symbols-outlined">phone</span>
                <input type="text" name="phone" class="form-input" placeholder="98XXXXXXXX" required value="<?= h($_POST['phone'] ?? '') ?>"></div>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon password-field">
                        <span class="material-symbols-outlined">lock</span>
<<<<<<< HEAD
                        <input type="password" id="register_password" name="password" class="form-input" placeholder="Min 6 chars" required>
=======
                        <input type="password" id="register_password" name="password" class="form-input" placeholder="Min 8 chars, include number &amp; special char" minlength="8" required value="<?= h($_POST['password'] ?? '') ?>">
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
                        <button type="button" class="password-toggle" data-toggle-password data-target="register_password" aria-label="Show password">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-icon password-field">
                        <span class="material-symbols-outlined">lock</span>
<<<<<<< HEAD
                        <input type="password" id="register_confirm_password" name="confirm_password" class="form-input" placeholder="Re-enter" required>
=======
                        <input type="password" id="register_confirm_password" name="confirm_password" class="form-input" placeholder="Re-enter password" required value="<?= h($_POST['confirm_password'] ?? '') ?>">
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
                        <button type="button" class="password-toggle" data-toggle-password data-target="register_confirm_password" aria-label="Show confirm password">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>I am a...</label>
                <select name="role" class="form-input form-select">
                    <option value="attendee" <?= ($_POST['role'] ?? '') === 'attendee' ? 'selected' : '' ?>>Attendee</option>
                    <option value="organizer" <?= ($_POST['role'] ?? '') === 'organizer' ? 'selected' : '' ?>>Event Organizer</option>
                </select>
            </div>
<<<<<<< HEAD
            <label style="display:flex;align-items:flex-start;gap:.625rem;margin:0 0 1.25rem;cursor:pointer">
                <input type="checkbox" required style="width:16px;height:16px;margin-top:2px;accent-color:var(--color-crimson);cursor:pointer">
                <span style="font-size:.75rem;color:var(--color-text-secondary);line-height:1.6">I agree to the <span style="color:var(--color-crimson);font-weight:600">Terms of Service</span> and <span style="color:var(--color-crimson);font-weight:600">Privacy Policy</span></span>
            </label>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:1rem;font-family:'Plus Jakarta Sans';font-size:1rem">Create Account</button>
        </form>
        <div style="text-align:center;margin-top:1.5rem">
            <p style="font-size:.8rem;color:var(--color-text-secondary)">Already have an account?
                <a href="<?= APP_URL ?>/index.php?page=login" style="color:var(--color-crimson);font-weight:700;text-decoration:none">Login</a>
            </p>
=======
            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.875rem;margin-top:0.5rem">Register</button>
        </form>
        <div style="text-align:center;margin-top:2rem">
            <p style="font-size:0.875rem;color:var(--secondary)">Already have an account? <a href="<?= APP_URL ?>/index.php?page=login" style="color:var(--primary);font-weight:700;text-decoration:none">Login</a></p>
        </div>
        <div style="text-align:center;margin-top:1rem">
            <a href="<?= APP_URL ?>/index.php" style="font-size:0.75rem;color:var(--secondary);text-decoration:none">&larr; Back to Homepage</a>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
        </div>
    </div>
</div>

<div class="main-content">
    <div class="top-bar"><h1>My Profile</h1></div>
    <div style="max-width:480px;background:white;border-radius:0.75rem;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04)">
        <?php if ($user): ?>
        <div style="display:flex;flex-direction:column;gap:1.25rem">
            <div>
                <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(93,92,116,0.8)">Full Name</label>
                <p style="font-size:1rem;font-weight:600;margin-top:0.25rem"><?= h($user['full_name']) ?></p>
            </div>
            <div>
                <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(93,92,116,0.8)">Email</label>
                <p style="font-size:1rem;margin-top:0.25rem"><?= h($user['email']) ?></p>
            </div>
            <div>
                <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(93,92,116,0.8)">Phone</label>
                <p style="font-size:1rem;margin-top:0.25rem"><?= h($user['phone']) ?></p>
            </div>
            <div>
                <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(93,92,116,0.8)">Role</label>
                <p style="font-size:1rem;margin-top:0.25rem;text-transform:capitalize"><?= h($user['role']) ?></p>
            </div>
            <div>
                <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:rgba(93,92,116,0.8)">Member Since</label>
                <p style="font-size:1rem;margin-top:0.25rem"><?= date('F d, Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
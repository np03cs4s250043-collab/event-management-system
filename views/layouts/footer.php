<?php $hideNav = $hideNav ?? false; ?>
</main>
<?php if (!$hideNav): ?>
    <footer class="footer">
        <div class="footer-inner">
            <div class="logo">Eventify</div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                <a href="<?= h(APP_URL) ?>/index.php">Home</a>
                <a href="<?= h(APP_URL) ?>/index.php?page=events">Events</a>
                <a href="<?= h(APP_URL) ?>/index.php?page=login">Login</a>
            </div>
            <div style="font-size:0.8rem;">&copy; <?= date('Y') ?> Eventify. All rights reserved.</div>
        </div>
    </footer>
<?php endif; ?>

<script src="<?= h(APP_URL) ?>/public/js/search.js?v=20260420"></script>
</body>
</html>
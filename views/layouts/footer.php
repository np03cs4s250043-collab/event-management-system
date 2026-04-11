<?php if (!isset($hideNav) || !$hideNav): ?>
<footer class="footer">
    <div class="footer-inner">
        <div>
            <div class="logo">Eventify</div>
            <p style="font-size:0.75rem;margin-top:0.5rem">Discover. Book. Experience.</p>
        </div>
        <div style="display:flex;gap:2rem">
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#">Privacy Policy</a>
        </div>
        <p style="font-size:0.7rem">&copy; <?= date('Y') ?> Eventify. Herald College Kathmandu.</p>
    </div>
</footer>
<?php endif; ?>
<script src="<?= APP_URL ?>/public/js/search.js"></script>
</body>
</html>

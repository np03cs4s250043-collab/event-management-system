<?php if (!isset($hideNav) || !$hideNav): ?>
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-cols">
            <div class="brand-col">
                <a href="<?= APP_URL ?>/index.php" class="logo">Eventify</a>
                <p>The Cinematic Curator. Transforming how you discover and experience the best moments in your city.</p>
                <div class="socials">
                    <a href="#" aria-label="Twitter">𝕏</a>
                    <a href="#" aria-label="LinkedIn">in</a>
                    <a href="#" aria-label="Instagram">ig</a>
                </div>
            </div>
            <div>
                <h5>Navigation</h5>
                <ul>
                    <li><a href="<?= APP_URL ?>/index.php?page=events">Explore Events</a></li>
                    <li><a href="<?= APP_URL ?>/index.php?page=events&category=Concert">Concerts</a></li>
                    <li><a href="<?= APP_URL ?>/index.php?page=events&category=Football">Sports</a></li>
                    <li><a href="<?= APP_URL ?>/index.php?page=events&category=VIP+Event">VIP Access</a></li>
                </ul>
            </div>
            <div>
                <h5>Support</h5>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Refund Policy</a></li>
                </ul>
            </div>
            <div>
                <h5>Contact</h5>
                <ul class="contact">
                    <li><a href="mailto:hello@eventify.com">hello@eventify.com</a></li>
                    <li><a href="#" style="color: rgba(255,255,255,.42); font-weight:400">24/7 Concierge</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bar">
            <span>Copyright &copy; <?= date('Y') ?> Eventify. Herald College Kathmandu.</span>
            <span>Cookie Policy &middot; Accessibility</span>
        </div>
    </div>
</footer>
<?php endif; ?>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.event-card').forEach((el, i) => {
            el.setAttribute('data-aos', 'fade-up');
            el.setAttribute('data-aos-delay', (i % 4) * 100);
        });
        document.querySelectorAll('.stat-card').forEach((el, i) => {
            el.setAttribute('data-aos', 'zoom-in');
            el.setAttribute('data-aos-delay', (i % 4) * 100);
        });
        document.querySelectorAll('.hero h1').forEach(el => el.setAttribute('data-aos', 'fade-down'));
        document.querySelectorAll('.hero p.lead').forEach(el => { el.setAttribute('data-aos', 'fade-down'); el.setAttribute('data-aos-delay', '100'); });
        document.querySelectorAll('.search-bar, .category-pills, .filter-bar').forEach(el => { el.setAttribute('data-aos', 'fade-up'); el.setAttribute('data-aos-delay', '200'); });
        document.querySelectorAll('.auth-card, .confirm-card, .checkout-card, .booking-card').forEach(el => el.setAttribute('data-aos', 'zoom-in'));
        document.querySelectorAll('.data-table tbody tr, .review-item').forEach((el, i) => { el.setAttribute('data-aos', 'fade-up'); el.setAttribute('data-aos-delay', (i % 10) * 50); });
        AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });
    });
</script>
<script src="<?= APP_URL ?>/public/js/search.js?v=<?= filemtime(BASE_PATH . '/public/js/search.js') ?>"></script>

<!-- Eventify Chatbot -->
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/chatbot.css?v=<?= filemtime(BASE_PATH . '/public/css/chatbot.css') ?>">
<script>
    window.EMS_USER = {
        id: <?= isLoggedIn() ? (int) currentUserId() : 'null' ?>,
        role: <?= json_encode(currentRole()) ?>,
        name: <?= json_encode($_SESSION['full_name'] ?? '') ?>
    };
</script>
<script src="<?= APP_URL ?>/public/js/chatbot.js?v=<?= filemtime(BASE_PATH . '/public/js/chatbot.js') ?>" defer></script>
</body>
</html>

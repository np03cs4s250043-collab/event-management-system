<section class="hero">
    <span class="blob blob-1"></span>
    <span class="blob blob-2"></span>
    <span class="blob blob-3"></span>
    <div class="hero-bg"></div>
    <div class="hero-inner">
        <div class="live-badge">
            <span class="dot"></span>
            <span>LIVE EVENTS NEAR YOU</span>
        </div>
        <h1>Discover Events<br><span class="accent">Near You</span></h1>
        <p class="lead">Discover. Book. Experience.</p>
        <form class="search-bar" action="<?= APP_URL ?>/index.php" method="GET" data-events-search-form data-live-limit="6">
            <input type="hidden" name="page" value="events">
            <input type="text" name="search" placeholder="Search concerts, sports, festivals..." data-events-search>
            <input type="hidden" name="category" value="">
            <div class="autocomplete"></div>
            <button type="submit">Find Events</button>
        </form>
        <p data-search-meta style="margin-top:.75rem;color:rgba(255,255,255,.55);font-size:.8rem;min-height:1.1rem"></p>
        <div class="hero-stats">
            <div class="stat"><div class="val">50k+</div><div class="lbl">Happy Attendees</div></div>
            <div class="stat"><div class="val">412</div><div class="lbl">Live Events</div></div>
            <div class="stat"><div class="val">4.9★</div><div class="lbl">Avg Rating</div></div>
        </div>
    </div>
</section>

<div class="category-pills">
    <a href="<?= APP_URL ?>/index.php?page=events" class="pill active" data-category-filter="">All</a>
    <a href="<?= APP_URL ?>/index.php?page=events&category=Concert" class="pill" data-category-filter="Concert">Concert</a>
    <a href="<?= APP_URL ?>/index.php?page=events&category=Football" class="pill" data-category-filter="Football">Football</a>
    <a href="<?= APP_URL ?>/index.php?page=events&category=Cricket" class="pill" data-category-filter="Cricket">Cricket</a>
    <a href="<?= APP_URL ?>/index.php?page=events&category=Music+Event" class="pill" data-category-filter="Music Event">Music Event</a>
    <a href="<?= APP_URL ?>/index.php?page=events&category=VIP+Event" class="pill" data-category-filter="VIP Event">VIP Event</a>
</div>

<section class="container" style="padding:4rem 2rem 5rem">
    <div class="section-heading">
        <div>
            <div class="section-tag">UPCOMING</div>
            <h2>Upcoming Experiences</h2>
            <p>Handpicked events trending in your city</p>
        </div>
        <a href="<?= APP_URL ?>/index.php?page=events" class="btn btn-outline btn-sm">View All Events &rarr;</a>
    </div>
    <div class="event-grid" data-event-grid>
        <?php if (empty($events)): ?>
        <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--color-text-secondary)">
            <span class="material-symbols-outlined" style="font-size:3.5rem;display:block;margin-bottom:1rem">search_off</span>
            <h3>No events found</h3>
            <p>Try a different search keyword.</p>
        </div>
        <?php else: foreach ($events as $ev): ?>
        <div class="event-card" data-event-card-link data-event-link="<?= APP_URL ?>/index.php?page=event&id=<?= $ev['event_id'] ?>">
            <div class="card-img" style="<?= $ev['cover_image'] ? "background-image:url(" . UPLOAD_URL . h($ev['cover_image']) . ")" : '' ?>">
                <?php if (!$ev['cover_image']): ?>
                <div class="empty-ph">
                    <span class="material-symbols-outlined" style="font-size:3rem">image</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <span class="badge" style="background:var(--<?= match($ev['category']) { 'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary' } ?>)"><?= h($ev['category']) ?></span>
                <h3><?= h($ev['title']) ?></h3>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:.9rem">calendar_today</span><?= date('M d, Y', strtotime($ev['event_date'])) ?></div>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:.9rem">location_on</span><?= h($ev['venue']) ?></div>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:.9rem">event_seat</span><?= $ev['available_seats'] ?> seats left</div>
            </div>
            <div class="card-footer">
                <span class="price"><?= formatPrice($ev['ticket_price']) ?></span>
                <a href="<?= APP_URL ?>/index.php?page=event&id=<?= $ev['event_id'] ?>" class="btn btn-primary btn-sm">View Details</a>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>
</section>

<section class="feature-strip">
    <div class="inner">
        <div class="feature">
            <div class="ico">🎟</div>
            <h4>Instant Booking</h4>
            <p>Skip the queues. Book tickets in under 60 seconds.</p>
        </div>
        <div class="feature">
            <div class="ico">🔒</div>
            <h4>Secure Payments</h4>
            <p>Bank-grade encryption. Your money is safe with us.</p>
        </div>
        <div class="feature">
            <div class="ico">📩</div>
            <h4>E-Ticket Delivery</h4>
            <p>Tickets delivered instantly to your inbox.</p>
        </div>
    </div>
</section>

<section class="newsletter">
    <div class="inner">
        <h2>Never Miss a Premiere</h2>
        <p>Join 50k+ enthusiasts and get the best event recommendations directly in your inbox.</p>
        <form onsubmit="event.preventDefault(); alert('Subscribed — we\'ll be in touch.');">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
</section>

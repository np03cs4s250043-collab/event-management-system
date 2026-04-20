<section class="hero">
    <h1>Discover Events Near You</h1>
    <p>Discover. Book. Experience.</p>
    <form class="search-bar" action="<?= APP_URL ?>/index.php" method="GET">
        <input type="hidden" name="page" value="events">
        <input type="text" name="search" placeholder="Search for concerts, sports, festivals..." data-autocomplete>
        <div class="autocomplete"></div>
        <button type="submit"><span class="material-symbols-outlined">search</span></button>
    </form>
    <div class="category-pills" style="margin-top:2rem">
        <a href="<?= APP_URL ?>/index.php?page=events" class="pill pill-all active">All Events</a>
        <a href="<?= APP_URL ?>/index.php?page=events&category=Concert" class="pill pill-concert">Concerts</a>
        <a href="<?= APP_URL ?>/index.php?page=events&category=Music+Event" class="pill pill-music">Music Events</a>
        <a href="<?= APP_URL ?>/index.php?page=events&category=Football" class="pill pill-football">Football</a>
        <a href="<?= APP_URL ?>/index.php?page=events&category=Cricket" class="pill pill-cricket">Cricket</a>
    </div>
</section>

<section class="container" style="padding:3rem 1.5rem">
    <h2 style="font-size:1.75rem;font-weight:800;margin-bottom:0.5rem">Upcoming Events</h2>
    <p style="color:var(--secondary);margin-bottom:2rem">Don't miss out on the hottest events in Nepal</p>
    <div class="event-grid">
        <?php foreach ($events as $ev): ?>
        <div class="event-card">
            <div class="card-img" style="<?= $ev['cover_image'] ? "background-image:url(" . UPLOAD_URL . h($ev['cover_image']) . ");background-size:cover;background-position:center" : '' ?>">
                <?php if (!$ev['cover_image']): ?>
                <div style="height:100%;display:flex;align-items:center;justify-content:center">
                    <span class="material-symbols-outlined" style="font-size:3rem;color:rgba(255,255,255,0.3)">image</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <span class="badge" style="background:var(--<?= match($ev['category']) { 'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary' } ?>)"><?= h($ev['category']) ?></span>
                <h3><?= h($ev['title']) ?></h3>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">calendar_today</span> <?= date('M d, Y', strtotime($ev['event_date'])) ?></div>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">location_on</span> <?= h($ev['venue']) ?></div>
                <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">event_seat</span> <?= $ev['available_seats'] ?> seats left</div>
            </div>
            <div class="card-footer">
                <span class="price"><?= formatPrice($ev['ticket_price']) ?></span>
                <a href="<?= APP_URL ?>/index.php?page=event&id=<?= $ev['event_id'] ?>" class="btn btn-primary btn-sm">View Details</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center;padding:2rem 0">
        <a href="<?= APP_URL ?>/index.php?page=events" class="btn btn-outline">Browse All Events &rarr;</a>
    </div>
</section>
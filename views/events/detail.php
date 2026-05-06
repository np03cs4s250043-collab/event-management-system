<?php if (isset($events) && !isset($event)): ?>
<!-- Browse Events listing -->
<section style="background:var(--surface-container-low);padding:2rem 0">
<div class="container">
    <h1 style="font-size:2rem;font-weight:800;margin-bottom:0.5rem">Browse Events</h1>
    <p style="color:var(--secondary);margin-bottom:1.5rem">Find your next unforgettable experience</p>

    <div class="filter-bar" data-search-focus>
        <form action="" method="GET" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;width:100%" data-events-search-form>
            <input type="hidden" name="page" value="events">
            <div class="input-icon search-input-wrap" style="flex:1;min-width:200px">
                <span class="material-symbols-outlined">manage_search</span>
                <input type="text" name="search" class="form-input" placeholder="Search by title, description, or venue" value="<?= h($search) ?>" data-events-search>
            </div>
            <input type="hidden" name="category" value="<?= h($category) ?>">
        </form>
    </div>

    <div class="category-pills">
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>" class="pill pill-all <?= !$category ? 'active' : '' ?>" data-category-filter="">All</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Concert" class="pill pill-concert <?= $category === 'Concert' ? 'active' : '' ?>" data-category-filter="Concert">Concerts</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Music+Event" class="pill pill-music <?= $category === 'Music Event' ? 'active' : '' ?>" data-category-filter="Music Event">Music</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Football" class="pill pill-football <?= $category === 'Football' ? 'active' : '' ?>" data-category-filter="Football">Football</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Cricket" class="pill pill-cricket <?= $category === 'Cricket' ? 'active' : '' ?>" data-category-filter="Cricket">Cricket</a>
    </div>
</div>
</section>

<section class="container" style="padding:2rem 1.5rem">
    <div class="event-grid" data-event-grid>
        <?php if (empty($events)): ?>
        <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--secondary)">
            <span class="material-symbols-outlined" style="font-size:3.5rem;display:block;margin-bottom:1rem">search_off</span>
            <h3>No events found</h3>
            <p>Try adjusting your search or filter criteria.</p>
        </div>
        <?php else: foreach ($events as $ev): ?>
        <div class="event-card" data-event-card-link data-event-link="<?= APP_URL ?>/index.php?page=event&id=<?= $ev['event_id'] ?>">
            <div class="card-img" style="<?= $ev['cover_image'] ? "background-image:url(" . UPLOAD_URL . h($ev['cover_image']) . ");background-size:cover;background-position:center" : '' ?>">
                <?php if (!$ev['cover_image']): ?><div style="height:100%;display:flex;align-items:center;justify-content:center"><span class="material-symbols-outlined" style="font-size:3rem;color:rgba(255,255,255,0.3)">image</span></div><?php endif; ?>
            </div>
            <div class="card-body">
                <span class="badge" style="background:var(--<?= match($ev['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($ev['category']) ?></span>
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
        <?php endforeach; endif; ?>
    </div>

    <?php if (isset($pg) && $pg['total_pages'] > 1): ?>
    <div class="pagination" data-events-pagination>
        <?php for ($i = 1; $i <= $pg['total_pages']; $i++): ?>
        <a href="?page=events&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php else: ?>
<!-- Single Event Detail -->
<section class="container event-detail">
    <div class="cover" style="<?= $event['cover_image'] ? "background-image:url(" . UPLOAD_URL . h($event['cover_image']) . ");background-size:cover;background-position:center" : 'display:flex;align-items:center;justify-content:center' ?>">
        <?php if (!$event['cover_image']): ?><span class="material-symbols-outlined" style="font-size:4rem;color:rgba(255,255,255,0.3)">image</span><?php endif; ?>
    </div>
    <div class="content">
        <div class="info">
            <span class="badge" style="background:var(--<?= match($event['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>);margin-bottom:0.5rem"><?= h($event['category']) ?></span>
            <h1><?= h($event['title']) ?></h1>
            <div class="meta-list">
                <div class="meta-item"><span class="material-symbols-outlined">calendar_today</span> <?= date('l, M d, Y', strtotime($event['event_date'])) ?></div>
                <div class="meta-item"><span class="material-symbols-outlined">schedule</span> <?= date('h:i A', strtotime($event['event_time'])) ?></div>
                <div class="meta-item"><span class="material-symbols-outlined">location_on</span> <?= h($event['venue']) ?></div>
                <div class="meta-item"><span class="material-symbols-outlined">person</span> Organized by <?= h($event['organizer_name']) ?></div>
                <?php if ($avgRating > 0): ?>
                <div class="meta-item"><span class="material-symbols-outlined" style="color:#F1C40F">star</span> <?= $avgRating ?>/5 (<?= count($reviews) ?> reviews)</div>
                <?php endif; ?>
            </div>
            <div class="description"><?= nl2br(h($event['description'])) ?></div>

            <?php if (!empty($reviews)): ?>
            <div class="review-list">
                <h3 style="margin-bottom:1rem">Reviews</h3>
                <?php foreach ($reviews as $rev): ?>
                <div class="review-item">
                    <div class="review-header">
                        <span class="reviewer"><?= h($rev['full_name']) ?></span>
                        <span class="stars"><?= str_repeat('&#9733;', $rev['rating']) . str_repeat('&#9734;', 5 - $rev['rating']) ?></span>
                    </div>
                    <?php if ($rev['review_text']): ?><p class="review-text"><?= h($rev['review_text']) ?></p><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="booking-card">
                <div class="price-tag"><?= formatPrice($event['ticket_price']) ?> <span>/ ticket</span></div>
                <div class="seats-bar">
                    <div style="display:flex;justify-content:space-between;font-size:0.8rem;color:var(--secondary);margin-bottom:0.5rem">
                        <span><?= $event['available_seats'] ?> seats left</span>
                        <span><?= $seatPercent ?>% booked</span>
                    </div>
                    <div class="bar"><div class="fill" style="width:<?= $seatPercent ?>%"></div></div>
                </div>
                <?php if ($event['available_seats'] > 0 && isLoggedIn() && currentRole() === 'attendee'): ?>
                <form action="<?= APP_URL ?>/index.php?page=checkout" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <div class="qty-selector" data-qty-selector data-price="<?= $event['ticket_price'] ?>" data-max="<?= $maxBookable ?>">
                        <button type="button" data-qty-minus>&minus;</button>
                        <span class="qty" data-qty-value>1</span>
                        <button type="button" data-qty-plus>+</button>
                        <span style="font-size:0.8rem;color:var(--secondary)">Max <?= $maxBookable ?></span>
                    </div>
                    <div class="total">
                        <span>Total</span>
                        <span data-total-price><?= formatPrice($event['ticket_price']) ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        <span class="material-symbols-outlined">shopping_cart</span> Buy Tickets
                    </button>
                    <p style="text-align:center;font-size:0.7rem;color:var(--secondary);margin-top:0.75rem">
                        <span class="material-symbols-outlined" style="font-size:0.875rem;vertical-align:middle">check_circle</span> Booking is confirmed immediately after checkout
                    </p>
                </form>
                <?php elseif ($event['available_seats'] <= 0): ?>
                <div class="alert alert-error" style="margin-top:1rem">Sold Out!</div>
                <?php elseif (!isLoggedIn()): ?>
                <a href="<?= APP_URL ?>/index.php?page=login" class="btn btn-primary" style="width:100%;margin-top:1rem">Login to Book</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
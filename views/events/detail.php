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
<<<<<<< HEAD
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Music+Event" class="pill pill-music <?= $category === 'Music Event' ? 'active' : '' ?>" data-category-filter="Music Event">Music</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Football" class="pill pill-football <?= $category === 'Football' ? 'active' : '' ?>" data-category-filter="Football">Football</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Cricket" class="pill pill-cricket <?= $category === 'Cricket' ? 'active' : '' ?>" data-category-filter="Cricket">Cricket</a>
    </div>
    <p data-search-meta style="color:var(--secondary);font-size:0.9rem;min-height:1.3rem;margin-top:-0.5rem;margin-bottom:0.75rem"></p>
=======
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Music+Events" class="pill pill-music <?= $category === 'Music Events' ? 'active' : '' ?>" data-category-filter="Music Events">Music</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Football" class="pill pill-football <?= $category === 'Football' ? 'active' : '' ?>" data-category-filter="Football">Football</a>
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=Cricket" class="pill pill-cricket <?= $category === 'Cricket' ? 'active' : '' ?>" data-category-filter="Cricket">Cricket</a>
    </div>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
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
<<<<<<< HEAD
        <a href="<?= APP_URL ?>/index.php?page=events&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
=======
        <a href="?page=events&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&p=<?= $i ?>" class="<?= $i === $pg['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</section>

<?php else: ?>
<!-- Single Event Detail -->
<<<<<<< HEAD
<div>
    <!-- Cover Area (Full width) -->
    <section style="position:relative; width:100%; height:500px; overflow:hidden;">
        <div style="position:absolute; inset:0; <?= $event['cover_image'] ? "background-image:url(" . UPLOAD_URL . h($event['cover_image']) . ");" : "background:linear-gradient(135deg, #1a1a2e, #2a2a4e);" ?> background-size:cover; background-position:center; filter:brightness(0.7);">
            <?php if (!$event['cover_image']): ?>
                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                    <span class="material-symbols-outlined" style="font-size:4rem;color:rgba(255,255,255,0.3)">image</span>
                </div>
            <?php endif; ?>
        </div>
        <div style="position:absolute; inset:0; background:linear-gradient(to top, var(--surface) 0%, transparent 100%);"></div>
        <div class="container" style="position:absolute; bottom:2.5rem; left:0; right:0; padding:0 1.5rem;">
            <span class="badge" style="background:var(--<?= match($event['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>); font-size:0.75rem; padding:0.4rem 1rem; margin-bottom:1.5rem; display:inline-block; border-radius:9999px; color:white; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; box-shadow:0 4px 12px rgba(0,0,0,0.1) border:1px solid rgba(255,255,255,0.2)"><?= h($event['category']) ?></span>
            <h1 style="font-size: clamp(2.5rem, 5vw, 4.5rem); font-weight:800; color:var(--on-surface); line-height:1.1; margin:0; text-shadow:0 2px 10px rgba(255,255,255,0.6); font-family:'Plus Jakarta Sans', sans-serif; letter-spacing:-0.03em;"><?= h($event['title']) ?></h1>
        </div>
    </section>

    <!-- Main Content wrapper -->
    <div class="container" style="max-width:1280px; margin:0 auto; padding:3rem 1.5rem;">
        <div style="display:grid; grid-template-columns:1fr; gap:3rem; align-items:start;" class="event-detail-grid">
            <style>
                @media (min-width: 992px) {
                    .event-detail-grid { grid-template-columns: 1fr 380px !important; }
                }
            </style>
            
            <!-- Left Info Panel -->
            <div class="info">
                
                <!-- Quick Meta Cards -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1.5rem; margin-bottom:3rem;">
                    <div style="display:flex; align-items:center; gap:1rem; background:var(--surface-container-low); padding:1.25rem; border-radius:1rem; border:1px solid rgba(0,0,0,0.05);">
                        <div style="background:rgba(179,25,61,0.1); padding:0.75rem; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                            <span class="material-symbols-outlined" style="color:var(--primary)">calendar_today</span>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--secondary); opacity:0.8; margin-bottom:0.25rem;">Date & Time</p>
                            <p style="font-weight:600; color:var(--on-surface); font-size:1rem;"><?= date('M d, Y', strtotime($event['event_date'])) ?> • <?= date('h:i A', strtotime($event['event_time'])) ?></p>
                        </div>
                    </div>
                    
                    <div style="display:flex; align-items:center; gap:1rem; background:var(--surface-container-low); padding:1.25rem; border-radius:1rem; border:1px solid rgba(0,0,0,0.05);">
                        <div style="background:rgba(179,25,61,0.1); padding:0.75rem; border-radius:0.5rem; display:flex; align-items:center; justify-content:center;">
                            <span class="material-symbols-outlined" style="color:var(--primary)">location_on</span>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--secondary); opacity:0.8; margin-bottom:0.25rem;">Venue</p>
                            <p style="font-weight:600; color:var(--on-surface); font-size:1rem;"><?= h($event['venue']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Organizer Section -->
                <div style="display:flex; align-items:center; gap:1.25rem; padding:1.25rem; background:var(--surface-container); border-radius:1.25rem; margin-bottom:3rem;">
                    <div style="width:4rem; height:4rem; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-container)); color:white; display:flex; align-items:center; justify-content:center; font-size:1.75rem; font-weight:bold; border:3px solid white; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                        <?= strtoupper(substr($event['organizer_name'] ?? 'O', 0, 1)) ?>
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--secondary); opacity:0.8; margin-bottom:0.25rem">Organized By</p>
                        <p style="font-size:1.25rem; font-weight:700; color:var(--on-surface)"><?= h($event['organizer_name']) ?></p>
                    </div>
                </div>

                <!-- Description Section -->
                <div>
                    <h2 style="font-size:1.75rem; font-weight:800; margin-bottom:1.5rem; font-family:'Plus Jakarta Sans', sans-serif; letter-spacing:-0.02em;">About This Experience</h2>
                    <div style="line-height:1.8; color:var(--secondary); font-size:1.1rem; margin-bottom:3rem;">
                        <?= nl2br(h($event['description'])) ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <?php if (!empty($reviews)): ?>
                <div style="margin-top:3rem; padding-top:3rem; border-top:1px solid rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:2.5rem; flex-wrap:wrap; gap:1.5rem;">
                        <div>
                            <h2 style="font-size:2.25rem; font-weight:800; margin-bottom:0.5rem; font-family:'Plus Jakarta Sans', sans-serif;">Guest Reviews</h2>
                            <p style="color:var(--secondary);">What the crowd is saying.</p>
                        </div>
                        <?php if (isset($avgRating) && $avgRating > 0): ?>
                        <div style="display:flex; align-items:center; gap:1rem;">
                            <div style="text-align:right;">
                                <p style="font-size:2.5rem; font-weight:800; line-height:1; color:var(--on-surface)"><?= number_format($avgRating, 1) ?></p>
                                <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:rgba(93,92,116,0.6); letter-spacing:0.1em;">Avg Rating</p>
                            </div>
                            <div style="color:#F1C40F; display:flex;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?= $i<=$avgRating ? '1' : '0' ?>; font-size:1.5rem;">star</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:1.5rem;">
                        <?php foreach ($reviews as $rev): ?>
                        <div style="background:var(--surface-container-low); padding:1.75rem; border-radius:1rem; border-left:4px solid var(--primary); box-shadow:0 2px 8px rgba(0,0,0,0.03); display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <div style="color:#F1C40F; margin-bottom:1rem; display:flex;">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <span class="material-symbols-outlined" style="font-size:1.1rem; font-variation-settings: 'FILL' <?= $i<=$rev['rating'] ? '1' : '0' ?>;">star</span>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($rev['review_text']): ?>
                                    <p style="font-style:italic; margin-bottom:1.5rem; color:var(--on-surface); line-height:1.6; font-size:0.95rem;">"<?= h($rev['review_text']) ?>"</p>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex; align-items:center; gap:0.875rem;">
                                <div style="width:2.5rem; height:2.5rem; border-radius:50%; background:rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:center; font-weight:800; color:var(--secondary); font-size:1rem;">
                                    <?= strtoupper(substr($rev['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p style="font-weight:700; font-size:0.875rem; color:var(--on-surface);"><?= h($rev['full_name']) ?></p>
                                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--secondary); opacity:0.8;">Verified Attendee</p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sticky Right Booking Card -->
            <aside style="position:sticky; top:6rem;">
                <div style="background:var(--surface-container-lowest); border-radius:1.25rem; padding:2rem; box-shadow:0 20px 40px rgba(0,0,0,0.06); border:1px solid rgba(0,0,0,0.05);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
                        <span style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--secondary);">Price per ticket</span>
                        <span style="font-size:2rem; font-weight:800; color:var(--on-surface); font-family:'Plus Jakarta Sans', sans-serif;"><?= formatPrice($event['ticket_price']) ?></span>
                    </div>

                    <div style="margin-bottom:2rem;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:0.75rem;">
                            <span style="font-size:0.875rem; font-weight:700; color:var(--on-surface);">Availability</span>
                            <span style="font-size:0.75rem; font-weight:700; color:var(--primary);">Only <?= $event['available_seats'] ?> seats left!</span>
                        </div>
                        <div style="width:100%; height:10px; background:var(--surface-container); border-radius:999px; overflow:hidden;">
                            <div style="height:100%; width:<?= $seatPercent ?>%; border-radius:999px; background:linear-gradient(90deg, var(--primary), var(--primary-container)); transition:width 0.5s ease-out;"></div>
                        </div>
                    </div>

                    <?php if ($event['available_seats'] > 0 && isLoggedIn() && currentRole() === 'attendee'): ?>
                    <form action="<?= APP_URL ?>/index.php?page=checkout" method="POST">
                        <?= csrfField() ?>
                        <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        
                        <div style="background:var(--surface); padding:1.25rem; border-radius:1rem; margin-bottom:2rem;">
                            <p style="font-size:0.875rem; font-weight:700; margin-bottom:0.75rem; color:var(--on-surface);">Quantity</p>
                            <div style="display:flex; align-items:center; justify-content:space-between; background:white; padding:0.5rem 0.75rem; border-radius:0.75rem; border:1px solid rgba(0,0,0,0.1); box-shadow:0 2px 4px rgba(0,0,0,0.02);" data-qty-selector data-price="<?= $event['ticket_price'] ?>" data-max="<?= $maxBookable ?>">
                                <button type="button" data-qty-minus style="width:2.25rem; height:2.25rem; display:flex; align-items:center; justify-content:center; background:none; border:none; cursor:pointer; border-radius:0.375rem; transition:background 0.2s;" onmouseover="this.style.background='var(--surface-container)'" onmouseout="this.style.background='transparent'"><span class="material-symbols-outlined">remove</span></button>
                                <span style="font-size:1.25rem; font-weight:700; color:var(--on-surface);" data-qty-value>1</span>
                                <button type="button" data-qty-plus style="width:2.25rem; height:2.25rem; display:flex; align-items:center; justify-content:center; background:none; border:none; cursor:pointer; border-radius:0.375rem; transition:background 0.2s;" onmouseover="this.style.background='var(--surface-container)'" onmouseout="this.style.background='transparent'"><span class="material-symbols-outlined">add</span></button>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2.5rem; padding-top:1.5rem; border-top:1px solid rgba(0,0,0,0.05);">
                            <span style="font-size:1.125rem; font-weight:700; color:var(--on-surface);">Total Price</span>
                            <span style="font-size:1.75rem; font-weight:800; color:var(--primary); font-family:'Plus Jakarta Sans', sans-serif;" data-total-price><?= formatPrice($event['ticket_price']) ?></span>
                        </div>

                        <button type="submit" style="width:100%; border:none; cursor:pointer; padding:1.25rem; border-radius:0.75rem; font-size:1.125rem; font-weight:700; color:white; background:linear-gradient(135deg, var(--primary), var(--primary-container)); box-shadow:0 8px 24px rgba(179,25,61,0.25); transition:all 0.3s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                            Buy Tickets
                        </button>
                        
                        <div style="margin-top:1.5rem; text-align:center;">
                            <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.2em; color:var(--secondary); opacity:0.6; margin-bottom:0.75rem;">Secure Payment via</p>
                            <div style="display:inline-flex; justify-content:center; align-items:center; gap:0.5rem; filter:grayscale(100%); opacity:0.6; transition:all 0.3s; cursor:pointer;" onmouseover="this.style.filter='none';this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)';this.style.opacity='0.6'">
                                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuArRpJk0QxMs08o35bHBzJ5eAVtEJHf0GaioLLk_89fXFfKl4ctVN5U1aBdLu4-0e7z1OYfB0bkgfV9txybihD8lbi2LmLtjUOZQLrlOtM-rypLY4GBZpWI46zZimiewmS6lqm0rdQ10BU87GEfoBP1NF072XSSj-J7feDQYVnvKw3wZziQeYMvRrMaRnA6Q7O0k8OKo9PAxsgEAcUjp3YZcai-tefFLv-0daCX3ISzGOpL94YH-0ztxZ-lV8_ajvn8eTpB3cECSudg" alt="eSewa" style="height:1.5rem;">
                                <span style="font-weight:bold; color:#60bb46; font-style:italic; font-size:0.875rem;">eSewa</span>
                            </div>
                        </div>
                    </form>

                    <?php elseif ($event['available_seats'] <= 0): ?>
                    <div style="background:rgba(186,26,26,0.1); color:var(--error); border:1px solid rgba(186,26,26,0.2); padding:1.25rem; border-radius:0.75rem; text-align:center; font-weight:700; font-size:1.125rem; margin-top:1rem;">
                        Sold Out!
                    </div>
                    <?php elseif (!isLoggedIn()): ?>
                    <a href="<?= APP_URL ?>/index.php?page=login" style="display:block; width:100%; text-decoration:none; padding:1.25rem; border-radius:0.75rem; font-size:1.125rem; font-weight:700; color:white; background:linear-gradient(135deg, var(--primary), var(--primary-container)); text-align:center; box-sizing:border-box; box-shadow:0 8px 24px rgba(179,25,61,0.25);">
                        Login to Book
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Secondary Actions -->
                <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                    <button style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:1rem; border-radius:0.75rem; border:1px solid rgba(0,0,0,0.1); background:white; color:var(--on-surface); font-weight:700; font-size:0.875rem; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='var(--surface-container-low)'" onmouseout="this.style.background='white'">
                        <span class="material-symbols-outlined" style="font-size:1.25rem;">share</span> Share
                    </button>
                    <button style="flex:1; display:flex; align-items:center; justify-content:center; gap:0.5rem; padding:1rem; border-radius:0.75rem; border:1px solid rgba(0,0,0,0.1); background:white; color:var(--on-surface); font-weight:700; font-size:0.875rem; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='var(--surface-container-low)'" onmouseout="this.style.background='white'">
                        <span class="material-symbols-outlined" style="font-size:1.25rem;">favorite</span> Wishlist
                    </button>
                </div>
            </aside>
        </div>
    </div>
</div>
<?php endif; ?>
=======
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
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168

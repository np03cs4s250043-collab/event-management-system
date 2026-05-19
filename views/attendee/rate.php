<div class="main-content">
    <div class="top-bar"><h1>Rate: <?= h($event['title']) ?></h1></div>
    <div style="max-width:480px;background:white;border-radius:0.75rem;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04)">
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Your Rating</label>
                <div class="star-rating">
                    <input type="hidden" name="rating" value="0">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <span class="star" style="cursor:pointer;font-size:2rem;color:#ddd">&#9733;</span>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Review (optional)</label>
                <textarea name="review_text" class="form-input" placeholder="Share your experience..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>
<<<<<<< HEAD
</div>
=======
</div>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168

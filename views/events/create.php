<div class="main-content">
    <div class="top-bar"><h1><?= isset($event) ? 'Edit Event' : 'Create Event' ?></h1></div>
    <?php if (!empty($errors)): ?><div class="alert alert-error"><?= implode('<br>', array_map('h', $errors)) ?></div><?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data" style="<?= isset($event) ? 'max-width:720px' : 'display:grid;grid-template-columns:2fr 1fr;gap:2rem;align-items:start' ?>">
        <?= csrfField() ?>
        <div style="background:white;border-radius:0.75rem;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04)">
            <h3 style="margin-bottom:1.5rem;font-weight:700">Event Details</h3>
            <div class="form-group"><label>Event Title</label><input type="text" name="title" class="form-input" required value="<?= h(isset($event) ? $event['title'] : ($_POST['title'] ?? '')) ?>" placeholder="e.g. Nepathya Live Concert 2026"></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-input" required placeholder="Describe your event..."><?= h(isset($event) ? $event['description'] : ($_POST['description'] ?? '')) ?></textarea></div>
            <div class="form-group"><label>Category</label>
                <select name="category" class="form-input form-select" required>
                    <?php if (!isset($event)): ?><option value="">Select category</option><?php endif; ?>
                    <?php foreach (['Concert','Conference','Workshop','Webinar','Sports','Festival','Exhibition','Networking','Music Events','Football','Cricket'] as $c): ?>
                    <option value="<?= $c ?>" <?= (isset($event) ? $event['category'] : ($_POST['category'] ?? '')) === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group"><label>Date</label><input type="date" name="event_date" class="form-input" required value="<?= h(isset($event) ? $event['event_date'] : ($_POST['event_date'] ?? '')) ?>"></div>
                <div class="form-group"><label>Time</label><input type="time" name="event_time" class="form-input" required value="<?= h(isset($event) ? $event['event_time'] : ($_POST['event_time'] ?? '')) ?>"></div>
            </div>
            <div class="form-group"><label>Venue</label><input type="text" name="venue" class="form-input" required value="<?= h(isset($event) ? $event['venue'] : ($_POST['venue'] ?? '')) ?>" placeholder="e.g. Dashrath Stadium, Kathmandu"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div class="form-group"><label>Max Capacity</label><input type="number" name="max_capacity" class="form-input" required min="1" value="<?= h(isset($event) ? $event['max_capacity'] : ($_POST['max_capacity'] ?? '')) ?>"></div>
                <div class="form-group"><label>Ticket Price (Rs.)</label><input type="number" name="ticket_price" class="form-input" required min="1" step="0.01" value="<?= h(isset($event) ? $event['ticket_price'] : ($_POST['ticket_price'] ?? '')) ?>"></div>
            </div>
            <?php if (isset($event)): ?>
            <div class="form-group"><label>Cover Image (leave empty to keep current)</label>
                <div class="upload-area" data-upload-area>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" style="display:none">
                    <span class="material-symbols-outlined">cloud_upload</span>
                    <p>Click or drag to upload new image</p>
                    <img class="preview" style="<?= $event['cover_image'] ? '' : 'display:none' ?>" src="<?= $event['cover_image'] ? UPLOAD_URL . h($event['cover_image']) : '' ?>" alt="Preview">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:1rem">Save Changes</button>
            <a href="<?= APP_URL ?>/index.php?page=organizer/dashboard" class="btn btn-outline" style="margin-top:1rem;margin-left:0.5rem">Cancel</a>
            <?php endif; ?>
        </div>
        <?php if (!isset($event)): ?>
        <div>
            <div style="background:white;border-radius:0.75rem;padding:2rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:1.5rem">
                <h3 style="margin-bottom:1rem;font-weight:700">Cover Image</h3>
                <div class="upload-area" data-upload-area>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" style="display:none">
                    <span class="material-symbols-outlined">cloud_upload</span>
                    <p>Click or drag to upload</p>
                    <p style="font-size:0.7rem;margin-top:0.25rem">JPG, PNG, WebP (max 2MB)</p>
                    <img class="preview" style="display:none" alt="Preview">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.875rem">
                <span class="material-symbols-outlined" style="font-size:1.25rem">publish</span> Publish Event
            </button>
            <p style="text-align:center;font-size:0.75rem;color:var(--secondary);margin-top:0.75rem">Event will be pending admin approval</p>
        </div>
        <?php endif; ?>
    </form>
</div>
<div class="main-content">
    <div class="top-bar">
<<<<<<< HEAD
        <h1 data-aos="fade-right">Organizer Dashboard</h1>
        <a href="<?= APP_URL ?>/index.php?page=organizer/create" class="btn btn-primary btn-sm" data-aos="fade-left"><span class="material-symbols-outlined" style="font-size:1rem">add</span> Create Event</a>
    </div>

    <!-- AI Co-Pilot Insight Widget (Glassmorphism & Dynamic) -->
    <div data-aos="fade-up" style="background: linear-gradient(135deg, rgba(239, 236, 255, 0.6), rgba(255, 255, 255, 0.4)); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.8); border-left: 4px solid var(--primary); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1.25rem; box-shadow: 0 8px 32px rgba(0,0,0,0.04);">
        <div style="background: linear-gradient(135deg, var(--primary), var(--primary-container)); width: 3rem; height: 3rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(179,25,61,0.2);">
            <span class="material-symbols-outlined" style="color: white;">smart_toy</span>
        </div>
        <div>
            <h3 style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.1rem; color: var(--on-surface); margin-bottom: 0.25rem;">Eventify AI Co-Pilot Insight</h3>
            <p style="color: var(--secondary); font-size: 0.95rem; line-height: 1.5;">Based on recent trends, music events on Saturday evenings see a <strong style="color: var(--primary);">24% higher booking rate</strong>. Consider scheduling your next concert then. Additionally, try generating an engaging agenda using our new AI Draft tool when creating your next event!</p>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card stat-blue" data-aos="zoom-in" data-aos-delay="0"><div class="stat-label">Total Events</div><div class="stat-value"><?= $stats['total_events'] ?? 0 ?></div><span class="material-symbols-outlined stat-icon">event</span></div>
        <div class="stat-card stat-green" data-aos="zoom-in" data-aos-delay="100"><div class="stat-label">Total Bookings</div><div class="stat-value"><?= $stats['total_bookings'] ?? 0 ?></div><span class="material-symbols-outlined stat-icon">confirmation_number</span></div>
        <div class="stat-card stat-coral" data-aos="zoom-in" data-aos-delay="200"><div class="stat-label">Total Revenue</div><div class="stat-value"><?= formatPrice($stats['total_revenue'] ?? 0) ?></div><span class="material-symbols-outlined stat-icon">payments</span></div>
        <div class="stat-card stat-orange" data-aos="zoom-in" data-aos-delay="300"><div class="stat-label">Active Events</div><div class="stat-value"><?= $stats['active_events'] ?? 0 ?></div><span class="material-symbols-outlined stat-icon">verified</span></div>
    </div>

    <!-- Analytics Charts Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;" data-aos="fade-up" data-aos-delay="400">
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);">
            <h3 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--secondary);">
                <span class="material-symbols-outlined">trending_up</span> Revenue Trends
            </h3>
            <div style="position:relative;height:240px;width:100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04);">
            <h3 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--secondary);">
                <span class="material-symbols-outlined">pie_chart</span> Bookings by Category
            </h3>
            <div style="position:relative;height:240px;width:100%;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <h2 style="font-size:1.25rem;font-weight:800;margin-bottom:1rem;font-family:'Plus Jakarta Sans', sans-serif;" data-aos="fade-right">My Events</h2>
    <?php if (empty($events)): ?>
    <div style="text-align:center;padding:4rem;color:var(--secondary);background:white;border-radius:1rem;box-shadow:0 4px 20px rgba(0,0,0,0.03)" data-aos="fade-up">
        <span class="material-symbols-outlined" style="font-size:3.5rem; opacity:0.3; margin-bottom: 1rem;">event_note</span>
        <p style="font-size: 1.1rem;">No events yet. <a href="<?= APP_URL ?>/index.php?page=organizer/create" style="color:var(--primary); font-weight:700; text-decoration:none;">Create your first event</a></p>
    </div>
    <?php else: ?>
    <table class="data-table" data-aos="fade-up" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; border-radius: 1rem;">
        <thead style="background: rgba(245, 242, 255, 0.8);"><tr><th style="padding: 1.25rem 1.5rem; font-family: 'Plus Jakarta Sans', sans-serif;">Title</th><th>Category</th><th>Date</th><th>Bookings</th><th>Revenue</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($events as $i => $ev): ?>
        <tr data-aos="fade-up" data-aos-delay="<?= ($i % 10) * 50 ?>" style="transition: background 0.3s;" onmouseover="this.style.background='var(--surface-container-low)'" onmouseout="this.style.background='transparent'">
            <td style="font-weight:600; padding: 1.25rem 1.5rem; color: var(--on-surface);"><div style="display:flex; align-items:center; gap: 0.75rem;"><div style="width:2rem;height:2rem;border-radius:0.5rem;background:var(--surface-container-high);display:flex;align-items:center;justify-content:center;color:var(--primary)"><span class="material-symbols-outlined" style="font-size:1.1rem">festival</span></div> <?= h($ev['title']) ?></div></td>
            <td><span class="badge" style="background:var(--<?= match($ev['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>); padding: 0.35rem 0.85rem; border-radius: 999px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: white;"><?= h($ev['category']) ?></span></td>
            <td style="color: var(--secondary); font-size: 0.9rem; font-weight: 500;"><?= date('M d, Y', strtotime($ev['event_date'])) ?></td>
            <td style="font-weight: 700;"><?= $ev['bookings_count'] ?> <span style="font-size: 0.75rem; color: var(--secondary); font-weight: 500;">/ <?= $ev['total_seats'] ?? 100 ?></span></td>
            <td style="font-weight:800; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--primary);"><?= formatPrice($ev['revenue']) ?></td>
            <td><span class="status-badge status-<?= strtolower($ev['status']) ?>" style="padding: 0.35rem 0.85rem; font-size: 0.65rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 0.25rem;"><span class="material-symbols-outlined" style="font-size: 0.8rem;"><?= $ev['status'] === 'Active' ? 'check_circle' : ($ev['status'] === 'Cancelled' ? 'cancel' : 'pending') ?></span> <?= h($ev['status']) ?></span></td>
            <td style="display:flex;gap:0.5rem; align-items: center; justify-content: flex-start; padding-top: 1rem;">
                <a href="<?= APP_URL ?>/index.php?page=organizer/edit&id=<?= $ev['event_id'] ?>" class="btn btn-sm" style="background:var(--surface-container-high); color: var(--secondary); border-radius: 0.5rem; padding: 0.5rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='var(--primary)'; this.style.color='white';" onmouseout="this.style.background='var(--surface-container-high)'; this.style.color='var(--secondary)';"><span class="material-symbols-outlined" style="font-size: 1.1rem;">edit</span></a>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=organizer/delete" style="display:inline">
                    <?= csrfField() ?><input type="hidden" name="event_id" value="<?= $ev['event_id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this event? All bookings will be cancelled." style="border-radius: 0.5rem; padding: 0.5rem; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"><span class="material-symbols-outlined" style="font-size: 1.1rem;">delete</span></button>
                </form>
=======
        <h1>Welcome, <?= h($_SESSION['full_name'] ?? '') ?></h1>
        <a href="<?= APP_URL ?>/index.php?page=events" class="btn btn-primary btn-sm"><span class="material-symbols-outlined" style="font-size:1rem">explore</span> Browse Events</a>
    </div>

    <div class="stat-grid">
        <div class="stat-card stat-blue"><div class="stat-label">Upcoming Events</div><div class="stat-value"><?= $stats['upcoming'] ?></div><span class="material-symbols-outlined stat-icon">event</span></div>
        <div class="stat-card stat-purple"><div class="stat-label">Past Events</div><div class="stat-value"><?= $stats['past'] ?></div><span class="material-symbols-outlined stat-icon">history</span></div>
        <div class="stat-card stat-coral"><div class="stat-label">Total Spent</div><div class="stat-value"><?= formatPrice($stats['total_spent']) ?></div><span class="material-symbols-outlined stat-icon">payments</span></div>
    </div>

    <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1rem">Upcoming Bookings</h2>
    <?php if (empty($upcoming)): ?>
    <div style="text-align:center;padding:3rem;color:var(--secondary);background:white;border-radius:0.75rem">
        <span class="material-symbols-outlined" style="font-size:2.5rem;display:block;margin-bottom:0.5rem">event_busy</span>
        <p>No upcoming bookings. <a href="<?= APP_URL ?>/index.php?page=events" style="color:var(--primary)">Browse events</a></p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Event</th><th>Category</th><th>Date</th><th>Venue</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($upcoming as $b): ?>
        <tr>
            <td style="font-weight:600"><?= h($b['title']) ?></td>
            <td><span class="badge" style="background:var(--<?= match($b['category']) {'Concert'=>'concert','Music Event'=>'music','Football'=>'football','Cricket'=>'cricket',default=>'secondary'} ?>)"><?= h($b['category']) ?></span></td>
            <td><?= date('M d', strtotime($b['event_date'])) ?></td>
            <td><?= h($b['venue']) ?></td>
            <td style="font-weight:600"><?= formatPrice($b['total_amount']) ?></td>
            <td><span class="status-badge status-<?= strtolower($b['status']) ?>"><?= h($b['status']) ?></span></td>
            <td>
                <?php if ($b['status'] === 'confirmed'): ?>
                <form method="POST" action="<?= APP_URL ?>/index.php?page=attendee/cancel" style="display:inline">
                    <?= csrfField() ?>
                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Cancel this booking?">Cancel</button>
                </form>
                <?php endif; ?>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
<<<<<<< HEAD
</div>

<!-- Add Chart.js for dynamic analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') {
            return;
        }

        const safeRevenue = Number(<?= json_encode((float)($stats['total_revenue'] ?? 30000)) ?>) || 30000;

        // Safe defaults if arrays are empty or non-existent
        const ctxRev = document.getElementById('revenueChart');
        if(ctxRev) {
            new Chart(ctxRev.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue Trend',
                        data: [12000, 19000, 15000, 28000, 22000, safeRevenue],
                        borderColor: '#b3193d',
                        backgroundColor: 'rgba(179, 25, 61, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#d63653',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#b3193d',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [4, 4], color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        const ctxCat = document.getElementById('categoryChart');
        if(ctxCat) {
            new Chart(ctxCat.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Concerts', 'Sports', 'Workshops', 'Conferences'],
                    datasets: [{
                        data: [45, 25, 20, 10],
                        backgroundColor: ['#b3193d', '#27AE60', '#3498DB', '#E67E22'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 20, font: { family: 'Inter' } } } }
                }
            });
        }
    });
</script>
=======
</div>
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168

/**
 * Eventify - Main JavaScript
 */
const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';

function debounce(fn, ms = 300) {
    let timer;
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), ms); };
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
}

async function apiFetch(url, options = {}) {
    try {
        const res = await fetch(APP_URL + url, {
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            ...options
        });
        return await res.json();
    } catch (e) {
        console.error('API Error:', e);
        return { error: true, message: 'Network error' };
    }
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// ========================================
// Autocomplete Search
// ========================================
document.querySelectorAll('[data-autocomplete]').forEach(input => {
    if (input.hasAttribute('data-events-search')) return;
    const dropdown = input.parentElement.querySelector('.autocomplete');
    if (!dropdown) return;

    input.addEventListener('input', debounce(async (e) => {
        const q = e.target.value.trim();
        if (q.length < 2) { dropdown.classList.remove('show'); return; }
        const data = await apiFetch(`/api/search.php?q=${encodeURIComponent(q)}`);
        if (data.results && data.results.length) {
            dropdown.innerHTML = data.results.map(r =>
                `<a href="${APP_URL}/index.php?page=event&id=${r.event_id}">${r.title} <small style="color:var(--secondary)">${r.category}</small></a>`
            ).join('');
            dropdown.classList.add('show');
        } else {
            dropdown.classList.remove('show');
        }
    }, 300));

    document.addEventListener('click', (e) => {
        if (!input.parentElement.contains(e.target)) dropdown.classList.remove('show');
    });
});

// ========================================
// Live Email Validation
// ========================================
document.querySelectorAll('[data-email-check]').forEach(input => {
    const feedback = input.closest('.form-group')?.querySelector('.email-feedback');
    input.addEventListener('blur', async () => {
        const email = input.value.trim();
        if (!email) return;
        const data = await apiFetch('/api/auth.php', {
            method: 'POST', body: JSON.stringify({ email })
        });
        if (feedback) {
            if (data.available) {
                feedback.innerHTML = '<span style="color:#27AE60">&#10003; Email available</span>';
            } else {
                feedback.innerHTML = '<span style="color:var(--error)">&#10007; Email already taken</span>';
            }
        }
    });
});

// ========================================
// Ticket Quantity Calculator
// ========================================
document.querySelectorAll('[data-qty-selector]').forEach(container => {
    const minusBtn = container.querySelector('[data-qty-minus]');
    const plusBtn = container.querySelector('[data-qty-plus]');
    const qtyDisplay = container.querySelector('[data-qty-value]');
    const totalDisplay = document.querySelector('[data-total-price]');
    const pricePerTicket = parseFloat(container.dataset.price) || 0;
    const maxQty = parseInt(container.dataset.max) || 5;
    let qty = 1;

    function update() {
        qtyDisplay.textContent = qty;
        if (totalDisplay) totalDisplay.textContent = `Rs. ${(qty * pricePerTicket).toLocaleString('en-NP', {minimumFractionDigits: 2})}`;
        const qtyInput = document.querySelector('input[name="quantity"]');
        if (qtyInput) qtyInput.value = qty;
    }

    minusBtn?.addEventListener('click', () => { if (qty > 1) { qty--; update(); } });
    plusBtn?.addEventListener('click', () => { if (qty < maxQty) { qty++; update(); } });
    update();
});

// ========================================
// Category Filter (Events page - Ajax)
// ========================================
document.querySelectorAll('[data-category-filter]').forEach(pill => {
    pill.addEventListener('click', async (e) => {
        e.preventDefault();
        document.querySelectorAll('[data-category-filter]').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        const cat = pill.dataset.categoryFilter;
        const grid = document.querySelector('[data-event-grid]');
        if (!grid) return;

        const search = document.querySelector('[name="search"]')?.value || '';
        const url = `/api/events.php?category=${encodeURIComponent(cat)}&search=${encodeURIComponent(search)}`;
        const data = await apiFetch(url);
        if (data.html) {
            grid.innerHTML = data.html;
        } else if (data.events) {
            grid.innerHTML = data.events.map(ev => renderEventCard(ev)).join('');
        }
        if (data.events?.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--secondary)"><span class="material-symbols-outlined" style="font-size:3rem;display:block;margin-bottom:1rem">search_off</span>No events found</div>';
        }

        const pagination = document.querySelector('[data-events-pagination]');
        if (pagination) pagination.style.display = 'none';
    });
});

function renderEventCard(ev) {
    const badgeVar = ev.category === 'Concert'
        ? 'concert'
        : ev.category === 'Music Event'
            ? 'music'
            : ev.category === 'Football'
                ? 'football'
                : ev.category === 'Cricket'
                    ? 'cricket'
                    : 'secondary';
    const img = ev.cover_image ? `${APP_URL}/public/uploads/${ev.cover_image}` : '';
    const dateVal = ev.event_date ? new Date(ev.event_date) : null;
    const dateText = dateVal && !Number.isNaN(dateVal.getTime())
        ? dateVal.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
        : ev.event_date || '';
    const priceText = Number(ev.ticket_price || 0).toLocaleString('en-NP', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    return `
    <div class="event-card" data-event-card-link data-event-link="${APP_URL}/index.php?page=event&id=${ev.event_id}">
        <div class="card-img" style="${img ? `background-image:url(${img});background-size:cover;background-position:center` : ''}">
            ${img ? '' : '<div style="height:100%;display:flex;align-items:center;justify-content:center"><span class="material-symbols-outlined" style="font-size:3rem;color:rgba(255,255,255,0.3)">image</span></div>'}
        </div>
        <div class="card-body">
            <span class="badge" style="background:var(--${badgeVar})">${escapeHtml(ev.category || '')}</span>
            <h3>${escapeHtml(ev.title)}</h3>
            <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">calendar_today</span>${dateText}</div>
            <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">location_on</span>${escapeHtml(ev.venue || '')}</div>
            <div class="meta"><span class="material-symbols-outlined" style="font-size:1rem">event_seat</span>${ev.available_seats ?? 0} seats left</div>
        </div>
        <div class="card-footer">
            <span class="price">Rs. ${priceText}</span>
            <a href="${APP_URL}/index.php?page=event&id=${ev.event_id}" class="btn btn-primary btn-sm">View Details</a>
        </div>
    </div>`;
}

// ========================================
// Events Page - Live Ajax Search
// ========================================
(() => {
    const searchInput = document.querySelector('[data-events-search]');
    const grid = document.querySelector('[data-event-grid]');
    const form = document.querySelector('[data-events-search-form]');
    if (!searchInput || !grid || !form) return;

    const pagination = document.querySelector('[data-events-pagination]');
    const searchBar = document.querySelector('[data-search-focus]');

    async function updateGrid() {
        const activePill = document.querySelector('[data-category-filter].active');
        const category = activePill?.dataset.categoryFilter || '';
        const search = searchInput.value.trim();
        const data = await apiFetch(`/api/events.php?category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`);

        if (Array.isArray(data.events) && data.events.length) {
            grid.innerHTML = data.events.map(ev => renderEventCard(ev)).join('');
        } else {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--secondary)"><span class="material-symbols-outlined" style="font-size:3.5rem;display:block;margin-bottom:1rem">search_off</span><h3>No events found</h3><p>Try a different keyword or category.</p></div>';
        }

        if (pagination) pagination.style.display = 'none';
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        await updateGrid();
    });

    searchInput.addEventListener('input', debounce(updateGrid, 250));

    searchBar?.addEventListener('click', (e) => {
        if (!e.target.closest('input, button, a')) {
            searchInput.focus();
        }
    });
})();

// ========================================
// Clickable Event Cards
// ========================================
document.addEventListener('click', (e) => {
    const card = e.target.closest('[data-event-card-link]');
    if (!card) return;
    if (e.target.closest('a, button, input, textarea, select, label, form')) return;

    const targetUrl = card.getAttribute('data-event-link');
    if (targetUrl) {
        window.location.href = targetUrl;
    }
});

// ========================================
// My Bookings Tabs (Ajax)
// ========================================
document.querySelectorAll('[data-booking-tab]').forEach(tab => {
    tab.addEventListener('click', async () => {
        document.querySelectorAll('[data-booking-tab]').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const type = tab.dataset.bookingTab;
        const container = document.querySelector('[data-bookings-content]');
        if (!container) return;
        const data = await apiFetch(`/api/bookings.php?type=${type}`);
        if (data.html) container.innerHTML = data.html;
    });
});

// ========================================
// Confirm Delete Modal
// ========================================
document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', (e) => {
        if (!confirm(btn.dataset.confirm || 'Are you sure?')) {
            e.preventDefault();
        }
    });
});

// ========================================
// Image Upload Preview
// ========================================
document.querySelectorAll('[data-upload-area]').forEach(area => {
    const input = area.querySelector('input[type="file"]');
    const preview = area.querySelector('.preview');

    area.addEventListener('click', () => input?.click());
    area.addEventListener('dragover', e => { e.preventDefault(); area.style.borderColor = 'var(--primary)'; });
    area.addEventListener('dragleave', () => { area.style.borderColor = ''; });
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.style.borderColor = '';
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; showPreview(input.files[0]); }
    });
    input?.addEventListener('change', () => { if (input.files[0]) showPreview(input.files[0]); });

    function showPreview(file) {
        if (!preview) return;
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(file);
    }
});

// ========================================
// Star Rating Widget
// ========================================
document.querySelectorAll('.star-rating').forEach(widget => {
    const stars = widget.querySelectorAll('.star');
    const input = widget.querySelector('input[name="rating"]') || widget.parentElement.querySelector('input[name="rating"]');
    stars.forEach((star, i) => {
        star.addEventListener('click', () => {
            const val = i + 1;
            if (input) input.value = val;
            stars.forEach((s, j) => s.classList.toggle('active', j < val));
        });
        star.addEventListener('mouseenter', () => {
            stars.forEach((s, j) => s.style.color = j <= i ? '#F1C40F' : '');
        });
    });
    widget.addEventListener('mouseleave', () => {
        const val = input ? parseInt(input.value) || 0 : 0;
        stars.forEach((s, j) => { s.style.color = ''; s.classList.toggle('active', j < val); });
    });
});

// ========================================
// Password Toggle
// ========================================
document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const input = targetId
            ? document.getElementById(targetId)
            : btn.closest('.password-field')?.querySelector('input');
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('.material-symbols-outlined').textContent = input.type === 'password' ? 'visibility' : 'visibility_off';
        btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    });
});

// Mobile sidebar toggle
document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
    document.querySelector('.sidebar')?.classList.toggle('show-mobile');
});
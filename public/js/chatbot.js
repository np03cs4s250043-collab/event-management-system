/**
 * Eventify Chatbot Widget
 * Floating button + chat panel injected into every page via footer.php.
 * Talks to /api/index.php?resource=chatbot using session auth.
 */
(function () {
    const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';
    const ENDPOINT = APP_URL + '/api/index.php?resource=chatbot';
    const USER = window.EMS_USER || { id: null, role: null, name: '' };

    // Conversation state — full history sent to backend each turn.
    const messages = [];

    // ── Build DOM ─────────────────────────────────────────────
    const fab = document.createElement('button');
    fab.className = 'ev-chat-fab';
    fab.setAttribute('aria-label', 'Open assistant');
    fab.innerHTML = '<span class="material-symbols-outlined">chat</span>';

    const panel = document.createElement('div');
    panel.className = 'ev-chat-panel';
    panel.innerHTML = `
        <div class="ev-chat-header">
            <div>
                <h4>Eventify Assistant</h4>
                <p>Ask about events &middot; book tickets</p>
            </div>
            <button class="ev-chat-close" aria-label="Close">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>
        <div class="ev-chat-body" id="ev-chat-body"></div>
        <form class="ev-chat-input" id="ev-chat-form">
            <input type="text" id="ev-chat-input" placeholder="Ask about events…" autocomplete="off" maxlength="500" />
            <button type="submit" id="ev-chat-send">Send</button>
        </form>
    `;

    document.body.appendChild(fab);
    document.body.appendChild(panel);

    const body  = panel.querySelector('#ev-chat-body');
    const form  = panel.querySelector('#ev-chat-form');
    const input = panel.querySelector('#ev-chat-input');
    const send  = panel.querySelector('#ev-chat-send');
    const closeBtn = panel.querySelector('.ev-chat-close');

    // ── Open / close ──────────────────────────────────────────
    fab.addEventListener('click', () => {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            input.focus();
            if (messages.length === 0) {
                const greeting = USER.id
                    ? `Hi ${USER.name || 'there'}! I can help you find events and manage bookings. What are you looking for?`
                    : 'Hi! I can help you find events. To book tickets you\'ll need to log in.';
                renderAssistant(greeting);
            }
        }
    });
    closeBtn.addEventListener('click', () => panel.classList.remove('open'));

    // ── Send flow ─────────────────────────────────────────────
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;
        sendMessage(text);
    });

    async function sendMessage(text) {
        renderUser(text);
        messages.push({ role: 'user', content: text });
        input.value = '';
        setBusy(true);
        const typing = renderTyping();

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ messages }),
            });
            const data = await res.json();
            typing.remove();

            const reply = data.reply || 'Sorry, I had trouble responding.';
            messages.push({ role: 'assistant', content: reply });
            renderAssistant(reply);

            if (data.proposal && !data.proposal.error) {
                renderProposal(data.proposal);
            }
        } catch (err) {
            typing.remove();
            renderError('Network error. Please try again.');
        } finally {
            setBusy(false);
            input.focus();
        }
    }

    function setBusy(busy) {
        input.disabled = busy;
        send.disabled = busy;
    }

    // ── Renderers ─────────────────────────────────────────────
    function renderUser(text) {
        const el = document.createElement('div');
        el.className = 'ev-msg user';
        el.textContent = text;
        body.appendChild(el);
        scrollDown();
    }

    function renderAssistant(text) {
        const el = document.createElement('div');
        el.className = 'ev-msg assistant';
        // Linkify "log in" mentions for guests
        if (!USER.id && /log[\s-]?in/i.test(text)) {
            el.innerHTML = escapeHtml(text).replace(
                /log[\s-]?in/i,
                `<a class="ev-login-link" href="${APP_URL}/index.php?page=login">log in</a>`
            );
        } else {
            el.textContent = text;
        }
        body.appendChild(el);
        scrollDown();
    }

    function renderError(text) {
        const el = document.createElement('div');
        el.className = 'ev-msg error';
        el.textContent = text;
        body.appendChild(el);
        scrollDown();
    }

    function renderTyping() {
        const el = document.createElement('div');
        el.className = 'ev-typing';
        el.innerHTML = '<span></span><span></span><span></span>';
        body.appendChild(el);
        scrollDown();
        return el;
    }

    function renderProposal(p) {
        const card = document.createElement('div');
        card.className = 'ev-proposal';
        card.innerHTML = `
            <h5>Confirm your booking</h5>
            <dl>
                <dt>Event:</dt>     <dd>${escapeHtml(p.title)}</dd>
                <dt>Date:</dt>      <dd>${escapeHtml(p.date)}</dd>
                <dt>Venue:</dt>     <dd>${escapeHtml(p.venue || '')}</dd>
                <dt>Quantity:</dt>  <dd>${p.quantity}</dd>
                <dt>Total:</dt>     <dd>Rs. ${Number(p.total_npr).toLocaleString()}</dd>
            </dl>
            <div class="ev-proposal-actions">
                <button type="button" class="ev-btn-cancel">Cancel</button>
                <button type="button" class="ev-btn-confirm">Confirm &amp; Book</button>
            </div>
        `;
        const confirmBtn = card.querySelector('.ev-btn-confirm');
        const cancelBtn  = card.querySelector('.ev-btn-cancel');
        confirmBtn.addEventListener('click', () => {
            card.remove();
            sendMessage(`Yes, please book ${p.quantity} ticket(s) for event id ${p.event_id} now. (confirmed)`);
        });
        cancelBtn.addEventListener('click', () => {
            card.remove();
            sendMessage('No, cancel that — don\'t book.');
        });
        body.appendChild(card);
        scrollDown();
    }

    function scrollDown() {
        body.scrollTop = body.scrollHeight;
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }
})();

<?php
$paymentLabels = ['esewa' => 'eSewa', 'khalti' => 'Khalti', 'card' => 'Direct Confirmation'];
$paymentLabel  = $paymentLabels[$booking['payment_method'] ?? 'card'] ?? 'Direct Confirmation';
$txnId         = $booking['payment_txn'] ?? '';
?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary-brand': '#b3193d',
                    'primary-container': '#d63653',
                    'surface-app': '#fcf8ff',
                    'on-surface-dark': '#1a1a2e',
                    'secondary-text': '#5d5c74',
                    'outline-soft': '#e2bebf',
                },
                fontFamily: {
                    headline: ['"Plus Jakarta Sans"', 'sans-serif'],
                    body:     ['Inter', 'sans-serif'],
                }
            }
        }
    }
</script>
<style>
    body {
        font-family: 'Inter', sans-serif !important;
        background-color: #fcf8ff !important;
        color: #1a1a2e !important;
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>

<main class="flex-grow flex items-center justify-center p-4 md:p-8">
    <div class="max-w-3xl w-full">
        <!-- Main Confirmation Card -->
        <div class="bg-white rounded-xl shadow-2xl shadow-on-surface-dark/5 overflow-hidden flex flex-col md:flex-row relative">

            <!-- Left Decorative Accent -->
            <div class="w-full md:w-1/3 bg-[#1a1a2e] p-8 flex flex-col justify-between text-white relative overflow-hidden">
                <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at 30% 20%, #b3193d 0%, transparent 50%), radial-gradient(circle at 70% 80%, #5d5c74 0%, transparent 50%);"></div>
                <div class="relative z-10">
                    <a href="<?= APP_URL ?>/index.php" class="text-2xl font-extrabold tracking-tighter mb-8 font-headline text-white no-underline block">Eventify</a>
                    <div class="space-y-4 mt-8">
                        <div class="h-1 w-12 bg-primary-brand rounded-full"></div>
                        <h2 class="text-3xl font-headline font-bold leading-tight">Your seat is waiting.</h2>
                        <p class="text-white/60 text-sm font-medium">Get ready for an unforgettable experience curated just for you.</p>
                    </div>
                </div>
                <div class="relative z-10 pt-12">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40">Verified Booking</span>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="w-full md:w-2/3 p-8 md:p-12 bg-white">
                <!-- Success Icon & Header -->
                <div class="flex flex-col items-center md:items-start mb-10">
                    <div class="w-20 h-20 rounded-full bg-[#00C853]/10 flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-5xl text-[#00C853]" style="font-variation-settings:'FILL' 1;font-size:3rem;">check_circle</span>
                    </div>
                    <h1 class="text-3xl font-headline font-extrabold tracking-tight text-on-surface-dark mb-2">Booking Confirmed!</h1>
                    <p class="text-secondary-text font-medium">Your tickets have been successfully reserved.</p>
                </div>

                <!-- Booking Details Grid -->
                <div class="grid grid-cols-1 gap-y-6 mb-10">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Booking ID</span>
                        <span class="text-on-surface-dark font-semibold font-headline"><?= h($booking['booking_ref']) ?></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-outline-soft/30 pt-6">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Event</span>
                            <span class="text-on-surface-dark font-bold"><?= h($booking['title']) ?></span>
                        </div>
                        <div class="flex flex-col gap-1 md:text-left">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Date</span>
                            <span class="text-on-surface-dark font-medium">
                                <?= date('M d, Y', strtotime($booking['event_date'])) ?> &bull;
                                <?= date('g:i A', strtotime($booking['event_time'])) ?>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 border-t border-outline-soft/30 pt-6">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Venue</span>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary-text text-sm" style="font-size:1rem">location_on</span>
                                <span class="text-on-surface-dark font-medium"><?= h($booking['venue']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-outline-soft/30 pt-6">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Tickets</span>
                            <span class="text-on-surface-dark font-medium"><?= (int)$booking['quantity'] ?>x <?= h($booking['category'] ?? 'General') ?></span>
                        </div>
                        <div class="flex flex-col gap-1 md:text-left">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Paid</span>
                            <span class="text-primary-brand font-bold text-lg"><?= formatPrice($booking['total_amount']) ?></span>
                        </div>
                    </div>

                    <?php if ($txnId): ?>
                    <div class="flex flex-col gap-1 border-t border-outline-soft/30 pt-6">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-secondary-text/60">Transaction ID</span>
                        <span class="text-secondary-text/80 text-xs font-mono break-all"><?= h($txnId) ?></span>
                        <span class="text-[10px] text-secondary-text/60 mt-1">Paid via <span class="font-semibold text-on-surface-dark"><?= h($paymentLabel) ?></span></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?= APP_URL ?>/index.php?page=attendee/bookings" class="flex-1 bg-gradient-to-br from-primary-brand to-primary-container text-white px-6 py-4 rounded-lg font-headline font-bold text-sm tracking-wide text-center transition-all duration-300 hover:scale-[1.02] active:scale-95 shadow-xl shadow-primary-brand/20 no-underline">
                        View My Bookings
                    </a>
                    <a href="<?= APP_URL ?>/index.php?page=events" class="flex-1 bg-transparent border border-outline-soft/50 text-on-surface-dark px-6 py-4 rounded-lg font-headline font-bold text-sm tracking-wide text-center transition-all duration-300 hover:bg-surface-app no-underline">
                        Browse More Events
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex items-center justify-center px-4 text-secondary-text/60 text-sm gap-2">
            <span class="material-symbols-outlined text-sm" style="font-size:1rem">lock</span>
            <span>Securely processed by <?= h($paymentLabel) ?></span>
        </div>
    </div>
</main>

<!-- Custom Footer -->
<footer class="bg-[#1a1a2e] py-12 px-8 mt-auto border-t border-white/5">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <div class="text-lg font-black text-white mb-4">Eventify</div>
            <p class="text-white/40 text-xs leading-relaxed max-w-xs">
                Discover. Book. Experience. Your gateway to the best events in town.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 col-span-1 md:col-span-3 gap-8">
            <div class="flex flex-col gap-4">
                <h4 class="font-headline font-bold text-white text-sm uppercase tracking-widest">Platform</h4>
                <nav class="flex flex-col gap-2">
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="<?= APP_URL ?>/index.php?page=events">Explore Events</a>
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="<?= APP_URL ?>/index.php?page=attendee/dashboard">Dashboard</a>
                </nav>
            </div>
            <div class="flex flex-col gap-4">
                <h4 class="font-headline font-bold text-white text-sm uppercase tracking-widest">Legal</h4>
                <nav class="flex flex-col gap-2">
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="#">Privacy Policy</a>
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="#">Terms of Service</a>
                </nav>
            </div>
            <div class="flex flex-col gap-4">
                <h4 class="font-headline font-bold text-white text-sm uppercase tracking-widest">Connect</h4>
                <nav class="flex flex-col gap-2">
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="#">Contact Us</a>
                    <a class="text-white/40 hover:text-white transition-all text-xs no-underline" href="#">Support Center</a>
                </nav>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-12 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-white/40 text-xs">&copy; <?= date('Y') ?> Eventify. Herald College Kathmandu.</p>
        <div class="flex gap-6">
            <span class="material-symbols-outlined text-white/20 text-lg">credit_card</span>
            <span class="material-symbols-outlined text-white/20 text-lg">payments</span>
            <span class="material-symbols-outlined text-white/20 text-lg">account_balance_wallet</span>
        </div>
    </div>
</footer>

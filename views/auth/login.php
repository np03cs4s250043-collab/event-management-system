<?php
// views/auth/login.php
require_once __DIR__ . '/./../core/csrf_helper.php';
require_once __DIR__ . '/../../core/session_helper.php';

// If user has already loggedin take them to dashboard page
if (isLoggedIn()) {
    $dest = match(currentRole()) {
        'admin'     => 'admin.dashboard',
        'organizer' => 'organizer.dashboard',
        default     => 'attendee.dashboard',
    };
    header("Location: /ems/index.php?page=$dest");
    exit;
}

$pageTitle = 'Login';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Login | EVENTIFY</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  theme: { extend: { colors: {
    "primary-fixed": "#cce5ff", "primary-fixed-dim": "#9dcbf4", "surface-bright": "#f8f9fa",
    "on-secondary-fixed": "#0c1d2a", "secondary-container": "#d3e4f7", "on-surface": "#191c1d",
    "secondary-fixed": "#d3e4f7", "surface-container-highest": "#e1e3e4", "on-primary": "#ffffff",
    "inverse-on-surface": "#f0f1f2", "surface-container-high": "#e7e8e9",
    "on-primary-container": "#92c0e9", "outline": "#72787f", "surface-container-lowest": "#ffffff",
    "on-background": "#191c1d", "surface-variant": "#e1e3e4", "on-secondary-container": "#566676",
    "surface": "#f8f9fa", "outline-variant": "#c1c7cf", "on-surface-variant": "#41474e",
    "on-secondary": "#ffffff", "secondary": "#506070", "on-secondary-fixed-variant": "#384857",
    "inverse-primary": "#9dcbf4", "surface-tint": "#326286", "surface-container": "#edeeef",
    "primary-container": "#1b4f72", "background": "#f8f9fa", "primary": "#003857"
  }}}
}
</script>
<link rel="stylesheet" href="/ems/public/css/style.css">
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col items-center justify-center p-4">

<?php if ($flash): ?>
<div id="flash-msg" class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] px-6 py-3 rounded-xl shadow-lg text-sm font-bold
  <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<script>setTimeout(() => { const el = document.getElementById('flash-msg'); if(el) el.remove(); }, 4000);</script>
<?php endif; ?>

<main class="w-full max-w-md">
  <!-- Brand Identity -->
  <div class="text-center mb-12">
    <a class="inline-block hover:opacity-80 transition-opacity" href="/ems/index.php?page=home">
      <h2 class="text-primary text-2xl font-black tracking-tighter mb-2">EVENTIFY</h2>
      <div class="h-1 w-12 bg-primary mx-auto rounded-full"></div>
    </a>
  </div>

  <!-- Login Card -->
  <div class="bg-surface-container-lowest rounded-xl p-8 md:p-10 shadow-sm transition-all duration-300">
    <header class="mb-8">
      <h1 class="font-headline text-2xl font-bold tracking-tight text-on-surface">Login to Your Account</h1>
      <p class="text-on-secondary-container text-sm mt-2 font-medium tracking-wide">Enter your credentials to proceed.</p>
    </header>

    <form class="space-y-6" method="POST" action="/ems/controllers/AuthController.php?action=login">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

      <!-- Email -->
      <div class="space-y-1.5">
        <label class="block text-[0.7rem] font-bold tracking-[0.05em] uppercase text-secondary" for="email">EMAIL ADDRESS / USERNAME</label>
        <input class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary-fixed placeholder:text-outline-variant transition-all outline-none"
               id="email" name="email" placeholder="admin" type="text" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
      </div>

      <!-- Password -->
      <div class="space-y-1.5">
        <div class="flex justify-between items-center">
          <label class="block text-[0.7rem] font-bold tracking-[0.05em] uppercase text-secondary" for="password">PASSWORD</label>
          <a class="text-[0.7rem] font-bold text-primary tracking-tight hover:underline" href="#">Forgot Password?</a>
        </div>
        <input class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 text-on-surface focus:ring-2 focus:ring-primary-fixed placeholder:text-outline-variant transition-all outline-none"
               id="password" name="password" placeholder="••••••••" type="password" required/>
      </div>

      <!-- Submit -->
      <button type="submit"
              class="w-full py-3.5 text-white font-bold rounded-md shadow-lg active:scale-[0.98] transition-all flex justify-center items-center gap-2 group"
              style="background: linear-gradient(135deg, #003857 0%, #1b4f72 100%);">
        <span>Login</span>
        <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
      </button>
    </form>

    <footer class="mt-8 pt-6 border-t border-surface-container-high text-center">
      <p class="text-sm text-on-secondary-container">
        Don't have an account?
        <a class="text-primary font-bold hover:underline" href="/ems/index.php?page=register">Register here</a>
      </p>
    </footer>
  </div>

  <!-- small icons -->
  <div class="mt-12 flex flex-col items-center gap-4">
    <div class="flex gap-6">
      <span class="material-symbols-outlined text-outline-variant text-xl">verified_user</span>
      <span class="material-symbols-outlined text-outline-variant text-xl">account_balance</span>
      <span class="material-symbols-outlined text-outline-variant text-xl">school</span>
    </div>
    <p class="text-[0.65rem] font-bold tracking-[0.1em] uppercase text-outline-variant">Secure Academic Portal Access</p>
  </div>
</main>


<div class="fixed top-0 left-0 w-full h-full -z-10 pointer-events-none overflow-hidden">
  <div class="absolute -top-1/4 -right-1/4 w-[60%] h-[60%] bg-primary-fixed-dim/10 rounded-full blur-[120px]"></div>
  <div class="absolute -bottom-1/4 -left-1/4 w-[50%] h-[50%] bg-secondary-fixed/10 rounded-full blur-[100px]"></div>
</div>

<footer class="fixed bottom-0 w-full py-8 text-center pointer-events-none">
  <p class="font-['Inter'] text-xs font-medium tracking-[0.05em] uppercase text-slate-400">&copy; <?= date('Y') ?> EVENTIFY. All rights reserved.</p>
</footer>
</body>
</html>

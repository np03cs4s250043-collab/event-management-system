<?php
// views/auth/register.php
require_once __DIR__ . '/../../core/csrf_helper.php';
require_once __DIR__ . '/../../core/session_helper.php';

if (isLoggedIn()) { header('Location: /ems/index.php?page=home'); exit; }

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Register | EVENTIFY</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { theme: { extend: { colors: {
  "primary-fixed": "#cce5ff", "primary-fixed-dim": "#9dcbf4", "surface-bright": "#f8f9fa",
  "secondary-container": "#d3e4f7", "on-surface": "#191c1d", "secondary-fixed": "#d3e4f7",
  "surface-container-high": "#e7e8e9", "outline": "#72787f", "surface-container-lowest": "#ffffff",
  "on-background": "#191c1d", "surface-variant": "#e1e3e4", "on-secondary-container": "#566676",
  "surface": "#f8f9fa", "outline-variant": "#c1c7cf", "on-surface-variant": "#41474e",
  "on-secondary": "#ffffff", "secondary": "#506070", "surface-container": "#edeeef",
  "primary-container": "#1b4f72", "background": "#f8f9fa", "primary": "#003857"
}}}}
</script>
<link rel="stylesheet" href="/ems/public/css/style.css">
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col items-center justify-center p-6 relative">

<?php if ($flash): ?>
<div id="flash-msg" class="fixed top-4 left-1/2 -translate-x-1/2 z-[9999] px-6 py-3 rounded-xl shadow-lg text-sm font-bold
  <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
  <?= htmlspecialchars($flash['message']) ?>
</div>
<script>setTimeout(() => { const el = document.getElementById('flash-msg'); if(el) el.remove(); }, 4000);</script>
<?php endif; ?>

<!-- Decorative Background -->
<div class="absolute top-0 left-0 w-full h-full pointer-events-none z-0">
  <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-primary-container/5 rounded-full blur-[120px]"></div>
  <div class="absolute bottom-[-10%] right-[-5%] w-[40%] h-[40%] bg-secondary-container/10 rounded-full blur-[120px]"></div>
</div>

<!-- Top Nav -->
<nav class="fixed top-0 w-full z-50 flex justify-between items-center px-8 h-20 bg-[#f8f9fa]/80 backdrop-blur-xl">
  <a class="text-2xl font-black tracking-tighter text-[#1B4F72]" href="/ems/index.php?page=home">EVENTIFY</a>
  <div class="hidden md:flex gap-8 items-center">
    <a class="text-slate-600 font-medium hover:text-[#1B4F72] transition-colors text-sm" href="/ems/index.php?page=home">Home</a>
    <a class="text-slate-600 font-medium hover:text-[#1B4F72] transition-colors text-sm" href="/ems/index.php?page=events">Events</a>
    <a class="text-[#1B4F72] font-bold border-b-2 border-[#1B4F72] pb-1 text-sm" href="#">Register</a>
  </div>
</nav>

<!-- Registration Card -->
<main class="w-full max-w-[520px] z-10 mt-12">
  <div class="bg-surface-container-lowest rounded-xl shadow-none p-8 md:p-12 transition-all duration-300">
    <div class="mb-10 text-center">
      <p class="text-[0.7rem] font-bold tracking-[0.15em] uppercase text-secondary mb-3">Join the Community</p>
      <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-primary leading-tight">Create a New Account</h1>
    </div>

    <form method="POST" action="/ems/controllers/AuthController.php?action=register" class="space-y-6">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

      <!-- Full Name -->
      <div class="space-y-1.5">
        <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="name">Full Name</label>
        <input class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none placeholder:text-outline-variant/60"
               id="name" name="name" placeholder="John Doe" required type="text"
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"/>
      </div>

      <!-- Email -->
      <div class="space-y-1.5">
        <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="email">Email</label>
        <input class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none placeholder:text-outline-variant/60"
               id="email" name="email" placeholder="email@university.edu" required type="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
      </div>

      <!-- Phone -->
      <div class="space-y-1.5">
        <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="phone">Phone Number</label>
        <input class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none placeholder:text-outline-variant/60"
               id="phone" name="phone" placeholder="+977 98XXXXXXXX" type="tel"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"/>
      </div>

      <!-- Role -->
      <div class="space-y-1.5">
        <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="role">Role</label>
        <select class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none appearance-none"
                id="role" name="role">
          <option value="attendee" <?= ($_POST['role'] ?? '') === 'attendee' ? 'selected' : '' ?>>Attendee</option>
          <option value="organizer" <?= ($_POST['role'] ?? '') === 'organizer' ? 'selected' : '' ?>>Event Organizer</option>
        </select>
      </div>

      <!-- Password Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="password">Password</label>
          <input class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none placeholder:text-outline-variant/60"
                 id="password" name="password" placeholder="••••••••" required type="password"/>
        </div>
        <div class="space-y-1.5">
          <label class="text-[0.7rem] font-bold tracking-wider uppercase text-on-surface-variant block ml-1" for="confirm-password">Confirm</label>
          <input class="w-full bg-surface-container-high border-none rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all duration-200 outline-none placeholder:text-outline-variant/60"
                 id="confirm-password" name="confirm_password" placeholder="••••••••" required type="password"/>
        </div>
      </div>

      <!-- Terms -->
      <div class="flex items-start gap-3 pt-2">
        <input class="mt-1 rounded-sm border-outline-variant text-primary focus:ring-primary" id="terms" name="terms" type="checkbox" required/>
        <label class="text-xs text-on-surface-variant leading-relaxed" for="terms">
          I agree to the <a class="text-primary font-semibold hover:underline" href="#">Terms of Service</a> and <a class="text-primary font-semibold hover:underline" href="#">Privacy Policy</a>.
        </label>
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <button class="w-full text-white font-bold py-4 rounded-xl shadow-lg hover:opacity-90 active:scale-[0.98] transition-all duration-200 text-sm tracking-wide uppercase"
                style="background: linear-gradient(135deg, #003857 0%, #1b4f72 100%);" type="submit">
          Register
        </button>
      </div>
    </form>

    <div class="mt-10 text-center">
      <p class="text-sm text-on-surface-variant">Already have an account?
        <a class="text-primary font-bold hover:underline ml-1" href="/ems/index.php?page=login">Login</a>
      </p>
    </div>
  </div>

  <footer class="mt-12 text-center text-outline text-[0.65rem] uppercase tracking-widest space-y-2">
    <p>&copy; <?= date('Y') ?> EVENTIFY. All rights reserved.</p>
    <div class="flex justify-center gap-6">
      <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
      <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
      <a class="hover:text-primary transition-colors" href="#">Contact Support</a>
    </div>
  </footer>
</main>
</body>
</html>

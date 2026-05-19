<?php
// Copy this file to secrets.php and fill in your actual keys.
// secrets.php is gitignored and must never be committed.

define('GROQ_API_KEY', 'your-groq-api-key-here');

// ── eSewa Payment Gateway (production only) ────────────────────────────────
// Leave these commented out in development — sandbox credentials are used automatically.
// Uncomment and fill in for production deployment.
// define('ESEWA_MERCHANT_CODE', 'YOUR_MERCHANT_CODE');
// define('ESEWA_SECRET_KEY',    'YOUR_SECRET_KEY');
// define('ESEWA_PAYMENT_URL',   'https://epay.esewa.com.np/api/epay/main/v2/form');
// define('ESEWA_STATUS_URL',    'https://epay.esewa.com.np/api/epay/transaction/status/');

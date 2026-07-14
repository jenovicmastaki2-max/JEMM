<?php
// Basic security headers for all responses
// Include this file early (it's required inside database.php)

// Prevent MIME sniffing
header('X-Content-Type-Options: nosniff');
// Clickjacking protection
header('X-Frame-Options: SAMEORIGIN');
// Referrer
header('Referrer-Policy: no-referrer-when-downgrade');
// Basic Content Security Policy — adjust for production needs
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' data:; img-src 'self' data:; connect-src 'self'; frame-src 'self' data:; object-src 'none';");
// HSTS (only if serving HTTPS)
// header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

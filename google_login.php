<?php
/**
 * google_login.php
 * -------------------------------------------------------
 * Redirects the browser to Google's OAuth consent screen.
 * 
 * Place this file in the ROOT of your project (same folder as index.php).
 * The "Continue with Google" button on your login page should link here.
 */

session_start();

// ── Same credentials as google_callback.php ──────────────────────────────────
define('GOOGLE_CLIENT_ID',    '240563055427-f8m83d6t72de5ck1leqrvuduenbghoon.apps.googleusercontent.com');
define('GOOGLE_REDIRECT_URI', 'https://eusebianationalhighschool.gt.tc/google_callback.php');
// ─────────────────────────────────────────────────────────────────────────────

// Generate a random CSRF state token and store it in session
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

$params = http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'online',
    'prompt'        => 'select_account',  // always show account chooser
]);

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
exit();
<?php
/**
 * oauth_social.php — Social Login Handler (Google & Facebook)
 *
 * HOW TO SET UP:
 * 1. Google:
 *    - Go to https://console.cloud.google.com/
 *    - Create a project → Enable "Google+ API" or "Google Identity"
 *    - Credentials → OAuth 2.0 Client ID → Web Application
 *    - Add Authorized redirect URI: http://yourdomain.com/oauth_social.php?provider=google
 *    - Copy Client ID and Client Secret into GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET below
 *
 * 2. Facebook:
 *    - Go to https://developers.facebook.com/
 *    - Create App → Consumer → Add "Facebook Login" product
 *    - Settings → Valid OAuth Redirect URIs: http://yourdomain.com/oauth_social.php?provider=facebook
 *    - Copy App ID and App Secret into FACEBOOK_APP_ID / FACEBOOK_APP_SECRET below
 *
 * 3. Install Composer and run: composer require league/oauth2-google league/oauth2-facebook
 *    OR use the built-in curl approach below (no Composer needed).
 */

error_reporting(E_ALL ^ E_WARNING);
if (!isset($_SESSION)) {
    session_start();
}

require_once('classes/conn.php');    // provides $conn (PDO)
require_once('classes/main.class.php');

// ─────────────────────────────────────────────
// ①  CONFIGURATION — fill these in
// ─────────────────────────────────────────────
define('BASE_URL',    'http://localhost/eusebia-main'); // <-- change to your domain
define('REDIRECT_URI', BASE_URL . '/oauth_social.php');

// Google
define('GOOGLE_CLIENT_ID',     'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');

// Facebook
define('FACEBOOK_APP_ID',     'YOUR_FACEBOOK_APP_ID');
define('FACEBOOK_APP_SECRET', 'YOUR_FACEBOOK_APP_SECRET');

// ─────────────────────────────────────────────
// ②  ROUTE
// ─────────────────────────────────────────────
$provider = isset($_GET['provider']) ? strtolower(trim($_GET['provider'])) : '';
$code     = isset($_GET['code'])     ? $_GET['code']  : null;
$state    = isset($_GET['state'])    ? $_GET['state'] : null;

if (!in_array($provider, ['google', 'facebook'])) {
    redirect_with_error('Invalid OAuth provider.');
}

// Step 1 — Redirect user to provider
if (!$code) {
    $csrf = bin2hex(random_bytes(16));
    $_SESSION['oauth_state']    = $csrf;
    $_SESSION['oauth_provider'] = $provider;

    if ($provider === 'google') {
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => REDIRECT_URI . '?provider=google',
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $csrf,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);
    } else { // facebook
        $url = 'https://www.facebook.com/v19.0/dialog/oauth?' . http_build_query([
            'client_id'     => FACEBOOK_APP_ID,
            'redirect_uri'  => REDIRECT_URI . '?provider=facebook',
            'state'         => $csrf,
            'scope'         => 'email,public_profile',
            'response_type' => 'code',
        ]);
    }

    header('Location: ' . $url);
    exit;
}

// Step 2 — Handle callback
// CSRF check
if (!isset($_SESSION['oauth_state']) || $_SESSION['oauth_state'] !== $state) {
    redirect_with_error('State mismatch. Possible CSRF attack.');
}
unset($_SESSION['oauth_state']);

// Exchange code for access token
if ($provider === 'google') {
    $token_data = curl_post('https://oauth2.googleapis.com/token', [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => REDIRECT_URI . '?provider=google',
        'grant_type'    => 'authorization_code',
    ]);

    if (empty($token_data['access_token'])) {
        redirect_with_error('Google token exchange failed.');
    }

    // Fetch user info
    $user_info = curl_get(
        'https://www.googleapis.com/oauth2/v3/userinfo',
        $token_data['access_token']
    );

    $social_id    = $user_info['sub']            ?? null;
    $email        = $user_info['email']           ?? null;
    $name         = $user_info['name']            ?? '';
    $given_name   = $user_info['given_name']      ?? '';
    $family_name  = $user_info['family_name']     ?? '';
    $avatar       = $user_info['picture']         ?? '';
    $provider_key = 'google';

} else { // facebook
    $token_data = curl_post('https://graph.facebook.com/v19.0/oauth/access_token', [
        'code'          => $code,
        'client_id'     => FACEBOOK_APP_ID,
        'client_secret' => FACEBOOK_APP_SECRET,
        'redirect_uri'  => REDIRECT_URI . '?provider=facebook',
    ]);

    if (empty($token_data['access_token'])) {
        redirect_with_error('Facebook token exchange failed.');
    }

    $user_info = curl_get(
        'https://graph.facebook.com/me?fields=id,name,first_name,last_name,email,picture',
        $token_data['access_token']
    );

    $social_id    = $user_info['id']                        ?? null;
    $email        = $user_info['email']                     ?? null;
    $name         = $user_info['name']                      ?? '';
    $given_name   = $user_info['first_name']                ?? '';
    $family_name  = $user_info['last_name']                 ?? '';
    $avatar       = $user_info['picture']['data']['url']    ?? '';
    $provider_key = 'facebook';
}

if (!$social_id) {
    redirect_with_error('Could not retrieve user ID from ' . ucfirst($provider) . '.');
}

// ─────────────────────────────────────────────
// ③  DATABASE — look up or create resident
// ─────────────────────────────────────────────
/*
 * You need a column in tbl_resident (or a separate table) to store the
 * social provider ID.  Run this SQL once in your database:
 *
 *   ALTER TABLE tbl_resident
 *     ADD COLUMN social_provider   VARCHAR(20)  NULL,
 *     ADD COLUMN social_id         VARCHAR(128) NULL,
 *     ADD COLUMN social_avatar_url VARCHAR(512) NULL,
 *     ADD UNIQUE KEY uq_social (social_provider, social_id);
 *
 * Adjust tbl_resident and column names to match your actual schema.
 */

try {
    // 1. Try to find by (provider, social_id)
    $stmt = $conn->prepare("
        SELECT * FROM tbl_resident
        WHERE social_provider = ? AND social_id = ?
        LIMIT 1
    ");
    $stmt->execute([$provider_key, $social_id]);
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. If not found by social ID, try by email (link accounts)
    if (!$resident && $email) {
        $stmt = $conn->prepare("
            SELECT * FROM tbl_resident WHERE email = ? LIMIT 1
        ");
        $stmt->execute([$email]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);

        // Link the social ID to the existing account
        if ($resident) {
            $stmt = $conn->prepare("
                UPDATE tbl_resident
                SET social_provider = ?, social_id = ?, social_avatar_url = ?
                WHERE id = ?
            ");
            $stmt->execute([$provider_key, $social_id, $avatar, $resident['id']]);
        }
    }

    // 3. Auto-register new resident (basic profile — they can complete later)
    if (!$resident) {
        // Generate a random unguessable password (they won't use it; social login only)
        $random_pass = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO tbl_resident
                (firstname, lastname, email, password, social_provider, social_id, social_avatar_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $given_name  ?: $name,
            $family_name ?: '',
            $email       ?: '',
            $random_pass,
            $provider_key,
            $social_id,
            $avatar,
        ]);

        $new_id   = $conn->lastInsertId();
        $stmt     = $conn->prepare("SELECT * FROM tbl_resident WHERE id = ? LIMIT 1");
        $stmt->execute([$new_id]);
        $resident = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$resident) {
        redirect_with_error('Account setup failed. Please try again.');
    }

    // ─────────────────────────────────────────────
    // ④  SET SESSION and redirect
    // ─────────────────────────────────────────────
    // Mirror whatever set_userdata() does in main.class.php
    $_SESSION['user_id']     = $resident['id'];
    $_SESSION['user_email']  = $resident['email']     ?? $email;
    $_SESSION['user_name']   = ($resident['firstname'] ?? $given_name) . ' ' . ($resident['lastname'] ?? $family_name);
    $_SESSION['user_type']   = 'resident';
    $_SESSION['social_auth'] = $provider_key;

    header('Location: resident_dashboard.php');
    exit;

} catch (PDOException $e) {
    // Log actual error server-side; show generic message to user
    error_log('oauth_social.php DB error: ' . $e->getMessage());
    redirect_with_error('A database error occurred. Please contact the administrator.');
}


// ─────────────────────────────────────────────
// HELPER FUNCTIONS
// ─────────────────────────────────────────────

function curl_post(string $url, array $params): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($params),
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response ?: '{}', true) ?? [];
}

function curl_get(string $url, string $access_token): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $access_token],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response ?: '{}', true) ?? [];
}

function redirect_with_error(string $msg): never {
    $_SESSION['login_error'] = $msg;
    header('Location: login.php');
    exit;
}
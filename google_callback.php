<?php
/**
 * google_callback.php
 * -------------------------------------------------------
 * Place in ROOT of your project (same folder as index.php / login.php).
 * 
 * Google redirects here after user approves the consent screen.
 * This file:
 *   1. Exchanges the code for an access token
 *   2. Fetches the Google profile (name + email)
 *   3. If email found in tbl_resident  → logs them in
 *   4. If email NOT found              → auto-registers in tbl_resident, then logs in
 *   5. Redirects to resident_homepage.php
 */

session_start();

// ── Database connection ───────────────────────────────────────────────────────
require_once('classes/conn.php');       // provides $conn (PDO)
require_once('classes/main.class.php'); // EUSEBIAClass (has set_userdata)

// ── Your Google OAuth credentials (from Google Cloud Console) ────────────────
$client_id     = '240563055427-f8m83d6t72de5ck1leqrvuduenbghoon.apps.googleusercontent.com';
$client_secret = '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'; // <-- add this from Cloud Console
$redirect_uri  = 'https://eusebianationalhighschool.gt.tc/google_callback.php';
// ─────────────────────────────────────────────────────────────────────────────

// Error from Google (user cancelled, etc.)
if (isset($_GET['error'])) {
    $_SESSION['google_error'] = 'Google login was cancelled or failed: ' . htmlspecialchars($_GET['error']);
    header('Location: login.php');
    exit();
}

// No code = bad redirect
if (empty($_GET['code'])) {
    $_SESSION['google_error'] = 'Google login failed: no authorization code received.';
    header('Location: login.php');
    exit();
}

// ── Step 1: Exchange code for access token ────────────────────────────────────
$token_url  = 'https://oauth2.googleapis.com/token';
$post_data  = http_build_query([
    'code'          => $_GET['code'],
    'client_id'     => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri'  => $redirect_uri,
    'grant_type'    => 'authorization_code',
]);

$context = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $post_data,
        'timeout' => 10,
    ],
]);

$token_response = @file_get_contents($token_url, false, $context);
$token_data     = json_decode($token_response, true);

if (empty($token_data['access_token'])) {
    $_SESSION['google_error'] = 'Google login failed: could not obtain access token. Please try again.';
    header('Location: login.php');
    exit();
}

// ── Step 2: Fetch Google user profile ────────────────────────────────────────
$profile_context = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'header'  => "Authorization: Bearer " . $token_data['access_token'] . "\r\n",
        'timeout' => 10,
    ],
]);

$profile_response = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, $profile_context);
$profile          = json_decode($profile_response, true);

if (empty($profile['email'])) {
    $_SESSION['google_error'] = 'Google login failed: could not retrieve your Google profile.';
    header('Location: login.php');
    exit();
}

// ── Extract profile data ──────────────────────────────────────────────────────
$google_email  = trim($profile['email']);
$google_fname  = $profile['given_name']  ?? '';
$google_lname  = $profile['family_name'] ?? '';

// ── Step 3: Check if resident already exists ──────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM tbl_resident WHERE email = ? LIMIT 1");
$stmt->execute([$google_email]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

$eusebia = new EUSEBIAClass();

if ($resident) {
    // ── EXISTING: just log in ─────────────────────────────────────────────────
    $eusebia->set_userdata($resident);
    header('Location: resident_homepage.php');
    exit();
}

// ── Step 4: NEW RESIDENT — auto-register ─────────────────────────────────────
try {
    $stmt = $conn->prepare("
        INSERT INTO tbl_resident (
            `email`, `phone_number`, `password`,
            `lname`, `fname`, `mi`,
            `age`, `sex`, `status`,
            `houseno`, `street`, `brgy`, `municipal`,
            `contact`, `bdate`, `bplace`, `nationality`,
            `addedby`
        ) VALUES (
            ?, NULL, ?,
            ?, ?, '',
            0, '', '',
            '', '', '', '',
            '', '', '', '',
            'Google'
        )
    ");

    // Random secure password — login is via Google so it will never be used directly
    $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

    $stmt->execute([
        $google_email,
        $random_password,
        $google_lname,
        $google_fname,
    ]);

    // Fetch the brand-new row to build the session
    $new_id   = $conn->lastInsertId();
    $stmt2    = $conn->prepare("SELECT * FROM tbl_resident WHERE id_resident = ? LIMIT 1");
    $stmt2->execute([$new_id]);
    $new_resident = $stmt2->fetch(PDO::FETCH_ASSOC);

    $eusebia->set_userdata($new_resident);
    header('Location: resident_homepage.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['google_error'] = 'Registration error: ' . $e->getMessage();
    header('Location: login.php');
    exit();
}
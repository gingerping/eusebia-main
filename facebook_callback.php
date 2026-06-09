<?php
/**
 * facebook_callback.php
 * -------------------------------------------------------
 * Place in ROOT of your project (same folder as index.php / login.php).
 *
 * Facebook redirects here after the user approves the login dialog.
 * This file:
 *   1. Exchanges the code for an access token
 *   2. Fetches the Facebook profile (name + email)
 *   3. If email found in tbl_resident  → logs them in
 *   4. If email NOT found              → auto-registers in tbl_resident, then logs in
 *   5. Redirects to resident_homepage.php
 */

session_start();

// ── Credentials (already in your app) ────────────────────────────────────────
define('FB_APP_ID',      '1384598473521605');
define('FB_APP_SECRET',  '37976203ecf0cedc1a28d93ebfc9e3ac');
define('FB_REDIRECT_URI','https://eusebianationalhighschool.gt.tc/facebook_callback.php');
// ─────────────────────────────────────────────────────────────────────────────

require_once('classes/conn.php');       // provides $conn (PDO)
require_once('classes/main.class.php'); // EUSEBIAClass (has set_userdata)

// ── Error from Facebook (user cancelled, etc.) ────────────────────────────────
if (isset($_GET['error'])) {
    $_SESSION['google_error'] = 'Facebook login was cancelled or failed: ' . htmlspecialchars($_GET['error_description'] ?? $_GET['error']);
    header('Location: login.php');
    exit();
}

// ── No code = bad redirect ────────────────────────────────────────────────────
if (empty($_GET['code'])) {
    $_SESSION['google_error'] = 'Facebook login failed: no authorization code received.';
    header('Location: login.php');
    exit();
}

// ── Step 1: Exchange code for access token ────────────────────────────────────
$token_url = 'https://graph.facebook.com/v18.0/oauth/access_token?' . http_build_query([
    'client_id'     => FB_APP_ID,
    'client_secret' => FB_APP_SECRET,
    'redirect_uri'  => FB_REDIRECT_URI,
    'code'          => $_GET['code'],
]);

$ch = curl_init($token_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 10,
]);
$token_response = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($token_response['access_token'])) {
    $_SESSION['google_error'] = 'Facebook login failed: could not obtain access token. Please try again.';
    header('Location: login.php');
    exit();
}

$access_token = $token_response['access_token'];

// ── Step 2: Fetch Facebook user profile ──────────────────────────────────────
// Note: 'email' permission must be approved in your Facebook App settings.
$user_url = 'https://graph.facebook.com/me?' . http_build_query([
    'fields'       => 'id,name,first_name,last_name,email',
    'access_token' => $access_token,
]);

$ch = curl_init($user_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 10,
]);
$fb_user = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($fb_user['id'])) {
    $_SESSION['google_error'] = 'Facebook login failed: could not retrieve your Facebook profile.';
    header('Location: login.php');
    exit();
}

// ── Extract profile fields ────────────────────────────────────────────────────
// Facebook may not always return email (e.g. if user signed up with phone).
// Fall back to a unique placeholder so the INSERT doesn't fail a NOT NULL constraint.
$fb_email = $fb_user['email']      ?? ($fb_user['id'] . '@facebook.com');
$fb_fname = $fb_user['first_name'] ?? ($fb_user['name'] ?? 'Facebook');
$fb_lname = $fb_user['last_name']  ?? 'User';

// ── Step 3: Check if resident already exists ──────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM tbl_resident WHERE email = ? LIMIT 1");
$stmt->execute([$fb_email]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

$eusebia = new EUSEBIAClass();

if ($resident) {
    // ── EXISTING: just log in ─────────────────────────────────────────────────
    $eusebia->set_userdata($resident);
    header('Location: resident_homepage.php');
    exit();
}

// ── Step 4: NEW RESIDENT — auto-register ─────────────────────────────────────
// Only columns that actually exist in tbl_resident are used here.
// (Matching the exact schema from resident.class.php → create_resident)
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
            'Facebook'
        )
    ");

    // A random unusable password — login is always via Facebook OAuth
    $random_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

    $stmt->execute([
        $fb_email,
        $random_password,
        $fb_lname,
        $fb_fname,
    ]);

    // Fetch the newly created row to build the session correctly
    $new_id = $conn->lastInsertId();
    $stmt2  = $conn->prepare("SELECT * FROM tbl_resident WHERE id_resident = ? LIMIT 1");
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
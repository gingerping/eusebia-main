<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL);
if(!isset($_SESSION)) {
    $showdate = date("Y-m-d");
    date_default_timezone_set('Asia/Manila');
    $showtime = date("h:i:a");
    $_SESSION['storedate'] = $showdate;
    $_SESSION['storetime'] = $showdate;
    session_start();
}

require('classes/main.class.php');
$eusebia->login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>EPANHS | Login</title>
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#0b2b5c">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        /* ── NAVBAR ── */
        .navbar-custom {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
            padding: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .8rem 1.5rem;
            gap: .75rem;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: clamp(.95rem, 3vw, 1.35rem);
            color: white !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-shrink: 1;
            min-width: 0;
        }

        .navbar-brand span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-portal {
            border-radius: 40px;
            padding: 7px 18px;
            font-weight: 500;
            font-size: .875rem;
            transition: all .2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            background: rgba(255,215,0,.8);
            border: 1px solid #ffd700;
            color: #0b2b5c;
            font-weight: 600;
            flex-shrink: 0;
        }

        .btn-portal:hover {
            background: #ffd700;
            transform: translateY(-2px);
            color: #0b2b5c;
        }

        @media (max-width: 400px) {
            .btn-portal .btn-label { display: none; }
            .btn-portal { padding: 8px 11px; border-radius: 50%; }
            .navbar-inner { padding: .7rem 1rem; }
        }

        /* ── PAGE BODY ── */
        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65)), url('icons/eusebia.jpg');
            background-size: cover;
            background-position: center;
            min-height: calc(100vh - 57px);
            display: flex;
            flex-direction: column;
        }

        .page-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* ── LOGIN CONTAINER ── */
        .login-container {
            max-width: 460px;
            width: 100%;
            margin: 0 auto;
        }

        .header-content {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            width: 88px;
            height: 88px;
            background: white;
            border-radius: 50%;
            padding: 11px;
            box-shadow: 0 8px 20px rgba(0,0,0,.15);
            margin-bottom: 1rem;
            transition: transform .2s;
        }

        .login-logo:hover { transform: scale(1.03); }

        .system-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: clamp(1.3rem, 4vw, 1.75rem);
            color: #ffffff;
            margin-bottom: .25rem;
        }

        .sub-title {
            color: rgba(255,215,0,.85);
            font-size: .88rem;
            letter-spacing: .5px;
        }

        /* ── CARD ── */
        .login-card {
            background: white;
            border: none;
            border-radius: 32px;
            box-shadow: 0 25px 45px -12px rgba(0,0,0,.3);
            overflow: hidden;
        }

        .login-card .card-body { padding: 2rem 1.8rem; }

        /* ── FORM ── */
        .form-label {
            font-weight: 600;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #1f3a5f;
            margin-bottom: .45rem;
            display: block;
        }

        .input-group-custom {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            transition: all .2s;
            margin-bottom: 1.2rem;
        }

        .input-group-custom:focus-within {
            border-color: #2a6f9c;
            box-shadow: 0 0 0 3px rgba(42,111,156,.14);
            background: white;
        }

        .input-icon {
            padding: .75rem 0 .75rem 1.15rem;
            color: #2a6f9c;
            font-size: .95rem;
        }

        .input-field {
            width: 100%;
            padding: .75rem 1rem .75rem .5rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: .95rem;
            font-weight: 500;
            color: #1a2c3e;
        }

        .input-field::placeholder { color: #a0afc0; font-weight: 400; }

        .form-switch-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .form-switch-custom .form-check-input {
            width: 2rem;
            cursor: pointer;
            background-color: #cbd5e1;
            border-color: #94a3b8;
        }

        .form-switch-custom .form-check-input:checked {
            background-color: #2a6f9c;
            border-color: #2a6f9c;
        }

        .form-switch-custom label {
            font-size: .85rem;
            color: #334155;
            cursor: pointer;
        }

        /* ── SOCIAL BUTTONS ── */
        .social-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.2rem 0;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .social-divider span {
            font-size: .78rem;
            color: #94a3b8;
            font-weight: 500;
            white-space: nowrap;
        }

        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;
            width: 100%;
            padding: .72rem 1rem;
            border-radius: 40px;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            border: 1.5px solid;
            margin-bottom: .75rem;
        }

        .btn-google {
            background: #ffffff;
            border-color: #dadce0;
            color: #3c4043;
        }

        .btn-google:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
            transform: translateY(-1px);
            color: #3c4043;
        }

        .btn-facebook {
            background: #1877f2;
            border-color: #1877f2;
            color: #ffffff;
        }

        .btn-facebook:hover {
            background: #0c63d4;
            border-color: #0c63d4;
            box-shadow: 0 2px 10px rgba(24,119,242,.35);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .btn-social .social-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* ── BUTTONS ── */
        .btn-login-submit {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            border: none;
            border-radius: 40px;
            padding: .8rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: all .2s;
            margin-bottom: 1.2rem;
            cursor: pointer;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1f3a6b, #2a6f9c);
            box-shadow: 0 8px 18px rgba(11,43,92,.25);
        }

        .btn-register-link {
            background: #eef2ff;
            border: 1.5px solid #cbd5e1;
            border-radius: 40px;
            padding: .7rem;
            font-weight: 600;
            font-size: .9rem;
            width: 100%;
            color: #1f3a5f;
            transition: all .2s;
            cursor: pointer;
        }

        .btn-register-link:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        hr { margin: 1.5rem 0; opacity: .25; }

        /* ── FOOTER ── */
        footer {
            background: #0b1f33;
            color: #cddcec;
            padding: 2rem 1rem;
            text-align: center;
            font-size: 0.9rem;
            border-top-left-radius: 32px;
            border-top-right-radius: 32px;
            margin-top: 3rem;
        }

        @media (max-width: 500px) {
            .login-card .card-body { padding: 1.5rem 1.3rem; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-custom">
    <div class="navbar-inner">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-mortarboard-fill" style="flex-shrink:0;"></i>
            <span>EPAMNHS Portal</span>
        </a>
        <a href="index.php" class="btn-portal">
            <i class="fas fa-arrow-left"></i>
            <span class="btn-label">Back to Main Portal</span>
        </a>
    </div>
</nav>

<!-- ── PAGE ── -->
<div class="page-body">
    <div class="login-container">

        <div class="header-content">
            <img src="icons/Documents/eusebia.png" class="login-logo" alt="School Seal">
            <h2 class="system-title">Eusebia Paz Arroyo Memorial National High School</h2>
            <p class="sub-title">Buluang, Baao, Camarines Sur</p>
        </div>

        <div class="card login-card">
            <div class="card-body">
                <?php if (!empty($_SESSION['google_error'])): ?>
                    <div class="alert alert-danger" style="border-radius:14px; font-size:.88rem;">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= $_SESSION['google_error']; unset($_SESSION['google_error']); ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <label class="form-label">Email or Phone</label>
                    <div class="input-group-custom">
                        <div class="input-icon"><i class="fas fa-envelope"></i></div>
                        <input class="input-field" type="text" placeholder="your.email@example.com" name="login_identity" required autofocus>
                    </div>

                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <div class="input-icon"><i class="fas fa-key"></i></div>
                        <input class="input-field" type="password" placeholder="••••••••" id="myInput" name="password" required>
                    </div>

                    <div class="form-switch-custom">
                        <input class="form-check-input" type="checkbox" onclick="myFunction()" id="showPasswordSwitch">
                        <label for="showPasswordSwitch">Show password</label>
                    </div>

                    <button class="btn-login-submit" type="submit" name="login">
                        <i class="fas fa-sign-in-alt me-2"></i> Log in
                    </button>
                </form>
<div class="text-end mb-3" style="margin-top:-0.8rem;">
    <a href="forgot_password.php" style="font-size:.85rem; color:#2a6f9c; text-decoration:none; font-weight:500;">
        <i class="fas fa-question-circle me-1"></i> Forgot Password?
    </a>
</div>
                <div class="social-divider"><span>or continue with</span></div>

                <a href="#" class="btn-social btn-google" onclick="handleGoogleLogin(event)">
                    <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Continue with Google
                </a>

               
                <hr>

                <div class="text-center mb-1">
                    <p class="mb-2 fw-semibold" style="font-size:.92rem; color:#334155;">Don't have an account yet?</p>
                    <button class="btn-register-link" onclick="window.location.href='student_registration.php';">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0 text-md-start">
                <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">&copy; <?= date('Y') ?> EPAMNHS Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<script src="js/pwa.js"></script>
<script>
    function myFunction() {
        var x = document.getElementById("myInput");
        x.type = (x.type === "password") ? "text" : "password";
    }

    function handleGoogleLogin(e) {
        e.preventDefault();
        var params = new URLSearchParams({
            client_id:     '240563055427-f8m83d6t72de5ck1leqrvuduenbghoon.apps.googleusercontent.com',
            redirect_uri:  'https://eusebianationalhighschool.gt.tc/google_callback.php',
            response_type: 'code',
            scope:         'openid email profile',
            prompt:        'select_account'
        });
        window.location.href = 'https://accounts.google.com/o/oauth2/v2/auth?' + params.toString();
    }

    function handleFacebookLogin(e) {
        e.preventDefault();
        var params = new URLSearchParams({
            client_id:     '1384598473521605',
            redirect_uri:  'https://eusebianationalhighschool.gt.tc/facebook_callback.php',
            response_type: 'code',
            scope:         'email,public_profile',
            auth_type:     'rerequest'
        });
        window.location.href = 'https://www.facebook.com/v18.0/dialog/oauth?' + params.toString();
    }
</script>

</body>
</html>

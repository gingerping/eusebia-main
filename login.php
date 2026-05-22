<?php 
error_reporting(E_ALL ^ E_WARNING);

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
            padding: 1rem;
            text-align: center;
            font-size: .78rem;
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

                <hr>

                <div class="text-center mb-1">
                    <p class="mb-2 fw-semibold" style="font-size:.92rem; color:#334155;">Don't have an account yet?</p>
                    <button class="btn-register-link" onclick="window.location.href='resident_registration.php';">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<footer class="footer-custom">
  <div class="container">
    <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
    <br><small><?= date('Y') ?> EPAMNHS. All rights reserved.</small>
  </div>
</footer>

<script>
    function myFunction() {
        var x = document.getElementById("myInput");
        x.type = (x.type === "password") ? "text" : "password";
    }
</script>

</body>
</html>
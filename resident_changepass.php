<?php 
error_reporting(E_ALL ^ E_WARNING);
require('classes/resident.class.php');

$userdetails = $residenteusebia->get_userdata();
$residenteusebia->resident_changepass();

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$current_date = $dt->format('l, F j, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>EPAMNHS | Change Password</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AOS Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
            color: #1a2c3e;
            scroll-behavior: smooth;
        }

        /* ========== NAVBAR (same as portal) ========== */
        .navbar-custom {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
            backdrop-filter: blur(8px);
            padding: 0.9rem 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.3px;
            color: white !important;
            transition: transform 0.2s;
        }
        .navbar-brand:hover { transform: scale(1.02); }
        .dropdown-toggle-custom {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(4px);
            border-radius: 40px;
            padding: 8px 20px;
            border: 1px solid rgba(255,255,255,0.25);
            color: white !important;
            font-weight: 500;
            transition: all 0.2s;
        }
        .dropdown-toggle-custom:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
        }
        .dropdown-menu-custom {
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
            padding: 12px 6px;
            min-width: 210px;
            background: #ffffffdd;
            backdrop-filter: blur(12px);
        }
        .dropdown-item-custom {
            border-radius: 16px;
            padding: 10px 18px;
            font-weight: 500;
            transition: all 0.2s;
            color: #0b2b5c;
        }
        .dropdown-item-custom i { width: 28px; margin-right: 6px; }
        .dropdown-item-custom:hover {
            background: #eef2ff;
            transform: translateX(5px);
        }
        .home-icon {
            color: white;
            font-size: 1.4rem;
            margin-right: 1rem;
            transition: 0.2s;
        }
        .home-icon:hover {
            transform: scale(1.08);
            color: #f0f4fe;
        }

        /* ========== CHANGE PASSWORD CARD ========== */
        .password-card {
            max-width: 550px;
            margin: 2rem auto;
            background: white;
            border-radius: 36px;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            padding: 1.5rem;
            text-align: center;
            color: white;
        }
        .card-header-custom h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            margin: 0;
        }
        .card-body-custom {
            padding: 2rem;
        }
        .input-group-custom {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            transition: all 0.2s;
            margin-bottom: 1.2rem;
            position: relative;
        }
        .input-group-custom:focus-within {
            border-color: #2a6f9c;
            box-shadow: 0 0 0 3px rgba(42,111,156,0.15);
            background: white;
        }
        .input-icon {
            padding: 0.75rem 0 0.75rem 1.2rem;
            color: #2a6f9c;
            font-size: 1rem;
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 0.5rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .toggle-password {
            position: absolute;
            right: 1rem;
            cursor: pointer;
            color: #94a3b8;
            transition: 0.2s;
        }
        .toggle-password:hover {
            color: #2a6f9c;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1f3a5f;
            margin-bottom: 0.5rem;
        }
        #message {
            font-size: 0.8rem;
            margin: -0.5rem 0 1rem 0;
            display: block;
        }
        .btn-change {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            border: none;
            border-radius: 40px;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: all 0.2s;
        }
        .btn-change:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1f3a6b, #2a6f9c);
            box-shadow: 0 8px 18px rgba(11,43,92,0.25);
        }

        /* ========== BACK TO TOP ========== */
        .top-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #0b2b5c;
            width: 48px;
            height: 48px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 99;
            text-decoration: none;
            opacity: 0;
            visibility: hidden;
        }
        .top-link.show {
            opacity: 1;
            visibility: visible;
        }
        .top-link:hover {
            background: #1f5a9e;
            transform: translateY(-5px);
            color: white;
        }

        /* ========== FOOTER ========== */
        .footer-custom {
            background: #0b1f33;
            color: #cddcec;
            padding: 2rem 1rem;
            text-align: center;
            font-size: 0.9rem;
            border-top-left-radius: 32px;
            border-top-right-radius: 32px;
            margin-top: 3rem;
        }

        @media (max-width: 600px) {
            .password-card { margin: 1rem; }
            .card-body-custom { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">
            <i class="bi bi-mortarboard-fill me-2"></i> EPAMNHS Portal
        </a>
        <div class="ms-auto d-flex align-items-center">
            <a href="resident_homepage.php" class="home-icon me-3" data-bs-toggle="tooltip" title="Home">
                <i class="fas fa-home fa-lg"></i>
            </a>
            <div class="dropdown">
                <button class="btn dropdown-toggle-custom dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2"></i> <?= htmlspecialchars($userdetails['surname'] . ', ' . $userdetails['firstname']); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item dropdown-item-custom" href="resident_profile.php?id_resident=<?= $userdetails['id_resident'] ?>"><i class="fas fa-id-card"></i> My Profile</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'] ?>"><i class="fas fa-key"></i> Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- ========== CHANGE PASSWORD CARD ========== -->
<div class="container">
    <div class="password-card" data-aos="fade-up">
        <div class="card-header-custom">
            <h3><i class="fas fa-lock me-2"></i>Change Password</h3>
            <p class="mb-0 opacity-75 small">Secure your account</p>
        </div>
        <div class="card-body-custom">
           <form method="post">
    <input type="hidden" name="id_resident" value="<?= $userdetails['id_resident'] ?>">

    <div class="form-label">Current Password</div>
    <div class="input-group-custom">
        <div class="input-icon"><i class="fas fa-lock"></i></div>
        <input class="input-field" type="password" name="oldpassword" required>
    </div>

    <div class="form-label">New Password</div>
    <div class="input-group-custom">
        <div class="input-icon"><i class="fas fa-key"></i></div>
        <input class="input-field" type="password" name="newpassword" required>
    </div>

    <div class="form-label">Confirm New Password</div>
    <div class="input-group-custom">
        <div class="input-icon"><i class="fas fa-user-lock"></i></div>
        <input class="input-field" type="password" name="checkpassword" required>
    </div>

    <button class="btn btn-change" type="submit" name="resident_changepass">Update Password</button>
</form>
        </div>
    </div>
</div>

<!-- Back to Top Button -->
<a href="#" class="top-link" id="backToTopBtn">
    <i class="fas fa-arrow-up"></i>
</a>
<br><br><br><br><br><br>
<!-- Footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School<br>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0"> <?= date('Y') ?> EPAMNHS Portal.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 40 });

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('toggle'));
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });

    // Password match validation
    const newPwd = document.getElementById('newPassword');
    const confirmPwd = document.getElementById('confirmPassword');
    const messageSpan = document.getElementById('message');

    function validateMatch() {
        if (newPwd.value === confirmPwd.value && newPwd.value !== '') {
            messageSpan.innerHTML = '✓ Passwords match';
            messageSpan.style.color = '#2e7d32';
        } else if (confirmPwd.value !== '') {
            messageSpan.innerHTML = '✗ Passwords do not match';
            messageSpan.style.color = '#d32f2f';
        } else {
            messageSpan.innerHTML = '';
        }
    }

    newPwd.addEventListener('keyup', validateMatch);
    confirmPwd.addEventListener('keyup', validateMatch);

    // Back to top
    const backBtn = document.getElementById('backToTopBtn');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) backBtn.classList.add('show');
        else backBtn.classList.remove('show');
    });
    backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tooltips (if any)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>
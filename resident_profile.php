<?php 
    error_reporting(E_ALL ^ E_WARNING);
    require('classes/resident.class.php');
    ini_set('display_errors',0);
    $userdetails = $residenteusebia->get_userdata();
    $id_resident = $_GET['id_resident'];
    $resident = $residenteusebia->get_single_resident($id_resident);
    

    $residenteusebia->profile_update();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>EPAMNHS | My Profile</title>

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

        /* ========== PROFILE CARD ========== */
        .profile-card {
            max-width: 1100px;
            margin: 2rem auto;
            background: white;
            border-radius: 36px;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .profile-header {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            padding: 1.5rem 2rem;
            color: white;
        }
        .profile-header h3 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            margin: 0;
        }
        .profile-header p {
            margin: 0;
            opacity: 0.85;
            font-size: 0.9rem;
        }
        .profile-body {
            padding: 2rem;
        }
        .info-section {
            background: #f8fafd;
            border-radius: 24px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .info-section h6 {
            font-weight: 700;
            color: #0b2b5c;
            border-left: 4px solid #2a6f9c;
            padding-left: 12px;
            margin-bottom: 1rem;
        }
        .form-control, .form-select {
            border-radius: 16px;
            border: 1px solid #dee2e6;
            padding: 0.6rem 1rem;
            background: white;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2a6f9c;
            box-shadow: 0 0 0 0.2rem rgba(42,111,156,0.25);
        }
        .btn-update {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            border: none;
            border-radius: 40px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            color: white;
            transition: 0.2s;
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #1f3a6b, #2a6f9c);
            transform: translateY(-2px);
        }
        .btn-household {
            background: #eef2ff;
            border: 1px solid #cbd5e1;
            border-radius: 40px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            color: #1f3a5f;
            transition: 0.2s;
        }
        .btn-household:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
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

        @media (max-width: 768px) {
            .profile-body { padding: 1.2rem; }
            .info-section { padding: 1rem; }
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

<!-- ========== PROFILE MAIN CARD ========== -->
<div class="container">
    <div class="profile-card" data-aos="fade-up">
        <div class="profile-header">
            <h3><i class="fas fa-user-graduate me-2"></i>Student Profile</h3>
            <p><?= $current_date ?> | EPAMNHS – Buluang, Baao, Camarines Sur</p>
        </div>
        <div class="profile-body">
            <form method="post">
                <!-- VIEW INFORMATION (readonly) -->
                <div class="info-section">
                    <h6><i class="fas fa-eye me-2"></i>View Information</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['lname']); ?>" >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['fname']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['mi']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['email']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sex</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['sex']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nationality</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['nationality']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Birth Date</label>
                            <input type="date" class="form-control" name="bdate" value="<?= $resident['bdate']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Birth Place</label>
                            <input class="form-control" value="<?= htmlspecialchars($resident['bplace']); ?>">
                        </div>
                    </div>
                </div>

                <!-- UPDATE INFORMATION (editable) -->
                <div class="info-section">
                    <h6><i class="fas fa-edit me-2"></i>Update Information</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Age</label>
                            <input class="form-control" type="number" name="age" value="<?= htmlspecialchars($resident['age']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Civil Status</label>
                            <input class="form-control" type="text" name="status" value="<?= htmlspecialchars($resident['status']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact No.</label>
                            <input class="form-control" type="tel" name="contact" maxlength="11" pattern="[0-9]{11}" value="<?= htmlspecialchars($resident['contact']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">House No.</label>
                            <input class="form-control" type="text" name="houseno" value="<?= htmlspecialchars($resident['houseno']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Street</label>
                            <input class="form-control" type="text" name="street" value="<?= htmlspecialchars($resident['street']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Barangay</label>
                            <input class="form-control" type="text" name="brgy" value="<?= htmlspecialchars($resident['brgy']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for household search -->
                <input type="hidden" name="lname" value="<?= htmlspecialchars($resident['lname']); ?>">
                <input type="hidden" name="mi" value="<?= htmlspecialchars($resident['mi']); ?>">

                <div class="d-flex justify-content-center gap-3 mt-3">
                    <button type="submit" name="profile_update" class="btn btn-update">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </div>

                
            </form>
        </div>
    </div>
</div>

<!-- Back to Top Button -->
<a href="#" class="top-link" id="backToTopBtn">
    <i class="fas fa-arrow-up"></i>
</a>

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

    // Back to top button
    const backBtn = document.getElementById('backToTopBtn');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) backBtn.classList.add('show');
        else backBtn.classList.remove('show');
    });
    backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
</script>
</body>
</html>
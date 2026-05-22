<?php 
error_reporting(E_ALL ^ E_WARNING);
require('classes/main.class.php');
require('classes/resident.class.php');

$userdetails = $eusebia->get_userdata();
$eusebia->create_twelve(); // handles Grade 12 enrollment submission

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$current_date = $dt->format('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EPANHS | Grade 12 Enrollment</title>

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

        /* ========== NAVBAR (modern gradient) ========== */
        .navbar-custom {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
            backdrop-filter: blur(8px);
            padding: 0.9rem 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
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

        /* ========== HERO SECTION ========== */
        .hero-enroll {
            text-align: center;
            padding: 2rem 1rem 1rem;
        }
        .badge-grade {
            background: #ffffffcc;
            backdrop-filter: blur(4px);
            border-radius: 80px;
            display: inline-block;
            padding: 0.3rem 1.5rem;
            font-weight: 600;
            color: #0b2b5c;
            font-size: 0.9rem;
        }
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 3rem;
            background: linear-gradient(135deg, #0b2b5c, #2a6f9c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 1rem 0 0.5rem;
        }
        .logo-strip {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        .logo-strip img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            transition: transform 0.2s;
        }
        .logo-strip img:hover { transform: scale(1.05); }
        .quote-text {
            font-style: italic;
            font-weight: 400;
            color: #4a627a;
            border-left: 4px solid #2a6f9c;
            background: #ffffffaa;
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 60px;
            backdrop-filter: blur(4px);
        }

        /* ========== PROCEDURE CARDS ========== */
        .step-card {
            background: white;
            border-radius: 32px;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 15px 30px -12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 35px -16px rgba(0,0,0,0.15);
        }
        .step-icon {
            font-size: 3.8rem;
            background: linear-gradient(145deg, #1f4e8c, #0b2b5c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }
        .step-title {
            font-weight: 700;
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
        }
        
        /* ========== ENROLLMENT BUTTON ========== */
        .btn-enroll {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            border: none;
            border-radius: 50px;
            padding: 1rem 2.2rem;
            font-size: 1.3rem;
            font-weight: 600;
            color: white;
            transition: 0.25s;
            box-shadow: 0 10px 20px -8px rgba(11,43,92,0.4);
        }
        .btn-enroll:hover {
            transform: scale(1.02);
            background: linear-gradient(135deg, #1f3a6b, #2a6f9c);
            color: white;
        }

        /* ========== MODAL STYLE (scrolling fixed) ========== */
        .modern-modal .modal-content {
            border-radius: 32px;
            border: none;
            overflow: hidden;
            box-shadow: 0 30px 40px rgba(0,0,0,0.2);
        }
        .modal-header {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            color: white;
            border-bottom: none;
            padding: 1.2rem 1.8rem;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .modal-body {
            padding: 1.8rem;
            background: #fefefe;
            max-height: 65vh;
            overflow-y: auto;
        }
        .form-section {
            background: #f8fafd;
            padding: 1rem;
            border-radius: 24px;
            margin-bottom: 1.5rem;
        }
        .form-section h6 {
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
        }
        .form-control:focus, .form-select:focus {
            border-color: #2a6f9c;
            box-shadow: 0 0 0 0.2rem rgba(42,111,156,0.25);
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
            .hero-title { font-size: 2.2rem; }
            .step-title { font-size: 1.3rem; }
            .logo-strip img { height: 55px; }
            .modal-body { max-height: 55vh; }
        }
    </style>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">
            <i class="bi bi-mortarboard-fill me-2"></i> EPAMHS Portal
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

<!-- ========== HERO SECTION ========== -->
<div class="hero-enroll">
    <div class="container">
        <div class="badge-grade mb-3">
            <i class="fas fa-file-alt me-2"></i> Enrollment Portal
        </div>
        <h1 class="hero-title">Grade 12 Enrollment</h1>
        <div class="current-date mt-3 text-secondary">
            <i class="far fa-calendar-alt me-1"></i> <?= $current_date ?>
        </div>
    </div>
</div>

<!-- ========== PROCEDURE STEPS ========== -->
<div class="container mt-5 pt-3" data-aos="fade-up">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #0b2b5c;">How to Enroll</h2>
        <hr class="w-25 mx-auto" style="height: 3px; background: linear-gradient(90deg,#0b2b5c,#2a6f9c); opacity: 0.5;">
    </div>
    <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="50">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-id-card"></i></div>
                <div class="step-title">Step 1: Prepare</div>
                <p class="text-secondary">Gather all necessary documents and information for Grade 12 admission.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-laptop"></i></div>
                <div class="step-title">Step 2: Choose Course</div>
                <p class="text-secondary">Select your Strand specialization / course and fill the online form accurately.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="step-title">Step 3: Wait</div>
                <p class="text-secondary">Monitor your email or portal for confirmation and class assignment.</p>
            </div>
        </div>
    </div>
</div>

<!-- ========== ENROLLMENT BUTTON + MODAL ========== -->
<div class="container text-center my-5" data-aos="zoom-in">
    <button type="button" class="btn btn-enroll" data-bs-toggle="modal" data-bs-target="#enrollmentModal">
        <i class="fas fa-pen-alt me-2"></i> Open Grade 12 Form
    </button>
</div>

<!-- MODAL: Grade 12 Enrollment Form (with strand selection) -->
<div class="modal fade modern-modal" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" class="was-validated">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="enrollmentModalLabel"><i class="fas fa-edit me-2"></i>Grade 12 Enrollment Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- School Year & Strand Selection -->
                    <div class="form-section">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">School Year:</label>
                                <select name="sy" class="form-select" required>
                                    <option value="2026-2027">2026-2027</option>
                                    <option value="2027-2028">2027-2028</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">SHS Strand (Course):</label>
                                <select name="course" class="form-select border-primary" required>
                                    <option value="">-- Select Strand --</option>
                                    <option value="STEM">STEM (Science, Technology, Engineering, and Mathematics)</option>
                                    <option value="ABM">ABM (Accountancy, Business, and Management)</option>
                                    <option value="GAS">GAS (General Academic Strand)</option>
                                    <option value="TVL-ICT">TVL-ICT (Information and Communication Technology)</option>
                                    <option value="TVL-HE">TVL-HE (Home Economics)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">LRN:</label>
                                <input name="lrn" type="text" class="form-control" placeholder="Learner Reference No." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">School ID:</label>
                                <input name="school_id" type="text" class="form-control" placeholder="School ID" required>
                            </div>
                        </div>
                    </div>

                    <!-- Learner Information -->
                    <div class="form-section">
                        <h6><i class="fas fa-user-graduate me-2"></i>Learner Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4"><input name="lname" type="text" class="form-control" placeholder="Last Name" required></div>
                            <div class="col-md-4"><input name="fname" type="text" class="form-control" placeholder="First Name" required></div>
                            <div class="col-md-4"><input name="mi" type="text" class="form-control" placeholder="Middle Name" required></div>
                            <div class="col-md-4"><input name="bdate" type="date" class="form-control" required></div>
                            <div class="col-md-4">
                                <select name="sex" class="form-select" required>
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4"><input name="age" type="number" class="form-control" placeholder="Age" required></div>
                            <div class="col-md-4"><input name="contact" type="number" class="form-control" placeholder="Contact No." required></div>
                            <div class="col-md-8"><input name="email" type="email" class="form-control" placeholder="Email Address" required></div>
                            <div class="col-md-6"><textarea name="current_address" class="form-control" rows="2" placeholder="Current Address" required></textarea></div>
                            <div class="col-md-6"><textarea name="perm_address" class="form-control" rows="2" placeholder="Permanent Address" required></textarea></div>
                        </div>
                    </div>

                    <!-- Parent / Guardian Information -->
                    <div class="form-section">
                        <h6><i class="fas fa-family me-2"></i>Parent / Guardian</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-semibold">Father's Name</label>
                                <input name="ffname" class="form-control mb-2" placeholder="First Name" required>
                                <input name="flname" class="form-control mb-2" placeholder="Last Name" required>
                                <input name="contact_f" class="form-control" placeholder="Contact No." required>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold">Mother's Maiden Name</label>
                                <input name="mfname" class="form-control mb-2" placeholder="First Name" required>
                                <input name="mlname" class="form-control mb-2" placeholder="Last Name" required>
                                <input name="contact_m" class="form-control" placeholder="Contact No." required>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Education -->
                    <div class="form-section">
                        <h6><i class="fas fa-school me-2"></i>Previous Education</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Last Grade Level Completed</label>
                                <input name="lglc" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Last School Attended</label>
                                <input name="lsa" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last School Year Completed</label>
                                <input name="lysc" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School ID </label>
                                <input name="school_id_prev" type="text" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id_resident" value="<?= $userdetails['id_resident'] ?? ''; ?>">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="create_twelve" class="btn btn-enroll rounded-pill px-5">Submit Enrollment</button>
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
                <p class="mb-0"> <?= date('Y') ?> EPAMHS Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 20 });

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
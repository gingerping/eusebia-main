<?php 
error_reporting(E_ALL ^ E_WARNING);
include('classes/resident.class.php');

// Get user data
$userdetails = $eusebia->get_userdata();
$current_user_id = $userdetails['id_resident'];

// Date and time for display
$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$current_date = $dt->format('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>EUSEBIA PAZ ARROYO NHS | Student Portal</title>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Library for scroll animations (optional) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
    font-family: 'Inter', sans-serif;
    /* Updated background for better performance and fixed position */
    background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
    background-attachment: fixed; /* Keeps the gradient from stretching if the page is long */
    
    color: #1a2c3e;
    scroll-behavior: smooth;

    /* Text rendering improvements */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
    
    /* Prevents horizontal scroll on small screens */
    overflow-x: hidden; 
    margin: 0;
}
/* Bold headings with tight letter-spacing */
h1, h2, h3, .fw-bold {
    font-weight: 700;
    letter-spacing: -0.022em;
}

/* Medium weight for navigation/sub-labels */
.nav-link, .label-medium {
    font-weight: 500;
    letter-spacing: -0.011em;
}

/* Slightly more line-height for body text to improve readability */
p, .card-text {
    line-height: 1.6;
    font-weight: 400;
}

        /* Custom Navbar */
        .navbar-custom {
            background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
            backdrop-filter: blur(8px);
            padding: 0.9rem 2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: -0.3px;
            color: white !important;
            transition: transform 0.2s;
        }
        .navbar-brand:hover {
            transform: scale(1.02);
        }
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
        .dropdown-item-custom i {
            width: 28px;
            margin-right: 6px;
        }
        .dropdown-item-custom:hover {
            background: #eef2ff;
            transform: translateX(5px);
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            padding: 3rem 1rem 2rem;
            background: radial-gradient(circle at 10% 30%, rgba(11,43,92,0.04), transparent);
        }
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2.8rem;
            background: linear-gradient(135deg, #0b2b5c, #2a6f9c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.75rem;
        }
        .welcome-badge {
            background: #ffffffcc;
            backdrop-filter: blur(4px);
            border-radius: 80px;
            display: inline-block;
            padding: 0.3rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            color: #0b2b5c;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .current-date {
            font-size: 0.9rem;
            color: #4a627a;
            margin-top: 0.75rem;
        }

        /* Grade Cards Grid */
        .grade-section {
            padding: 2rem 1rem 4rem;
        }
        .grade-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(2px);
            border-radius: 36px;
            border: none;
            box-shadow: 0 18px 35px -12px rgba(0,0,0,0.12);
            transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            cursor: pointer;
            overflow: hidden;
            text-align: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }
        .grade-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #ffffff, #f5f9ff);
            z-index: -1;
            transition: opacity 0.3s;
        }
        .grade-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 28px 40px -18px rgba(0,0,0,0.25);
        }
        .grade-icon {
    font-size: 1.8rem;
    margin-bottom: 1.2rem;
    display: inline-block;
    
    /* Improved Gradient Depth */
    background: linear-gradient(145deg, #2a65ad, #0b2b5c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent; /* Better Safari support */
    background-clip: text;
    
    /* Adds depth since the icon is transparent */
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
    
    /* Smoother transition */
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.3s ease;
}
.grade-icon:hover {
    transform: translateY(-5px) scale(1.05);
    filter: drop-shadow(0 8px 12px rgba(31, 78, 140, 0.2));
}
        .grade-card:hover .grade-icon {
            transform: scale(1.08);
        }
        .grade-title {
            font-weight: 800;
            font-size: 1.9rem;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #1f3a5f, #1e5a88);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.3rem;
        }
        .grade-sub {
            font-size: 0.85rem;
            color: #5e7e9e;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .card-link {
            text-decoration: none;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1rem;
            }
            .hero-title {
                font-size: 2rem;
            }
            .grade-title {
                font-size: 1.5rem;
            }
            .grade-section {
                padding: 1rem 1rem 2rem;
            }
        }

        /* Back to top button */
        .top-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #0b2b5c;
            width: 50px;
            height: 50px;
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
            transition: 0.2s ease;
        }
        .top-link.show {
            opacity: 1;
            visibility: visible;
        }
        .top-link:hover {
            background: #1f5a9e;
            transform: translateY(-5px);
        }

        /* Footer */
        .footer-custom {
            background: #0b1f33;
            color: #cddcec;
            padding: 2rem 1rem;
            text-align: center;
            font-size: 0.9rem;
            border-top-left-radius: 32px;
            border-top-right-radius: 32px;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<!-- Modern Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="bi bi-mortarboard-fill me-2"></i> EPAMHS Portal
        </a>
        <div class="dropdown ms-auto">
            <button class="btn dropdown-toggle-custom dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i> <?= htmlspecialchars($userdetails['surname'] . ', ' . $userdetails['firstname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item dropdown-item-custom" href="resident_profile.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-id-card"></i> My Profile</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="resident_changepass.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-key"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item dropdown-item-custom" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero / Welcome Section -->
<div class="hero-section">
    <div class="container">
        <div class="welcome-badge mb-3">
            <i class="fas fa-graduation-cap me-2"></i> Student Dashboard
        </div>
        <h1 class="hero-title">Choose your journey,<br> <span style="background: linear-gradient(135deg,#1e5a88,#0f3b7a); -webkit-background-clip:text; background-clip:text; color:transparent;"><?= htmlspecialchars($userdetails['surname'] . ', ' . $userdetails['firstname']); ?></span></h1>
        <p class="mt-3 text-secondary" style="max-width: 550px; margin: 0 auto;">select your grade level.</p>
        <div class="current-date">
            <i class="far fa-calendar-alt me-1"></i> <?= $current_date ?>
        </div>
    </div>
</div>

<!-- Grade Level Cards Grid -->
<div class="grade-section">
    <div class="container">
        <div class="row g-5 justify-content-center">
            <!-- Grade 7 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="50">
                <a href="grade7.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 7</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Junior High • First Year</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Enter <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <!-- Grade 8 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <a href="grade8.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 8</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Junior High • Second Year</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Explore <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <!-- Grade 9 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
                <a href="grade9.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 9</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Junior High • Third Year</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Continue <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <!-- Grade 10 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <a href="grade10.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 10</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Junior High • Fourth Year</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Advance <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <!-- Grade 11 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                <a href="grade11.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 11</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Senior High • Grade 11</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Level Up <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <!-- Grade 12 -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <a href="grade12.php?id_resident=<?= $current_user_id ?>" class="card-link">
                    <div class="grade-card">
                        <div class="grade-icon">GRADE 12</div>
                        <div class="grade-title"></div>
                        <div class="grade-sub">Senior High • Grade 12</div>
                        <hr class="my-3 w-25 mx-auto" style="opacity:0.3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">Graduation Track <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Back to Top Button -->
<a href="#" class="top-link" id="backToTopBtn" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</a>

<!-- Footer -->
<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo  Memorial National High School<br>
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
    // Initialize AOS scroll animations
    AOS.init({
        duration: 800,
        once: true,
        offset: 20,
    });

    // Back to top button visibility + smooth scroll
    const backBtn = document.getElementById('backToTopBtn');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backBtn.classList.add('show');
        } else {
            backBtn.classList.remove('show');
        }
    });
    backBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tooltip initialization (if any)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>

</body>
</html>
<?php 

error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 1);
require('classes/main.class.php');
require('classes/resident.class.php');

$userdetails = $eusebia->get_userdata();
$eusebia->create_eight();

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$current_date = $dt->format('l, F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link class="manifest" href="manifest.json">
    <meta name="theme-color" content="#0b2b5c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EPAMNHS">
    <link rel="apple-touch-icon" href="icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="icons/pwa/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="144x144" href="icons/pwa/icon-144x144.png">
    <link rel="icon" type="image/png" sizes="192x192" href="icons/pwa/icon-192x192.png">

    <title>EPANHS | Grade 8 Enrollment</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function() {
        var params = new URLSearchParams(window.location.search);
        if (params.get('lrn_error')) {
            var lrn = params.get('lrn_error');
            Swal.fire({
                icon: 'error',
                title: 'LRN Already Registered',
                text: 'LRN "' + lrn + '" is already used by another enrollment. Please use a different LRN.',
                confirmButtonColor: '#d33'
            });
            history.replaceState(null, '', window.location.pathname);
        }
    })();
    </script>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
            color: #1a2c3e;
            scroll-behavior: smooth;
        }

        /* NAVBAR */
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
        .dropdown-item-custom:hover { background: #eef2ff; transform: translateX(5px); }

        /* HERO */
        .hero-enroll { text-align: center; padding: 2rem 1rem 1rem; }
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

        /* STEP CARDS */
        .step-card {
            background: white;
            border-radius: 32px;
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 15px 30px -12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .step-card:hover { transform: translateY(-8px); box-shadow: 0 25px 35px -16px rgba(0,0,0,0.15); }
        .step-icon {
            font-size: 3.8rem;
            background: linear-gradient(145deg, #1f4e8c, #0b2b5c);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1rem;
        }
        .step-title { font-weight: 700; font-size: 1.6rem; margin-bottom: 0.5rem; }

        /* ENROLL BUTTON */
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
        .btn-enroll:hover { transform: scale(1.02); background: linear-gradient(135deg, #1f3a6b, #2a6f9c); color: white; }

        /* MODAL SUBMIT BUTTON REMODEL (Prevents clipping on small screens) */
        .btn-modal-submit {
            background: linear-gradient(135deg, #0b2b5c, #1f5a9e);
            border: none;
            color: white;
            font-weight: 600;
            transition: 0.25s;
            box-shadow: 0 4px 10px rgba(11,43,92,0.2);
        }
        .btn-modal-submit:hover { background: linear-gradient(135deg, #1f3a6b, #2a6f9c); color: white; }

        /* MODAL */
        .modern-modal .modal-content {
            border-radius: 24px;
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
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .modal-body { padding: 1.5rem; background: #fefefe; }
        .modal-footer { background: #f8fafd; border-top: 1px solid #e9ecef; padding: 1rem 1.5rem; }

        .form-section {
            background: #f8fafd;
            padding: 1rem;
            border-radius: 20px;
            margin-bottom: 1.2rem;
        }
        .form-section h6 {
            font-weight: 700;
            color: #0b2b5c;
            border-left: 4px solid #2a6f9c;
            padding-left: 12px;
            margin-bottom: 1rem;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #dee2e6;
            padding: 0.6rem 1rem;
            font-size: 16px; /* prevents iOS zoom */
        }
        .form-control:focus, .form-select:focus {
            border-color: #2a6f9c;
            box-shadow: 0 0 0 0.2rem rgba(42,111,156,0.25);
        }

        /* No red borders until submit */
        .form-control, .form-select { border-color: #dee2e6 !important; }
        .was-validated .form-control:invalid,
        .was-validated .form-select:invalid { border-color: #dc3545 !important; box-shadow: none !important; }
        .was-validated .form-control:valid,
        .was-validated .form-select:valid { border-color: #28a745 !important; box-shadow: none !important; }

        /* UPLOAD */
        .upload-area {
            border: 2px dashed #2a6f9c;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            background: #f0f6fb;
            cursor: pointer;
            transition: background 0.2s;
        }
        .upload-area:hover, .upload-area.dragover { background: #dceef9; }
        .file-preview-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 8px 14px;
            margin-bottom: 6px;
            font-size: 0.88rem;
        }
        .file-preview-item .file-icon { font-size: 1.2rem; color: #2a6f9c; }
        .file-preview-item .remove-file { margin-left: auto; cursor: pointer; color: #dc3545; }

        /* BACK TO TOP */
        .top-link {
            position: fixed;
            bottom: 2rem; right: 2rem;
            background: #0b2b5c;
            width: 48px; height: 48px;
            border-radius: 30px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
            z-index: 99;
            text-decoration: none;
            opacity: 0; visibility: hidden;
        }
        .top-link.show { opacity: 1; visibility: visible; }
        .top-link:hover { background: #1f5a9e; transform: translateY(-5px); color: white; }

        /* FOOTER */
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

        /* MOBILE */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.2rem; }
            .step-title { font-size: 1.3rem; }
            .modern-modal .modal-dialog {
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                height: 100% !important;
                max-height: 100% !important;
            }
            .modern-modal .modal-content {
                border-radius: 0 !important;
                height: 100vh;
            }
            .modal-body { padding: 1rem !important; }
            .form-section { padding: 0.85rem !important; border-radius: 14px !important; }
            
            /* Responsive modal footer adjustments */
            .modal-footer {
                padding: 0.75rem 1rem !important;
                display: flex;
                justify-content: space-between;
            }
            .modal-footer .btn {
                padding: 0.5rem 1rem !important;
                font-size: 0.9rem !important;
                flex: 1;
                margin: 0 4px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">
            <i class="bi bi-mortarboard-fill me-2"></i> EPAMHS Portal
        </a>
        <div class="dropdown ms-auto">
            <button class="btn dropdown-toggle-custom dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-2"></i> <?= htmlspecialchars($userdetails['surname'] . ', ' . $userdetails['firstname']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-custom dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item dropdown-item-custom" href="resident_homepage.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="my_submissions.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-file-alt"></i> My Submissions</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="resident_profile.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-id-card"></i> My Profile</a></li>
                <li><a class="dropdown-item dropdown-item-custom" href="resident_changepass.php?id_resident=<?= $current_user_id ?>"><i class="fas fa-key"></i> Change Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item dropdown-item-custom" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-enroll">
    <div class="container">
        <div class="badge-grade mb-3">
            <i class="fas fa-file-alt me-2"></i> Enrollment Portal
        </div>
        <h1 class="hero-title">Grade 8 Enrollment</h1>
        <div class="current-date mt-3 text-secondary">
            <i class="far fa-calendar-alt me-1"></i> <?= $current_date ?>
        </div>
    </div>
</div>

<div class="container mt-5 pt-3" data-aos="fade-up">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color:#0b2b5c;">How to Enroll</h2>
        <hr class="w-25 mx-auto" style="height:3px;background:linear-gradient(90deg,#0b2b5c,#2a6f9c);opacity:0.5;">
    </div>
    <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="50">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-id-card"></i></div>
                <div class="step-title">Step 1: Prepare</div>
                <p class="text-secondary">Gather all necessary documents and information for Grade 8 admission.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-laptop"></i></div>
                <div class="step-title">Step 2: Fill-Up</div>
                <p class="text-secondary">Complete the online enrollment form accurately and submit.</p>
            </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
            <div class="step-card">
                <div class="step-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="step-title">Step 3: Wait</div>
                <p class="text-secondary">Monitor your email or portal for the confirmation & class assignment.</p>
            </div>
        </div>
    </div>
</div>

<div class="container text-center my-5" data-aos="zoom-in">
    <button type="button" class="btn btn-enroll" data-bs-toggle="modal" data-bs-target="#enrollmentModal">
        <i class="fas fa-pen-alt me-2"></i> Open Grade 8 Form
    </button>
</div>

<div class="modal fade modern-modal" id="enrollmentModal" tabindex="-1" aria-labelledby="enrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="enrollForm" method="post" enctype="multipart/form-data" class="was-validated">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="enrollmentModalLabel">
                        <i class="fas fa-edit me-2"></i>Grade 8 Enrollment Form
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="overflow-y: auto; max-height: 70vh;">

                    <div class="form-section" id="studentTypeSection">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold"><i class="fas fa-user-tag me-1"></i> Student Type:</label>
                                <select id="studentTypeSelect" class="form-select" onchange="handleStudentType(this.value)" required>
                                    <option value="">-- Select Student Type --</option>
                                    <option value="new">New Student</option>
                                    <option value="old">Old Student</option>
                                    <option value="transferee">Transferee</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="lrnLookupSection" style="display:none;" class="form-section">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Enter your LRN to auto-fill your information:</label>
                                <input type="text" id="lrnLookupInput" class="form-control" placeholder="Enter LRN and wait..." maxlength="12">
                            </div>
                            <div class="col-md-4">
                                <div id="lrnLookupStatus" style="font-size:.85rem;"></div>
                            </div>
                        </div>
                    </div>

                    <div id="mainFormFields" style="display:none;">

                        <div class="form-section">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">School Year:</label>
                                    <select name="sy" class="form-select" required>
                                        <option value="2026-2027">2026-2027</option>
                                        <option value="2027-2028">2027-2028</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">LRN:</label>
                                    <input name="lrn" type="text" class="form-control" placeholder="Learner Reference No." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">School ID:</label>
                                    <input name="school_id" type="text" class="form-control" placeholder="School ID" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6><i class="fas fa-user-graduate me-2"></i>Learner Information</h6>
                            <div class="row g-3">
                                <div class="col-md-4"><input name="lname" type="text" class="form-control" placeholder="Last Name" required></div>
                                <div class="col-md-4"><input name="fname" type="text" class="form-control" placeholder="First Name" required></div>
                                <div class="col-md-4"><input name="mi" type="text" class="form-control" placeholder="Middle Name" required></div>
                                <div class="col-md-4">
    <label class="form-label fw-semibold small mb-1">
        <i class="fas fa-calendar-alt me-1"></i>Date of Birth
    </label>
    <input name="bdate" type="date" class="form-control" required>
</div>
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

                        <div class="form-section">
                            <h6><i class="fas fa-users me-2"></i>Parent / Guardian</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-semibold">Father's Name</label>
                                    <input name="ffname" class="form-control mb-2" placeholder="First Name" required>
                                    <input name="flname" class="form-control mb-2" placeholder="Last Name" required>
                                    <input name="fmi" class="form-control mb-2" placeholder="Middle Initial" required>
                                    <input name="contact_f" class="form-control" placeholder="Contact No." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Mother's Maiden Name</label>
                                    <input name="mfname" class="form-control mb-2" placeholder="First Name" required>
                                    <input name="mlname" class="form-control mb-2" placeholder="Last Name" required>
                                    <input name="mmi" class="form-control mb-2" placeholder="Middle Initial" required>
                                    <input name="contact_m" class="form-control" placeholder="Contact No." required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6><i class="fas fa-school me-2"></i>Previous Education</h6>
                            <div class="row g-3">
                                <div class="col-md-8"><input name="lglc" class="form-control" placeholder="Last Grade Level Completed" required></div>
                                <div class="col-md-4"><input name="lsa" class="form-control" placeholder="Last School Attended" required></div>
                                <div class="col-md-8"><input name="lysc" class="form-control" placeholder="Last School Year Completed" required></div>
                                <div class="col-md-4"><input name="school_id" type="text" class="form-control" placeholder="School ID (Previous)" required></div>
                            </div>
                        </div>
                        <div class="form-section">
                            <h6><i class="fas fa-hand-holding-heart me-2"></i>Socioeconomic Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Indigenous People (IP) Member</label>
                                    <select name="is_ip" class="form-select">
                                        <option value="No" selected>No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="ip_group_div" style="display:none;">
                                    <label class="form-label fw-semibold small">IP Group / Tribe</label>
                                    <input name="ip_group" class="form-control" placeholder="e.g. Agta, Dumagat, Igorot">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">4Ps / Pantawid Pamilyang Pilipino Program</label>
                                    <select name="is_4ps" class="form-select">
                                        <option value="No" selected>No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="fourps_id_div" style="display:none;">
                                    <label class="form-label fw-semibold small">4Ps Household ID Number</label>
                                    <input name="fourps_id" class="form-control" placeholder="4Ps Household ID">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6><i class="fas fa-file-upload me-2"></i>Upload Supporting Documents</h6>
                            <p class="text-muted small mb-2">Attach required documents (e.g. PSA Birth Certificate, Report Card, Good Moral Certificate). You may select multiple files.</p>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#2a6f9c;"></i>
                                <p class="mb-1 fw-semibold">Drag &amp; drop files here, or <span class="text-primary" style="cursor:pointer;" onclick="document.getElementById('docFiles').click()">browse</span></p>
                                <p class="text-muted small mb-0">Accepted: PDF, JPG, PNG, DOC, DOCX &mdash; Max 5MB per file</p>
                                <input type="file" id="docFiles" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="d-none">
                            </div>
                            <div id="filePreviewList" class="mt-2"></div>
                        </div>

                        <input type="hidden" name="student_type" id="studentTypeHidden" value="new">
                        <input type="hidden" name="id_resident" value="<?= $userdetails['id_resident'] ?? ''; ?>">
                        <!-- Populated by LRN lookup — tells backend which Grade 7 record to delete on approval -->
                        <input type="hidden" name="prev_grade_table" value="">
                        <input type="hidden" name="prev_grade_id"    value="">

                    </div></div><div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="create_eight" class="btn btn-modal-submit rounded-pill px-4">
                        <i class="fas fa-paper-plane me-2"></i>Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<a href="#" class="top-link" id="backToTopBtn"><i class="fas fa-arrow-up"></i></a>

<footer class="footer-custom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0"><?= date('Y') ?> EPAMHS Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 20 });

    // Sync student type to hidden input
    document.getElementById('studentTypeSelect').addEventListener('change', function() {
        document.getElementById('studentTypeHidden').value = this.value || 'new';
    });

    // Back to top
    const backBtn = document.getElementById('backToTopBtn');
    window.addEventListener('scroll', () => {
        backBtn.classList.toggle('show', window.scrollY > 300);
    });
    backBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tooltip init
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

    // Validate only on submit
    document.getElementById('enrollForm').addEventListener('submit', function(e) {
        this.classList.add('was-validated');
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            var firstInvalid = this.querySelector(':invalid');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Reset on modal close
    document.getElementById('enrollmentModal').addEventListener('hidden.bs.modal', function() {
        var form = document.getElementById('enrollForm');
        form.classList.remove('was-validated');
        form.reset();
        document.getElementById('mainFormFields').style.display = 'none';
        document.getElementById('lrnLookupSection').style.display = 'none';
        document.getElementById('filePreviewList').innerHTML = '';
        selectedFiles = [];
    });

    // ===== DOCUMENT UPLOAD =====
    const uploadArea  = document.getElementById('uploadArea');
    const docFiles    = document.getElementById('docFiles');
    const previewList = document.getElementById('filePreviewList');
    let selectedFiles = [];

    function iconForType(name) {
        const ext = name.split('.').pop().toLowerCase();
        if (['jpg','jpeg','png'].includes(ext)) return 'fas fa-image';
        if (ext === 'pdf') return 'fas fa-file-pdf';
        if (['doc','docx'].includes(ext)) return 'fas fa-file-word';
        return 'fas fa-file-alt';
    }

    function renderPreviews() {
        previewList.innerHTML = '';
        selectedFiles.forEach((f, i) => {
            const sizeKB = (f.size / 1024).toFixed(1);
            const div = document.createElement('div');
            div.className = 'file-preview-item';
            div.innerHTML = `<i class="file-icon ${iconForType(f.name)}"></i>
                <span class="text-truncate" style="max-width:60%;">${f.name}</span>
                <span class="text-muted ms-1">(${sizeKB} KB)</span>
                <i class="remove-file fas fa-times-circle" data-idx="${i}" title="Remove"></i>`;
            previewList.appendChild(div);
        });
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        docFiles.files = dt.files;
        previewList.querySelectorAll('.remove-file').forEach(btn => {
            btn.addEventListener('click', () => {
                selectedFiles.splice(parseInt(btn.dataset.idx), 1);
                renderPreviews();
            });
        });
    }

    function addFiles(fileList) {
        const MAX = 5 * 1024 * 1024;
        Array.from(fileList).forEach(f => {
            if (f.size > MAX) { alert(`"${f.name}" exceeds 5MB limit and was skipped.`); return; }
            if (!selectedFiles.find(sf => sf.name === f.name && sf.size === f.size)) selectedFiles.push(f);
        });
        renderPreviews();
    }

    uploadArea.addEventListener('click', (e) => { if (!e.target.classList.contains('text-primary')) docFiles.click(); });
    docFiles.addEventListener('change', () => addFiles(docFiles.files));
    uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('dragover'); });
    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
    uploadArea.addEventListener('drop', (e) => { e.preventDefault(); uploadArea.classList.remove('dragover'); addFiles(e.dataTransfer.files); });
</script>
<script src="js/pwa.js"></script>

<script>
(function() {
    var lrnInput = document.querySelector('input[name="lrn"]');
    if (!lrnInput) return;
    var feedback = document.createElement('div');
    feedback.style.cssText = 'font-size:.85rem;margin-top:4px;display:none;';
    lrnInput.parentNode.insertBefore(feedback, lrnInput.nextSibling);
    var timer = null;
    lrnInput.addEventListener('input', function() {
        clearTimeout(timer);
        var val = this.value.trim();
        feedback.style.display = 'none';
        lrnInput.setCustomValidity('');
        if (val.length < 3) return;
        timer = setTimeout(function() {
            // Pass student_type and target_grade so old students are not blocked
            var studentType = document.getElementById('studentTypeHidden').value || 'new';
            fetch('check_lrn.php?lrn=' + encodeURIComponent(val)
                + '&student_type=' + encodeURIComponent(studentType)
                + '&target_grade=eight')
                .then(r => r.json())
                .then(data => {
                    if (data.taken) {
                        feedback.innerHTML = '<i class="fas fa-times-circle" style="color:#dc3545;"></i> <span style="color:#dc3545;">This LRN is already registered in Grade 8. Please use a different LRN.</span>';
                        lrnInput.style.borderColor = '#dc3545';
                        lrnInput.setCustomValidity('LRN already registered');
                    } else {
                        feedback.innerHTML = '<i class="fas fa-check-circle" style="color:#28a745;"></i> <span style="color:#28a745;">LRN is available.</span>';
                        lrnInput.style.borderColor = '#28a745';
                        lrnInput.setCustomValidity('');
                    }
                    feedback.style.display = 'block';
                })
                .catch(() => {});
        }, 500);
    });
})();
</script>

<script>
function handleStudentType(val) {
    var lookup   = document.getElementById('lrnLookupSection');
    var fields   = document.getElementById('mainFormFields');
    var lrnField = document.querySelector('input[name="lrn"]');
    if (val === '') {
        lookup.style.display = 'none';
        fields.style.display = 'none';
    } else if (val === 'new') {
        lookup.style.display = 'none';
        fields.style.display = 'block';
        clearForm();
        if (lrnField) { lrnField.readOnly = false; lrnField.value = ''; }
    } else {
        // old / transferee — show lookup first, hide main fields until LRN is found
        lookup.style.display = 'block';
        fields.style.display = 'none';
        clearForm();
        if (lrnField) lrnField.readOnly = true;
    }
}

function clearForm() {
    ['lrn','school_id','lname','fname','mi','bdate','sex','age','contact','email',
     'current_address','perm_address','ffname','flname','fmi','contact_f',
     'mlname','mfname','mmi','contact_m','lglc','lsa','lysc'].forEach(function(n) {
        var el = document.querySelector('[name="' + n + '"]');
        if (el) el.value = '';
    });
    // Clear prev_grade hidden fields
    var ptbl = document.querySelector('[name="prev_grade_table"]');
    var pid  = document.querySelector('[name="prev_grade_id"]');
    if (ptbl) ptbl.value = '';
    if (pid)  pid.value  = '';
    document.getElementById('lrnLookupStatus').innerHTML = '';
    document.getElementById('lrnLookupInput').value = '';
}

(function() {
    var timer = null;
    var input  = document.getElementById('lrnLookupInput');
    if (!input) return;
    input.addEventListener('input', function() {
        clearTimeout(timer);
        var val    = this.value.trim();
        var status = document.getElementById('lrnLookupStatus');
        status.innerHTML = '';
        // Clear previous source info when user types new LRN
        var ptbl = document.querySelector('[name="prev_grade_table"]');
        var pid  = document.querySelector('[name="prev_grade_id"]');
        if (ptbl) ptbl.value = '';
        if (pid)  pid.value  = '';
        document.getElementById('mainFormFields').style.display = 'none';
        if (val.length < 3) return;
        status.innerHTML = '<span style="color:#6c757d;"><i class="fas fa-spinner fa-spin me-1"></i>Searching...</span>';
        timer = setTimeout(function() {
            fetch('lookup_lrn.php?lrn=' + encodeURIComponent(val))
                .then(r => r.json())
                .then(res => {
                    if (res.found) {
                        var d = res.data;
                        var set = (name, val) => { var el = document.querySelector('[name="'+name+'"]'); if (el) el.value = val || ''; };
                        set('lrn', d.lrn); set('school_id', d.school_id);
                        set('lname', d.lname); set('fname', d.fname); set('mi', d.mi);
                        set('bdate', d.bdate); set('sex', d.sex); set('age', d.age);
                        set('contact', d.contact); set('email', d.email);
                        set('current_address', d.current_address); set('perm_address', d.perm_address);
                        set('ffname', d.ffname); set('flname', d.flname); set('fmi', d.fmi); set('contact_f', d.contact_f);
                        set('mfname', d.mfname); set('mlname', d.mlname); set('mmi', d.mmi); set('contact_m', d.contact_m);
                        set('lglc', d.lglc); set('lsa', d.lsa); set('lysc', d.lysc);

                        // *** Store the source record so backend can delete it on approval ***
                        if (ptbl) ptbl.value = res.source_table || '';
                        if (pid)  pid.value  = res.source_id    || '';

                        // Show the full form now that data is loaded
                        document.getElementById('mainFormFields').style.display = 'block';
                        status.innerHTML = '<span style="color:#28a745;"><i class="fas fa-check-circle me-1"></i>Information loaded! Please review and submit.</span>';
                    } else {
                        status.innerHTML = '<span style="color:#dc3545;"><i class="fas fa-times-circle me-1"></i>LRN not found. Please fill the form manually.</span>';
                        // Show form anyway so they can fill manually
                        document.getElementById('mainFormFields').style.display = 'block';
                        var lrnField = document.querySelector('input[name="lrn"]');
                        if (lrnField) { lrnField.readOnly = false; lrnField.value = val; }
                    }
                })
                .catch(() => { status.innerHTML = '<span style="color:#dc3545;">Lookup failed. Please fill manually.</span>'; });
        }, 600);
    });
})();
</script>
    <script>
// Show/hide IP group and 4Ps ID fields conditionally
document.querySelector('[name="is_ip"]').addEventListener('change', function() {
    document.getElementById('ip_group_div').style.display = this.value === 'Yes' ? '' : 'none';
});
document.querySelector('[name="is_4ps"]').addEventListener('change', function() {
    document.getElementById('fourps_id_div').style.display = this.value === 'Yes' ? '' : 'none';
});
</script>

</body>
</html>
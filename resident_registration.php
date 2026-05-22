<?php 
     require('classes/resident.class.php');
    $residenteusebia->create_resident();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Registration Form – EPAMNHS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

    <style>
        /* ── TOKENS ── */
        :root {
            --navy-dark:    #0a2e5c;
            --navy-mid:     #103f80;
            --navy-light:   #1a5ca8;
            --gold:         #ffd700;
            --gold-soft:    rgba(255,215,0,.75);
            --white:        #ffffff;
            --bg:           #f0f4fb;
            --card-bg:      #ffffff;
            --border:       rgba(10,46,92,.1);
            --text-primary: #1a1e2e;
            --text-muted:   #5a6478;
            --label:        #2d3a52;
            --input-bg:     #f7f9fd;
            --input-focus:  #e8eff9;
            --radius:       10px;
            --shadow:       0 4px 24px rgba(10,46,92,.08);
        }

        /* ── BASE ── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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

        /* ── PAGE WRAPPER ── */
        main {
            flex: 1;
            padding: 2.5rem 1rem 3rem;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 5vw, 32px);
            color: var(--navy-dark);
            margin-bottom: .35rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 300;
        }

        .divider {
            width: 48px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin: .6rem auto 0;
        }

        /* ── CARD ── */
        .form-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
        }

        .form-card-header {
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy-mid) 100%);
            padding: 1.1rem 1.6rem;
            display: flex;
            align-items: center;
            gap: .7rem;
        }

        .form-card-header i { color: var(--gold); font-size: 18px; }

        .form-card-header h2 {
            color: var(--white);
            font-size: 15px;
            font-weight: 500;
            margin: 0;
            letter-spacing: .02em;
        }

        .form-card-body { padding: 1.8rem 1.6rem; }

        /* ── SECTION LABELS ── */
        .section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--navy-light);
            border-left: 3px solid var(--gold);
            padding-left: .55rem;
            margin: 1.6rem 0 1rem;
        }

        .section-label:first-child { margin-top: 0; }

        /* ── FORM FIELDS ── */
        .form-group { margin-bottom: .9rem; }

        label {
            display: block;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--label);
            margin-bottom: .35rem;
        }

        label .req { color: #c0392b; margin-left: 2px; }

        .form-control, .form-select {
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 14px;
            color: var(--text-primary);
            padding: .58rem .85rem;
            transition: border-color .2s, background .2s, box-shadow .2s;
            width: 100%;
        }

        .form-control::placeholder { color: #aab2c4; }

        .form-control:focus, .form-select:focus {
            background: var(--input-focus);
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px rgba(26,92,168,.12);
            outline: none;
        }

        /* Bootstrap validation overrides */
        .was-validated .form-control:valid,
        .was-validated .form-select:valid {
            border-color: #3b6d11;
            background-image: none;
        }

        .was-validated .form-control:invalid,
        .was-validated .form-select:invalid {
            border-color: #c0392b;
            background-image: none;
        }

        .valid-feedback   { font-size: 11.5px; color: #3b6d11; }
        .invalid-feedback { font-size: 11.5px; color: #c0392b; }

        /* ── PASSWORD TOGGLE ── */
        .password-wrap {
            position: relative;
        }

        .password-wrap .form-control {
            padding-right: 2.8rem;
        }

        .toggle-password {
            position: absolute;
            right: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 15px;
            z-index: 2;
            line-height: 1;
        }

        .toggle-password:hover { color: var(--navy-light); }

        /* ── TERMS ── */
        .terms-row {
            background: #f0f4fb;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: .85rem 1rem;
            margin-top: 1.4rem;
        }

        .form-check-input:checked {
            background-color: var(--navy-mid);
            border-color: var(--navy-mid);
        }

        .terms-link {
            color: var(--navy-light);
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px dashed var(--navy-light);
        }

        .terms-link:hover { color: var(--navy-dark); }

        /* ── ACTIONS ── */
        .form-actions {
            display: flex;
            gap: .75rem;
            justify-content: flex-end;
            margin-top: 1.6rem;
            flex-wrap: wrap;
        }

        .btn-back {
            background: transparent;
            border: 1.5px solid #c0392b;
            color: #c0392b;
            border-radius: var(--radius);
            padding: .58rem 1.4rem;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            transition: background .2s, color .2s;
        }

        .btn-back:hover {
            background: #c0392b;
            color: #fff;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--navy-mid), var(--navy-light));
            border: none;
            color: var(--white);
            border-radius: var(--radius);
            padding: .58rem 1.8rem;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 3px 12px rgba(16,63,128,.25);
        }

        .btn-submit:hover { opacity: .9; transform: translateY(-1px); }

        /* ── MODAL ── */
        .modal-header.navy {
            background: linear-gradient(135deg, var(--navy-dark), var(--navy-mid));
        }

        .modal-header.navy .modal-title { color: var(--white); }

        .modal-header.navy .btn-close {
            filter: invert(1);
        }

        .modal-body h6 {
            color: var(--navy-dark);
            font-weight: 600;
            margin-top: 1rem;
        }

        .modal-body p {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.7;
        }

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
        /* ── RESPONSIVE ── */
        @media (max-width: 576px) {
            .form-card-body { padding: 1.3rem 1rem; }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn-back,
            .btn-submit {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
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

<!-- MAIN -->
<main>
    <!-- Page heading -->
    <div class="page-header">
        <h1>Registration Form</h1>
        <p>Please fill in all required fields accurately.</p>
        <div class="divider"></div>
    </div>

    <!-- Card -->
    <div class="form-card">
        <div class="form-card-header">
            <i class="fas fa-user-plus"></i>
            <h2>New Student Registration</h2>
        </div>

        <div class="form-card-body">
            <form method="post" enctype="multipart/form-data" class="was-validated" novalidate>

                <!-- ── Personal Information ── -->
                <div class="section-label">Personal Information</div>

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Last Name <span class="req">*</span></label>
                            <input type="text" class="form-control" name="lname" placeholder="e.g. Dela Cruz" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Last name is required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>First Name <span class="req">*</span></label>
                            <input type="text" class="form-control" name="fname" placeholder="e.g. Juan" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">First name is required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Middle Name <span class="req">*</span></label>
                            <input type="text" class="form-control" name="mi" placeholder="e.g. Santos" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Middle name is required.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Birth Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="bdate" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Birth date is required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Birth Place <span class="req">*</span></label>
                            <input type="text" class="form-control" name="bplace" placeholder="e.g. Manila" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Birth place is required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Nationality <span class="req">*</span></label>
                            <input type="text" class="form-control" name="nationality" placeholder="e.g. Filipino" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Nationality is required.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6 col-sm-4">
                        <div class="form-group">
                            <label>Age <span class="req">*</span></label>
                            <input type="number" class="form-control" name="age" placeholder="e.g. 14" min="1" max="100" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Age is required.</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="form-group">
                            <label>Sex <span class="req">*</span></label>
                            <select class="form-select" name="sex" required>
                                <option value="">Choose…</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Please select your sex.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Civil Status <span class="req">*</span></label>
                            <select class="form-select" name="status" required>
                                <option value="">Choose…</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                            </select>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Please select your status.</div>
                        </div>
                    </div>
                </div>

                <!-- ── Address ── -->
                <div class="section-label">Address</div>

                <div class="row g-3">
                    <div class="col-12 col-sm-3">
                        <div class="form-group">
                            <label>House No. <span class="req">*</span></label>
                            <input type="text" class="form-control" name="houseno" placeholder="e.g. 123" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="form-group">
                            <label>Street <span class="req">*</span></label>
                            <input type="text" class="form-control" name="street" placeholder="e.g. Rizal St." required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="form-group">
                            <label>Barangay <span class="req">*</span></label>
                            <input type="text" class="form-control" name="brgy" placeholder="e.g. Brgy. 1" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <div class="form-group">
                            <label>Municipality <span class="req">*</span></label>
                            <input type="text" class="form-control" name="municipal" placeholder="e.g. Quezon City" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Required.</div>
                        </div>
                    </div>
                </div>

                <!-- ── Account Details ── -->
                <div class="section-label">Account Details</div>

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Contact Number <span class="req">*</span></label>
                            <input type="tel" class="form-control" name="contact" maxlength="11"
                                   pattern="[0-9]{11}" placeholder="09XXXXXXXXX" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Enter a valid 11-digit number.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Email / Phone <span class="req">*</span></label>
                            <input type="text" class="form-control" name="login_identity"
                                   placeholder="email or phone number" required>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">This field is required.</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <label>Password <span class="req">*</span></label>
                            <div class="password-wrap">
                                <input type="password" class="form-control" id="password-field"
                                       name="password" placeholder="Create a password" required>
                                <span class="fa fa-fw fa-eye toggle-password"
                                      toggle="#password-field" role="button" aria-label="Toggle password"></span>
                            </div>
                            <div class="valid-feedback">Looks good.</div>
                            <div class="invalid-feedback">Password is required.</div>
                        </div>
                    </div>
                </div>

                <!-- ── Terms ── -->
                <div class="terms-row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck" style="font-size:13.5px;">
                            I have read and agree to the
                            <a href="#" class="terms-link" data-bs-toggle="modal" data-bs-target="#termsModal">
                                Terms and Conditions
                            </a>
                        </label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                    </div>
                </div>

                <!-- ── Actions ── -->
                <input type="hidden" name="role" value="resident">

                <div class="form-actions">
                    <button class="btn-submit" type="submit" name="add_resident">
                        <i class="fas fa-paper-plane"></i> Submit Registration
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>

<!-- ── TERMS MODAL ── -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header navy">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="fas fa-file-contract me-2" style="color:var(--gold);"></i>
                    Terms and Conditions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="font-size:14px; line-height:1.75;">
                <h6>1. Data Privacy Act of 2012</h6>
                <p>By registering, you allow EUSEBIA PAZ ARROYO NATIONAL HIGH SCHOOL to collect and process your personal information in accordance with the Data Privacy Act. Your data will be used solely for enrollment and emergency services.</p>

                <h6>2. Accuracy of Information</h6>
                <p>You certify that all information provided is true and correct. Providing false information may lead to the cancellation of your registration or legal action.</p>

                <h6>3. Usage Policy</h6>
                <p>This account is for the exclusive use of the registered student. Any unauthorized use of this system may result in suspension of access.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        onclick="document.getElementById('termsCheck').checked = true;">
                    I Understand &amp; Agree
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer-custom">
  <div class="container">
    <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
    <br><small><?= date('Y') ?> EPAMNHS. All rights reserved.</small>
  </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password toggle
    $(".toggle-password").on("click", function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });
</script>
</body>
</html>
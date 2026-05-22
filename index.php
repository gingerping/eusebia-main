<!DOCTYPE html>
<html lang="en">
<head>
  <title>EPAMNHS | Vision, Mission & Core Values</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
      color: #1a2c3e;
      scroll-behavior: smooth;
    }

    /* ── NAVBAR ── */
    .navbar-custom {
      background: linear-gradient(135deg, #0b2b5c 0%, #0f3b7a 100%);
      padding: 0;
      box-shadow: 0 4px 20px rgba(0,0,0,.12);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: .85rem 1.5rem;
      gap: .75rem;
    }

    .navbar-brand {
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: clamp(1rem, 3.5vw, 1.4rem);
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

    /* Desktop nav buttons */
    .nav-buttons {
      display: flex;
      gap: 10px;
      flex-shrink: 0;
    }

    .btn-nav {
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
    }

    .btn-login {
      background: rgba(255,255,255,.12);
      border: 1px solid rgba(255,255,255,.28);
      color: white;
    }
    .btn-login:hover { background: rgba(255,255,255,.25); color: white; }

    .btn-register {
      background: rgba(255,215,0,.8);
      border: 1px solid #ffd700;
      color: #0b2b5c;
      font-weight: 600;
    }
    .btn-register:hover { background: #ffd700; transform: translateY(-2px); color: #0b2b5c; }

    /* ── MOBILE NAV: always-visible compact buttons ── */
    @media (max-width: 768px) {
      .navbar-inner { padding: .7rem 1rem; }

      /* Keep buttons visible but shrink them */
      .nav-buttons {
        display: flex !important;
        gap: 7px;
      }

      .btn-nav {
        padding: 6px 13px;
        font-size: .78rem;
        gap: 5px;
      }

      /* Hide text on very small screens, show only icon */
      @media (max-width: 400px) {
        .btn-nav .btn-label { display: none; }
        .btn-nav { padding: 8px 11px; border-radius: 50%; }
      }
    }

    /* ── HERO ── */
    .hero {
      position: relative;
      width: 100%;
      height: 360px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .hero-bg {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 0;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(11,43,92,.35), rgba(15,59,122,.5));
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      padding: 2rem 1.5rem;
    }

    .seal-ring {
      width: 90px;
      height: 90px;
      margin: 0 auto 1.1rem;
      background: rgba(255,255,255,.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid rgba(255,215,0,.5);
    }

    .seal-ring img {
      width: 64px;
      height: 64px;
      object-fit: contain;
      filter: brightness(1.05) contrast(1.1);
      background: rgba(255,255,255,.9);
      border-radius: 50%;
      padding: 6px;
    }

    .school-name {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.3rem, 4.5vw, 2.1rem);
      font-weight: 700;
      color: white;
      text-shadow: 0 2px 8px rgba(0,0,0,.3);
      max-width: 640px;
      margin: 0 auto;
    }

    .school-sub {
      color: rgba(255,215,0,.85);
      font-size: .85rem;
      letter-spacing: 1px;
      margin-top: .35rem;
    }

    .divider-gold {
      width: 60px;
      height: 2px;
      background: linear-gradient(90deg, transparent, #ffd700, transparent);
      margin: 1rem auto;
    }

    .hero-caption {
      color: rgba(255,255,255,.7);
      font-size: .82rem;
    }

    @media (max-width: 600px) {
      .hero { height: 300px; }
      .seal-ring { width: 72px; height: 72px; }
      .seal-ring img { width: 52px; height: 52px; }
    }

    /* ── CONTENT / CARDS ── */
    .content {
      max-width: 900px;
      margin: 0 auto;
      padding: 2rem 1.2rem 4rem;
    }

    .card-section { margin-top: -1.8rem; }

    .mv-card {
      background: white;
      border-radius: 24px;
      box-shadow: 0 14px 30px -10px rgba(0,0,0,.09);
      margin-bottom: 1.6rem;
      transition: transform .3s, box-shadow .3s;
      overflow: hidden;
    }

    .mv-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 22px 38px -14px rgba(0,0,0,.13);
    }

    .card-header {
      padding: 1.4rem 1.6rem .8rem;
      display: flex;
      align-items: center;
      gap: .9rem;
      border-bottom: 1px solid #eef2f8;
      background: none;
    }

    .icon-badge {
      width: 52px;
      height: 52px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #eef2ff;
      flex-shrink: 0;
    }

    .icon-badge svg { width: 26px; height: 26px; }

    .card-label {
      font-size: 1.5rem;
      font-weight: 700;
      font-family: 'Playfair Display', serif;
      color: #0b2b5c;
      line-height: 1.2;
      margin: 0;
    }

    .card-tagline {
      font-size: .72rem;
      text-transform: uppercase;
      letter-spacing: 1.4px;
      color: #5a687c;
      margin: 0;
    }

    .card-body {
      padding: 1.4rem 1.6rem 1.8rem;
      font-size: .97rem;
      line-height: 1.7;
      color: #2c3e4e;
    }

    .card-body p { margin-bottom: .75rem; }
    .card-body p:last-child { margin-bottom: 0; }

    /* Core values grid */
    .values-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 10px;
      margin-top: .9rem;
    }

    .value-pill {
      background: #f8fafc;
      border-radius: 12px;
      padding: .65rem .95rem;
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid #e2e8f0;
      transition: all .2s;
    }

    .value-pill:hover {
      background: #fff;
      border-color: #cbd5e1;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0,0,0,.05);
    }

    .value-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .dot-blue   { background: #1e88e5; box-shadow: 0 0 7px rgba(30,136,229,.35); }
    .dot-green  { background: #43a047; box-shadow: 0 0 7px rgba(67,160,71,.35); }
    .dot-purple { background: #8e44ad; box-shadow: 0 0 7px rgba(142,68,173,.35); }
    .dot-amber  { background: #fb8c00; box-shadow: 0 0 7px rgba(251,140,0,.35); }

    .value-text {
      font-weight: 500;
      font-size: .88rem;
      color: #334155;
    }

    @media (max-width: 500px) {
      .card-header { padding: 1.1rem 1.1rem .6rem; }
      .card-body   { padding: 1.1rem 1.1rem 1.4rem; }
      .card-label  { font-size: 1.25rem; }
      .values-grid { grid-template-columns: 1fr 1fr; }
    }

    /* ── BACK TO TOP ── */
    .top-link {
      position: fixed;
      bottom: 1.8rem;
      right: 1.5rem;
      background: #0b2b5c;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      box-shadow: 0 4px 14px rgba(0,0,0,.2);
      transition: all .3s;
      z-index: 99;
      text-decoration: none;
      opacity: 0;
      visibility: hidden;
    }

    .top-link.show { opacity: 1; visibility: visible; }
    .top-link:hover { background: #1f5a9e; transform: translateY(-4px); color: white; }

    /* ── FOOTER ── */
    .footer-custom {
      background: #0b1f33;
      color: #cddcec;
      padding: 1.8rem 1rem;
      text-align: center;
      font-size: .84rem;
      border-top-left-radius: 28px;
      border-top-right-radius: 28px;
    }
  </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar-custom">
  <div class="navbar-inner">
    <a class="navbar-brand" href="#">
      <i class="bi bi-mortarboard-fill" style="flex-shrink:0;"></i>
      <span>EPAMNHS Portal</span>
    </a>

    <!-- Buttons always visible: desktop full, mobile compact -->
    <div class="nav-buttons">
      <a href="login.php" class="btn-nav btn-login">
        <i class="fas fa-sign-in-alt"></i>
        <span class="btn-label">Log in</span>
      </a>
      <a href="resident_registration.php" class="btn-nav btn-register">
        <i class="fas fa-user-plus"></i>
        <span class="btn-label">Register</span>
      </a>
    </div>
  </div>
</nav>

<!-- ── HERO ── -->
<header class="hero">
  <img class="hero-bg" id="heroBg" src="icons/eusebia.jpg" alt="School campus">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="seal-ring">
      <img src="icons/Documents/eusebia.png" alt="School Seal">
    </div>
    <h1 class="school-name">Eusebia Paz Arroyo Memorial National High School</h1>
    <p class="school-sub">Department of Education</p>
    <div class="divider-gold"></div>
    <p class="hero-caption">Mission · Vision · Core Values</p>
  </div>
</header>

<!-- ── CONTENT ── -->
<main class="content">
  <div class="card-section" data-aos="fade-up">

    <!-- VISION -->
    <article class="mv-card">
      <div class="card-header">
        <div class="icon-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="#185fa5" stroke-width="1.8">
            <circle cx="12" cy="12" r="3"/>
            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/>
          </svg>
        </div>
        <div>
          <h2 class="card-label">Vision</h2>
          <p class="card-tagline">What we aspire to become</p>
        </div>
      </div>
      <div class="card-body">
        <p>We dream of Filipinos who passionately love their country and whose values and competencies enable them to realize their full potential and contribute meaningfully to building the nation.</p>
        <p>As a learner-centered public institution, the Department of Education continuously improves itself to better serve its stakeholders.</p>
      </div>
    </article>

    <!-- MISSION -->
    <article class="mv-card" data-aos="fade-up" data-aos-delay="80">
      <div class="card-header">
        <div class="icon-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="#3b6d11" stroke-width="1.8">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <path d="M2 17l10 5 10-5"/>
            <path d="M2 12l10 5 10-5"/>
          </svg>
        </div>
        <div>
          <h2 class="card-label">Mission</h2>
          <p class="card-tagline">What we commit to do</p>
        </div>
      </div>
      <div class="card-body">
        <p>To protect and promote the right of every Filipino to quality, equitable, culture-based, and complete basic education where:</p>
        <p>* Students learn in a child-friendly, gender-sensitive, safe, and motivating environment.<br>
           * Teachers facilitate learning and constantly nurture every learner.<br>
           * Administrators and staff ensure an enabling and supportive environment for effective learning.<br>
           * Family, community, and stakeholders actively engage and share responsibility for developing life-long learners.</p>
      </div>
    </article>

    <!-- CORE VALUES -->
    <article class="mv-card" data-aos="fade-up" data-aos-delay="160">
      <div class="card-header">
        <div class="icon-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="#534ab7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <div>
          <h2 class="card-label">Core Values</h2>
          <p class="card-tagline">Principles we live by</p>
        </div>
      </div>
      <div class="card-body">
        <div class="values-grid">
          <div class="value-pill"><span class="value-dot dot-blue"></span><span class="value-text">Maka-Diyos</span></div>
          <div class="value-pill"><span class="value-dot dot-green"></span><span class="value-text">Maka-tao</span></div>
          <div class="value-pill"><span class="value-dot dot-purple"></span><span class="value-text">Makakalikasan</span></div>
          <div class="value-pill"><span class="value-dot dot-amber"></span><span class="value-text">Makabansa</span></div>
        </div>
      </div>
    </article>

  </div>
</main>

<!-- Back to Top -->
<a href="#" class="top-link" id="backToTopBtn">
  <i class="fas fa-arrow-up"></i>
</a>

<!-- Footer -->
<footer class="footer-custom">
  <div class="container">
    <i class="fas fa-school me-2"></i> Eusebia Paz Arroyo Memorial National High School
    <br><small><?= date('Y') ?> EPAMNHS. All rights reserved.</small>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 800, once: true, offset: 40 });

  // Back to top
  const backBtn = document.getElementById('backToTopBtn');
  window.addEventListener('scroll', () => {
    backBtn.classList.toggle('show', window.scrollY > 300);
  });
  backBtn.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
</body>
</html>
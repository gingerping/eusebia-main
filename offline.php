<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EPAMNHS – You're Offline</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(145deg, #f8faff 0%, #f0f4fe 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #1a2c3e;
    }

    .card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(11,43,92,.12);
      padding: 3rem 2.5rem;
      max-width: 440px;
      width: 90%;
      text-align: center;
    }

    .icon {
      width: 90px;
      height: 90px;
      background: linear-gradient(135deg, #0b2b5c, #0f3b7a);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
    }

    .icon svg { width: 44px; height: 44px; fill: #f0c040; }

    h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0b2b5c;
      margin-bottom: .6rem;
    }

    p {
      color: #4a5568;
      line-height: 1.6;
      margin-bottom: 2rem;
    }

    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #0b2b5c, #0f3b7a);
      color: #fff;
      padding: .75rem 2rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      font-size: .95rem;
      cursor: pointer;
      border: none;
      transition: opacity .2s;
    }

    .btn:hover { opacity: .85; }

    footer {
      margin-top: 2rem;
      font-size: .8rem;
      color: #9aabba;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">
      <!-- Wifi-off icon -->
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 1l22 22M16.72 11.06A10.94 10.94 0 0 1 19 12.55M5 12.55a10.94 10.94 0 0 1 5.17-2.8M10.71 5.05A16 16 0 0 1 22.56 9M1.42 9a15.91 15.91 0 0 1 4.7-2.88M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/>
      </svg>
    </div>

    <h1>You're offline</h1>
    <p>
      It looks like you've lost your internet connection.
      Some pages you've visited recently may still be available,
      but this one needs a live connection.
    </p>

    <button class="btn" onclick="tryReload()">Try Again</button>
  </div>

  <footer>EPAMNHS &mdash; Eusebia Paz Arroyo Memorial National High School</footer>

  <script>
    function tryReload() {
      window.location.reload();
    }

    // Auto-reload when connection is restored
    window.addEventListener('online', () => window.location.reload());
  </script>
</body>
</html>

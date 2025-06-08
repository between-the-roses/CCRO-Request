<?php
session_start();

include "../backend/db.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Certificate Type</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: url('../images/background.png') no-repeat center center fixed;
      background-size: cover;
      color: #333;
      margin: 0;
      min-height: 100vh;
    }

    /* Style the content to be centered */
    .content {
      flex: 1;
      padding: 20px;
      margin-bottom: 80px;
    }

    /* Navbar styles (assuming you want to keep them) */
    .logo-img {
      height: 45px;
      width: auto;
      margin-right: 15px;
    }

    .logo-stack-img {
      height: 200px;
      object-fit: contain;
    }

    .full-height {
      min-height: 100vh;
      padding-top: 100px;
      padding-bottom: 40px;
    }

    /* New styles for certificate cards */
    .certificate-card {
      border-radius: 32px;
      box-shadow: 0 8px 32px 0 rgba(0,0,0,0.13);
      transition: transform 0.2s, box-shadow 0.2s;
      min-height: 100px;
      display: flex;
      flex-direction: column;
      justify-content: stretch;
      background: #fff;
    }

    .certificate-card:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 16px 48px 0 rgba(0,0,0,0.18);
    }

    .bg-marriage {
      background: #FFC72C !important;
    }

    .cert-title {
      font-size: 2.7rem;
      font-weight: 800;
      line-height: 1.1;
      color: #1a2340;
      letter-spacing: -1px;
    }

    .bg-marriage .cert-title,
    .bg-marriage .cert-desc {
      color: #fff !important;
    }

    .cert-desc {
      font-size: 1.15rem;
      color: #222;
      font-weight: 400;
    }

    .get-now-btn {
      background: #FFC72C;
      color: #fff;
      font-weight: 700;
      border: none;
      border-radius: 30px;
      padding: 16px 48px;
      font-size: 1.2rem;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: 0 2px 12px 0 rgba(0,0,0,0.10);
      margin-top: 30px;
    }

    .get-now-btn:hover,
    .get-now-btn:focus {
      background: #e6b200;
      color: #fff;
      box-shadow: 0 4px 16px 0 rgba(0,0,0,0.16);
    }

    .get-now-btn-marriage {
      background: #fff;
      color: #FFC72C;
    }

    .get-now-btn-marriage:hover,
    .get-now-btn-marriage:focus {
      background: #ffe082;
      color: #bfa000;
    }

    @media (max-width: 991px) {
      .certificate-card {
        min-height: 100px;
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include "includes/navbar.php"; ?>

  <!-- Main Content -->
  <div class="content" id="content">
    <h1 class="fw-bold text-center mb-2" style="font-size:2.7rem;letter-spacing:-1px;">Select Certificate Type</h1>
    <p class="lead text-center mb-5" style="font-size:1.3rem;">Choose the type of certificate you want to request.</p>
    <div class="container-fluid">
      <div class="row justify-content-center g-5">
        <!-- Live Birth Card -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="certificate-card bg-white shadow-lg w-100 my-auto">
            <div class="p-5 d-flex flex-column align-items-center h-100">
              <h2 class="cert-title mb-3 text-center">Certificate<br>of Live Birth</h2>
              <p class="cert-desc text-center mb-4">
                Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Birth Certificate with just one click!
              </p>
              <button id="liveBirthBtn" class="get-now-btn mt-auto">GET NOW</button>
            </div>
          </div>
        </div>
        <!-- Marriage Card -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="certificate-card bg-marriage shadow-lg w-100 my-auto">
            <div class="p-5 d-flex flex-column align-items-center h-100">
              <h2 class="cert-title mb-3 text-center text-white">Certificate<br>of Marriage</h2>
              <p class="cert-desc text-center mb-4 text-white">
                Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Marriage Certificate with just one click!
              </p>
              <button id="marriageBtn" class="get-now-btn get-now-btn-marriage mt-auto">GET NOW</button>
            </div>
          </div>
        </div>
        <!-- Death Card -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="certificate-card bg-white shadow-lg w-100 my-auto">
            <div class="p-5 d-flex flex-column align-items-center h-100">
              <h2 class="cert-title mb-3 text-center">Certificate<br>of Death</h2>
              <p class="cert-desc text-center mb-4">
                Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Death Certificate with just one click!
              </p>
              <button id="deathBtn" class="get-now-btn mt-auto">GET NOW</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recaptcha Modal (place this BEFORE the confirmationModal) -->
  <div class="modal fade" id="recaptchaModal" tabindex="-1" aria-labelledby="recaptchaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h2 class="fw-bold mb-4" id="recaptchaLabel">Verify you are human</h2>
        <div class="d-flex justify-content-center mb-3">
          <div id="recaptcha-container">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAABecoQ9SovGYRIe8"></div>
          </div>
        </div>
        <div id="recaptcha-error" class="text-danger mb-2" style="display:none;">Please complete the reCAPTCHA.</div>
        <button class="btn btn-success px-4" onclick="submitRecaptcha()">Continue</button>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h2 class="fw-bold mb-4" id="modalLabel">Are you new or old requester?</h2>
        <div class="d-flex justify-content-center gap-3">
          <button class="btn btn-primary px-4" onclick="handleYes()">NEW</button>
          <button class="btn btn-secondary px-4" onclick="handleNo()">OLD</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

  <!-- Modal Logic -->
  <script>
    const liveBirthBtn = document.getElementById('liveBirthBtn');
    const marriageBtn = document.getElementById('marriageBtn');
    const deathBtn = document.getElementById('deathBtn');
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const recaptchaModal = new bootstrap.Modal(document.getElementById('recaptchaModal'));

    let activeType = "livebirth"; // default

    // Event listeners for each button
    liveBirthBtn.addEventListener('click', () => {
      activeType = "livebirth";
      // Update modal title to be more specific
      document.getElementById('modalLabel').textContent = "Are you requesting a Live Birth certificate for yourself?";
      confirmationModal.show();
    });

    marriageBtn.addEventListener('click', () => {
      activeType = "marriage";
      // Update modal title to be more specific about marriage certificate
      document.getElementById('modalLabel').textContent = "Are you requesting a Marriage certificate for yourself?";
      confirmationModal.show();
    });

    deathBtn.addEventListener('click', () => {
      activeType = "death";
      // Update modal title to be more specific
      document.getElementById('modalLabel').textContent = "Are you the authorized representative for this Death certificate?";
      confirmationModal.show();
    });

    function handleYes() {
      confirmationModal.hide();
      recaptchaModal.show(); // Show reCAPTCHA modal on confirmation
    }

    function handleNo() {
      confirmationModal.hide();
      window.location.href = "../auth/verify.php?type=" + encodeURIComponent(activeType) + "&self=no";
    }

    function submitRecaptcha() {
      // Cloudflare Turnstile sets the response in this hidden input
      const response = document.querySelector('input[name="cf-turnstile-response"]')?.value;
      if (!response) {
        document.getElementById('recaptcha-error').style.display = 'block';
        return;
      }
      document.getElementById('recaptcha-error').style.display = 'none';
      window.location.href = "../auth/verify.php?type=" + encodeURIComponent(activeType) + "&self=yes";
    }
  </script>
</body>
</html>
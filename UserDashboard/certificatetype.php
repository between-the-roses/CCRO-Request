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
  <link href="https://fonts.googleapis.com/css?family=Inter:700,500,400&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    body {
      font-family: 'Inter', Arial, sans-serif;
      background: linear-gradient(135deg, #e3f0ff 0%, #e6ffe6 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }
    .content {
      flex: 1;
      padding: 20px;
      margin-bottom: 50px;
      padding-top: 100px;
    }
    .certificate-section {
      display: flex;
      gap: 2.5rem;
      justify-content: center;
      align-items: flex-start;
      margin: 3.5rem auto 0;
      max-width: 1200px;
      flex-wrap: wrap;
    }
    .certificate-card {
      background: rgba(255, 255, 255, 0.65);
      backdrop-filter: blur(7px);
      border-radius: 2.3rem;
      box-shadow: 0 8px 36px rgba(55,78,144,0.10);
      padding: 2.5rem 2.2rem 2rem 2.2rem;
      width: 350px;
      min-height: 380px;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: transform 0.22s, box-shadow 0.22s;
      border: 3px solid transparent;
      cursor: pointer;
    }
    .certificate-card:hover {
      transform: translateY(-9px) scale(1.035);
      box-shadow: 0 12px 48px rgba(40,190,255,0.20);
      border: 3px solid #8ed1fc;
    }
    .certificate-card.yellow { background: rgba(255, 217, 85, 0.15); border-color: #ffe066; }
    .certificate-card.yellow:hover { border-color: #ffd600; }
    .cert-icon {
      font-size: 3.4rem;
      margin-bottom: 1.2rem;
      color: #38bdf8;
    }
    .certificate-card.yellow .cert-icon { color: #f2b93b; }
    .cert-title {
      font-size: 2.1rem;
      font-weight: 700;
      margin: 0 0 1rem 0;
      color: #112244;
      text-align: center;
      letter-spacing: 0.02em;
    }
    .certificate-card.yellow .cert-title { color: #b28b09; }
    .cert-desc {
      font-size: 1.08rem;
      color: #343f56;
      margin-bottom: 2.2rem;
      text-align: center;
    }
    .certificate-card.yellow .cert-desc { color: #94771d; }
    .get-now-btn {
      background: linear-gradient(90deg, #ffd600 60%, #ffe066 100%);
      color: #1d2549;
      border: none;
      border-radius: 50px;
      padding: 1.1rem 2.7rem;
      font-size: 1.15rem;
      font-weight: 600;
      box-shadow: 0 4px 18px rgba(210,174,27,0.09);
      transition: background 0.19s, box-shadow 0.19s;
      margin-top: auto;
    }
    .get-now-btn:hover {
      background: linear-gradient(90deg, #ffef9f 60%, #ffe066 100%);
      box-shadow: 0 6px 36px rgba(210,174,27,0.13);
    }
    @media (max-width: 1100px) {
      .certificate-section { flex-direction: column; gap: 1.5rem; align-items: center; }
      .certificate-card { width: 100%; max-width: 410px; }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include "includes/navbar.php"; ?>

  <!-- Main Content -->
  <div class="content" id="content">
    <h1 class="fw-bold text-center mb-2" style="font-size:2.7rem; letter-spacing:-1px;">Select Certificate Type</h1>
    <p class="lead text-center mb-5" style="font-size:1.3rem;">Choose the type of certificate you want to request.</p>
    <div class="certificate-section">
      <!-- Live Birth Card -->
      <div class="certificate-card">
        <span class="cert-icon"><i class="fas fa-baby"></i></span>
        <h2 class="cert-title">Certificate<br>of Live Birth</h2>
        <p class="cert-desc">
          Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Birth Certificate with just one click!
        </p>
        <button id="liveBirthBtn" class="get-now-btn">GET NOW</button>
      </div>
      <!-- Marriage Card -->
      <div class="certificate-card yellow">
        <span class="cert-icon"><i class="fas fa-ring"></i></span>
        <h2 class="cert-title">Certificate<br>of Marriage</h2>
        <p class="cert-desc">
          Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Marriage Certificate with just one click!
        </p>
        <button id="marriageBtn" class="get-now-btn">GET NOW</button>
      </div>
      <!-- Death Card -->
      <div class="certificate-card">
        <span class="cert-icon"><i class="fas fa-cross"></i></span>
        <h2 class="cert-title">Certificate<br>of Death</h2>
        <p class="cert-desc">
          Get <b>Certified True Machine Copy</b> and <b>Certified True Copy</b> of Death Certificate with just one click!
        </p>
        <button id="deathBtn" class="get-now-btn">GET NOW</button>
      </div>
    </div>
  </div>

  <?php include "validation.php"; ?>
  
  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h2 class="fw-bold mb-4" id="modalLabel">Are you a new or returning requestor?</h2>
        <div class="d-flex justify-content-center gap-3">
          <button class="btn btn-primary px-4" onclick="handleYes()">NEW</button>
          <button class="btn btn-secondary px-4" onclick="handleNo()">RETURNING</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts (load ONCE ONLY) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
  <script>
    const liveBirthBtn = document.getElementById('liveBirthBtn');
    const marriageBtn = document.getElementById('marriageBtn');
    const deathBtn = document.getElementById('deathBtn');
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    
    // Global variable to store active certificate type
    window.activeType = "marriage"; // default

    liveBirthBtn.addEventListener('click', () => {
        window.activeType = "livebirth";
        console.log('🎯 Selected LIVEBIRTH certificate');
        document.getElementById('modalLabel').textContent = "Are you a new or returning requestor for a Live Birth certificate?";
        confirmationModal.show();
    });
    
    marriageBtn.addEventListener('click', () => {
        window.activeType = "marriage";
        console.log('🎯 Selected MARRIAGE certificate');
        document.getElementById('modalLabel').textContent = "Are you a new or returning requestor for a Marriage certificate?";
        confirmationModal.show();
    });
    
    deathBtn.addEventListener('click', () => {
        window.activeType = "death";
        console.log('🎯 Selected DEATH certificate');
        document.getElementById('modalLabel').textContent = "Are you a new or returning requestor for a Death certificate?";
        confirmationModal.show();
    });

    function handleYes() {
        console.log('🚀 NEW user selected for certificate type:', window.activeType);
        confirmationModal.hide();
        
        // Wait a moment for modal to hide, then show verification
        setTimeout(() => {
            // Make sure the certificate type is set globally
            if (typeof setCertificateType === 'function') {
                setCertificateType(window.activeType);
            }
            
            const recaptchaModal = new bootstrap.Modal(document.getElementById('recaptchaModal'));
            recaptchaModal.show();
        }, 300);
    }
    
    function handleNo() {
        console.log('🔄 RETURNING user selected for certificate type:', window.activeType);
        confirmationModal.hide();
        
        // For returning users, redirect to the returning profile page
        window.location.href = `./profile/returning.php?type=${encodeURIComponent(window.activeType)}`;
    }
  </script>
</body>
</html>

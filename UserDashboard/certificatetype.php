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
    }

    .logo-img {
      height: 45px;
      width: auto;
      margin-right: 15px;
    }

    .logo-stack-img {
      height: 200px;
      object-fit: contain;
    }

    .btn-option {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 30px;
      background-color: #fff;
      border: none;
      border-radius: 20px;
      width: 100%;
      font-size: 2rem;
      font-weight: 700;
      justify-content: flex-start;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: transform 0.2s ease, background 0.2s ease;
    }

    .btn-option:hover {
      background-color: #f2f2f2;
      transform: scale(1.01);
    }

    .btn-option i {
      font-size: 2.5rem;
      color: #38bdf8;
    }

    .full-height {
      min-height: 100vh;
      padding-top: 100px;
      padding-bottom: 40px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include "includes/navbar.php"; ?>

  <!-- Main Fullscreen Layout -->
  <div class="container-fluid full-height d-flex align-items-center">
    <div class="row w-100">
      <!-- Left Column -->
      <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center text-center text-lg-start px-5">
        <div class="d-flex gap-4 mb-4">
          <img src="../images/Logo 1.png" alt="Logo 1" class="logo-stack-img" />
          <img src="../images/Logo 2.png" alt="Logo 2" class="logo-stack-img" />
        </div>
        <h1 class="display-3 fw-bold">Iligan Civil Registry</h1>
        <h2 class="text-primary fw-semibold fs-1">Online Appointment System</h2>
      </div>

      <!-- Right Column -->
      <div class="col-lg-6 d-flex flex-column justify-content-center align-items-center gap-4 px-5">
        <button id="liveBirthBtn" class="btn-option w-100"><i class="bx bx-user"></i> LIVE BIRTH</button>
        <button id="marriageBtn" class="btn-option w-100"><i class="bx bx-heart"></i> MARRIAGE</button>
        <button id="deathBtn" class="btn-option w-100"><i class="bx bx-plus-medical"></i> DEATH</button>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h2 class="fw-bold mb-4" id="modalLabel">Are you requesting for yourself?</h2>
        <div class="d-flex justify-content-center gap-3">
          <button class="btn btn-primary px-4" onclick="handleYes()">YES</button>
          <button class="btn btn-secondary px-4" onclick="handleNo()">NO</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Modal Logic -->
  <script>
    const liveBirthBtn = document.getElementById('liveBirthBtn');
    const marriageBtn = document.getElementById('marriageBtn');
    const deathBtn = document.getElementById('deathBtn');
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

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
  window.location.href = "../auth/verify.php?type=" + encodeURIComponent(activeType) + "&self=yes";
}

function handleNo() {
  confirmationModal.hide();
  window.location.href = "../auth/verify.php?type=" + encodeURIComponent(activeType) + "&self=no";
}

</script>
</body>
</html>
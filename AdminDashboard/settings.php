<?php include './includes/sidebar.php'; ?>
<?php include './includes/navbar.php'; ?>

<link rel="stylesheet" href="settings/settings.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<div class="main" style="margin-left:250px";>
  <div class="content settings-container">
    
    <!-- TEAM MANAGEMENT SECTION -->
    <?php include 'settings/team_management.php'; ?>

    <!-- VERIFIER SECTION -->
    <?php include 'settings/verifier.php'; ?>

    <!-- HISTORY SECTION -->
    <?php include 'settings/history.php'; ?>
    
  </div>
</div>

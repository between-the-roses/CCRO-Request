<?php
// AdminDashboard/transactions/index.php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/repository/stats_repo.php';

try {
  $cert          = certCounts($conn);
  $statusStats   = statusCounts($conn);
  $timeStats     = timeCounts($conn);
  $avgHours      = avgProcessingHours($conn);
  $recent        = recentRequests($conn, 10);
  $calendarEvents= calendarEvents($conn);
} catch (Throwable $e) {
  error_log("transactions/index error: ".$e->getMessage());
  $cert = ['birthCount'=>0,'birthCompleted'=>0,'marriageCount'=>0,'marriageCompleted'=>0,'deathCount'=>0,'deathCompleted'=>0];
  $statusStats = ['totalRequests'=>0,'pendingRequests'=>0,'confirmedRequests'=>0,'cancelledRequests'=>0,'processingRequests'=>0];
  $timeStats = ['todayRequests'=>0,'weekRequests'=>0,'monthRequests'=>0,'yearRequests'=>0];
  $avgHours = 0; $recent = []; $calendarEvents = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="main">
  <div class="content">
    <div class="container-fluid">
      <div class="content-alignment">

        <?php $timeStats = $timeStats; include __DIR__ . '/partials/quick_stats.php'; ?>
        <?php $statusStats = $statusStats; include __DIR__ . '/partials/overall_stats.php'; ?>

        <?php if ($avgHours > 0): ?>
        <div class="processing-time loading-animation">
          <h4><i class='bx bx-time'></i> Average Processing Time</h4>
          <h2><?= $avgHours ?></h2>
          <span>Hours</span>
        </div>
        <?php endif; ?>

        <?php $cert = $cert; include __DIR__ . '/partials/certificate_stats.php'; ?>

        <div class="row">
          <div class="col-lg-6">
            <?php $recent = $recent; include __DIR__ . '/partials/activity_feed.php'; ?>
          </div>
          <!-- <div class="col-lg-6">
            <?php $calendarEvents = $calendarEvents; include __DIR__ . '/partials/calendar.php'; ?>
          </div> -->
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// minimal init/animations (you can keep your original scripts here)
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(() => {
    document.querySelectorAll('.loading-animation').forEach((el, i) =>
      setTimeout(() => el.classList.add('loaded'), i * 100)
    );
  }, 300);
});
</script>
</body>
</html>

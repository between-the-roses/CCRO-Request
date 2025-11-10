<?php /** @var array $statusStats */ ?>
<div class="stats-overview loading-animation first-section">
  <h3 class="section-title"><i class='bx bx-chart'></i> Overall Statistics</h3>
  <div class="row g-0">
    <div class="col-md-3"><div class="stat-item">
      <div class="stat-number"><?= number_format($statusStats['totalRequests']) ?></div>
      <div class="stat-label">Total Requests</div>
    </div></div>
    <div class="col-md-3"><div class="stat-item">
      <div class="stat-number" style="color:#f39c12;"><?= number_format($statusStats['pendingRequests']) ?></div>
      <div class="stat-label">Pending</div>
    </div></div>
    <div class="col-md-3"><div class="stat-item">
      <div class="stat-number" style="color:#27ae60;"><?= number_format($statusStats['confirmedRequests']) ?></div>
      <div class="stat-label">Confirmed</div>
    </div></div>
    <div class="col-md-3"><div class="stat-item">
      <div class="stat-number" style="color:#e74c3c;"><?= number_format($statusStats['cancelledRequests']) ?></div>
      <div class="stat-label">Cancelled</div>
    </div></div>
  </div>
</div>

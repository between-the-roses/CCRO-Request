<?php /** @var array $timeStats */ ?>
<div class="quick-stats">
  <div class="stats-card today">
    <i class='bx bx-calendar-check'></i>
    <div class="time-stats">
      <h6>Today's Requests</h6>
      <h3><?= number_format($timeStats['todayRequests']) ?></h3>
    </div>
  </div>
  <div class="stats-card week">
    <i class='bx bx-calendar-week'></i>
    <div class="time-stats">
      <h6>This Week</h6>
      <h3><?= number_format($timeStats['weekRequests']) ?></h3>
    </div>
  </div>
  <div class="stats-card month">
    <i class='bx bx-calendar'></i>
    <div class="time-stats">
      <h6>This Month</h6>
      <h3><?= number_format($timeStats['monthRequests']) ?></h3>
    </div>
  </div>
  <div class="stats-card year">
    <i class='bx bx-calendar-alt'></i>
    <div class="time-stats">
      <h6>This Year</h6>
      <h3><?= number_format($timeStats['yearRequests']) ?></h3>
    </div>
  </div>
</div>

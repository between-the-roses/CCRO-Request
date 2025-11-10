<?php /** @var array $calendarEvents */ ?>
<div class="calendar loading-animation">
  <h3 class="section-title"><i class='bx bx-calendar'></i> Calendar</h3>
  <div class="calendar-header">
    <button class="calendar-nav-btn" onclick="prevMonth()"><i class='bx bx-chevron-left'></i></button>
    <h4 id="calendar-title"></h4>
    <button class="calendar-nav-btn" onclick="nextMonth()"><i class='bx bx-chevron-right'></i></button>
  </div>

  <table class="table">
    <thead><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr></thead>
    <tbody id="calendar-body"></tbody>
  </table>
</div>

<script>
  window.CALENDAR_EVENTS = <?= json_encode($calendarEvents, JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="assets/calendar.js"></script>

<?php
$birthCount = 80;
$birthCompleted = 50;
$marriageCount = 50;
$marriageCompleted = 45;
$deathCount = 60;
$deathCompleted = 58;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    /* Main Content Styles */
    .main {
        margin-left: 220px; /* Default margin when sidebar is expanded */
        margin-top: 60px; /* Matches the navbar height */
        padding: 20px;
        background-color: #f8f9fa;
        min-height: calc(100vh - 60px); /* Ensures the content spans the full height */
        transition: margin-left 0.3s ease; /* Smooth transition for content adjustment */
    }

    .sidebar.collapsed ~ .main {
        margin-left: 0; /* Reset margin when sidebar is collapsed */
    }
    .user {
      font-size: 16px;
      font-weight: 500;
    }
    .content {
      padding: 30px;
    }
    .card-box {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .calendar {
      margin-top: 30px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .calendar td {
      padding: 15px;
    }
    .highlight-red {
      background: #f44336;
      color: white;
      border-radius: 12px;
    }
    .highlight-green {
      background: #4caf50;
      color: white;
      border-radius: 12px;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>
<?php include 'navbar.php'; ?>

<div class="main">
  <div class="content">
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card-box">
          <h4>Birth</h4>
          <h2><?= $birthCount ?></h2>
          <p><?= $birthCompleted ?> Completed</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-box">
          <h4>Marriage</h4>
          <h2><?= $marriageCount ?></h2>
          <p><?= $marriageCompleted ?> Completed</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-box">
          <h4>Death</h4>
          <h2><?= $deathCount ?></h2>
          <p><?= $deathCompleted ?> Completed</p>
        </div>
      </div>
    </div>

    <div class="calendar">
      <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-outline-primary" onclick="prevMonth()">&lt;</button>
        <h4 id="calendar-title">March 2025</h4>
        <button class="btn btn-outline-primary" onclick="nextMonth()">&gt;</button>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
            <th>Thu</th><th>Fri</th><th>Sat</th>
          </tr>
        </thead>
        <tbody id="calendar-body">
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const calendarBody = document.getElementById("calendar-body");
  const calendarTitle = document.getElementById("calendar-title");

  const generateCalendar = (year, month) => {
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    calendarBody.innerHTML = "";
    calendarTitle.textContent = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });

    let row = document.createElement("tr");
    for (let i = 0; i < firstDay; i++) row.appendChild(document.createElement("td"));

    for (let day = 1; day <= lastDate; day++) {
      if (row.children.length === 7) {
        calendarBody.appendChild(row);
        row = document.createElement("tr");
      }

      const td = document.createElement("td");
      td.textContent = day;

      if (day === 27) td.classList.add("highlight-green");
      if (day === 31) td.classList.add("highlight-red");

      row.appendChild(td);
    }

    calendarBody.appendChild(row);
  };

  let currentMonth = 2; // March
  let currentYear = 2025;

  function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    generateCalendar(currentYear, currentMonth);
  }

  function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    generateCalendar(currentYear, currentMonth);
  }

  generateCalendar(currentYear, currentMonth);
</script>
</body>
</html>
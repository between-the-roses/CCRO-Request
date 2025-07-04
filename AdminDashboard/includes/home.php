<?php
// Initialize variables with default values
$birthCount = 0;
$birthCompleted = 0;
$marriageCount = 0;
$marriageCompleted = 0;
$deathCount = 0;
$deathCompleted = 0;
$totalRequests = 0;
$pendingRequests = 0;
$confirmedRequests = 0;
$cancelledRequests = 0;

// Include database connection
try {
    $db_path = __DIR__ . '/../../backend/db.php';
    if (file_exists($db_path)) {
        include $db_path;
    } else {
        // Alternative path check
        $alt_db_path = '../backend/db.php';
        if (file_exists($alt_db_path)) {
            include $alt_db_path;
        } else {
            throw new Exception("Database configuration file not found");
        }
    }

    // Check if connection is established
    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection not established");
    }

    if ($conn instanceof PDO) {
        // Count Birth Certificates
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%birth%' OR LOWER(certificate_type) LIKE '%livebirth%'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $birthCount = $result['total'];

        // Count Completed Birth Certificates
        $stmt = $conn->prepare("
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE (LOWER(c.certificate_type) LIKE '%birth%' OR LOWER(c.certificate_type) LIKE '%livebirth%') 
            AND t.status = 'confirmed'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $birthCompleted = $result['completed'];

        // Count Marriage Certificates
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%marriage%'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $marriageCount = $result['total'];

        // Count Completed Marriage Certificates
        $stmt = $conn->prepare("
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE LOWER(c.certificate_type) LIKE '%marriage%' 
            AND t.status = 'confirmed'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $marriageCompleted = $result['completed'];

        // Count Death Certificates
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%death%'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $deathCount = $result['total'];

        // Count Completed Death Certificates
        $stmt = $conn->prepare("
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE LOWER(c.certificate_type) LIKE '%death%' 
            AND t.status = 'confirmed'
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $deathCompleted = $result['completed'];

        // Count total requests
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM customer");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRequests = $result['total'];

        // Count requests by status
        $stmt = $conn->prepare("
            SELECT 
                COALESCE(t.status, 'pending') as status,
                COUNT(*) as count
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            GROUP BY COALESCE(t.status, 'pending')
        ");
        $stmt->execute();
        $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($statusCounts as $statusCount) {
            switch ($statusCount['status']) {
                case 'pending':
                    $pendingRequests = $statusCount['count'];
                    break;
                case 'confirmed':
                    $confirmedRequests = $statusCount['count'];
                    break;
                case 'cancelled':
                    $cancelledRequests = $statusCount['count'];
                    break;
            }
        }

    } else {
        // MySQLi version
        // Count Birth Certificates
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%birth%' OR LOWER(certificate_type) LIKE '%livebirth%'");
        $row = mysqli_fetch_assoc($result);
        $birthCount = $row['total'];

        // Count Completed Birth Certificates
        $result = mysqli_query($conn, "
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE (LOWER(c.certificate_type) LIKE '%birth%' OR LOWER(c.certificate_type) LIKE '%livebirth%') 
            AND t.status = 'confirmed'
        ");
        $row = mysqli_fetch_assoc($result);
        $birthCompleted = $row['completed'];

        // Count Marriage Certificates
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%marriage%'");
        $row = mysqli_fetch_assoc($result);
        $marriageCount = $row['total'];

        // Count Completed Marriage Certificates
        $result = mysqli_query($conn, "
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE LOWER(c.certificate_type) LIKE '%marriage%' 
            AND t.status = 'confirmed'
        ");
        $row = mysqli_fetch_assoc($result);
        $marriageCompleted = $row['completed'];

        // Count Death Certificates
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer WHERE LOWER(certificate_type) LIKE '%death%'");
        $row = mysqli_fetch_assoc($result);
        $deathCount = $row['total'];

        // Count Completed Death Certificates
        $result = mysqli_query($conn, "
            SELECT COUNT(*) as completed 
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            WHERE LOWER(c.certificate_type) LIKE '%death%' 
            AND t.status = 'confirmed'
        ");
        $row = mysqli_fetch_assoc($result);
        $deathCompleted = $row['completed'];

        // Count total requests
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM customer");
        $row = mysqli_fetch_assoc($result);
        $totalRequests = $row['total'];

        // Count requests by status
        $result = mysqli_query($conn, "
            SELECT 
                COALESCE(t.status, 'pending') as status,
                COUNT(*) as count
            FROM customer c 
            LEFT JOIN transaction t ON c.customer_id = t.customer_id 
            GROUP BY COALESCE(t.status, 'pending')
        ");
        
        while ($row = mysqli_fetch_assoc($result)) {
            switch ($row['status']) {
                case 'pending':
                    $pendingRequests = $row['count'];
                    break;
                case 'confirmed':
                    $confirmedRequests = $row['count'];
                    break;
                case 'cancelled':
                    $cancelledRequests = $row['count'];
                    break;
            }
        }
    }

} catch (Exception $e) {
    error_log("Database error in home.php: " . $e->getMessage());
    // Keep default values if database connection fails
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      margin-bottom: 20px;
      position: relative;
      overflow: hidden;
    }
    
    .card-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .card-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4CAF50, #45a049);
    }
    
    .card-box.marriage::before {
      background: linear-gradient(90deg, #2196F3, #1976D2);
    }
    
    .card-box.death::before {
      background: linear-gradient(90deg, #FF9800, #F57C00);
    }
    
    .card-box i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #4CAF50;
    }
    
    .card-box.marriage i {
      color: #2196F3;
    }
    
    .card-box.death i {
      color: #FF9800;
    }
    
    .card-box h4 {
      color: #333;
      font-weight: 600;
      margin-bottom: 10px;
    }
    
    .card-box h2 {
      color: #2c3e50;
      font-weight: 700;
      font-size: 2.5rem;
      margin: 10px 0;
    }
    
    .card-box p {
      color: #666;
      margin: 0;
      font-size: 14px;
    }
    
    .progress-bar-custom {
      background-color: #e9ecef;
      border-radius: 10px;
      height: 8px;
      margin-top: 10px;
      overflow: hidden;
    }
    
    .progress-fill {
      height: 100%;
      border-radius: 10px;
      transition: width 0.3s ease;
    }
    
    .progress-fill.birth {
      background: linear-gradient(90deg, #4CAF50, #45a049);
    }
    
    .progress-fill.marriage {
      background: linear-gradient(90deg, #2196F3, #1976D2);
    }
    
    .progress-fill.death {
      background: linear-gradient(90deg, #FF9800, #F57C00);
    }
    
    .calendar {
      margin-top: 30px;
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .calendar td {
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    
    .calendar td:hover {
      background-color: #f8f9fa;
      border-radius: 8px;
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
    
    .stats-overview {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .stat-item {
      text-align: center;
      padding: 15px;
    }
    
    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: #2c3e50;
    }
    
    .stat-label {
      color: #666;
      font-size: 14px;
      margin-top: 5px;
    }
    
    .section-title {
      color: #2c3e50;
      font-weight: 600;
      margin-bottom: 20px;
      font-size: 1.5rem;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>
<?php include 'navbar.php'; ?>

<div class="main">
  <div class="content">
    <!-- Overall Statistics -->
    <div class="stats-overview">
      <h3 class="section-title">
        <i class='bx bx-chart'></i> Overall Statistics
      </h3>
      <div class="row">
        <div class="col-md-3">
          <div class="stat-item">
            <div class="stat-number"><?= $totalRequests ?></div>
            <div class="stat-label">Total Requests</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <div class="stat-number" style="color: #ffc107;"><?= $pendingRequests ?></div>
            <div class="stat-label">Pending</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <div class="stat-number" style="color: #28a745;"><?= $confirmedRequests ?></div>
            <div class="stat-label">Confirmed</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <div class="stat-number" style="color: #dc3545;"><?= $cancelledRequests ?></div>
            <div class="stat-label">Cancelled</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Certificate Types Statistics -->
    <h3 class="section-title">
      <i class='bx bx-file'></i> Certificate Requests by Type
    </h3>
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card-box birth">
          <i class='bx bx-baby-carriage'></i>
          <h4>Birth Certificates</h4>
          <h2><?= $birthCount ?></h2>
          <p><?= $birthCompleted ?> Completed</p>
          <div class="progress-bar-custom">
            <div class="progress-fill birth" style="width: <?= $birthCount > 0 ? ($birthCompleted / $birthCount) * 100 : 0 ?>%;"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-box marriage">
          <i class='bx bx-heart'></i>
          <h4>Marriage Certificates</h4>
          <h2><?= $marriageCount ?></h2>
          <p><?= $marriageCompleted ?> Completed</p>
          <div class="progress-bar-custom">
            <div class="progress-fill marriage" style="width: <?= $marriageCount > 0 ? ($marriageCompleted / $marriageCount) * 100 : 0 ?>%;"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card-box death">
          <i class='bx bx-cross'></i>
          <h4>Death Certificates</h4>
          <h2><?= $deathCount ?></h2>
          <p><?= $deathCompleted ?> Completed</p>
          <div class="progress-bar-custom">
            <div class="progress-fill death" style="width: <?= $deathCount > 0 ? ($deathCompleted / $deathCount) * 100 : 0 ?>%;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar -->
    <div class="calendar">
      <h3 class="section-title">
        <i class='bx bx-calendar'></i> Calendar
      </h3>
      <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-outline-primary" onclick="prevMonth()">
          <i class='bx bx-chevron-left'></i> Previous
        </button>
        <h4 id="calendar-title">March 2025</h4>
        <button class="btn btn-outline-primary" onclick="nextMonth()">
          Next <i class='bx bx-chevron-right'></i>
        </button>
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

      // Add current date highlighting
      const today = new Date();
      if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
        td.classList.add("highlight-green");
      }

      // Example: highlight specific dates (you can connect this to your database for events)
      if (day === 15) td.classList.add("highlight-red");

      row.appendChild(td);
    }

    calendarBody.appendChild(row);
  };

  let currentMonth = new Date().getMonth();
  let currentYear = new Date().getFullYear();

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

  // Initialize calendar with current date
  generateCalendar(currentYear, currentMonth);

  // Add smooth animations for card boxes
  document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-box');
    cards.forEach((card, index) => {
      setTimeout(() => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 100);
      }, index * 100);
    });
  });
</script>
</body>
</html>
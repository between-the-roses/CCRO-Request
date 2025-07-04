<?php
// Dummy data for demonstration
// These would typically come from database queries in a real application
$overallCount = 190;
$birthCount = 80;
$marriageCount = 60;
$deathCount = 50;

// Monthly data for charts
$monthlyData = [
    'Jan' => ['birth' => 20, 'marriage' => 15, 'death' => 25],
    'Feb' => ['birth' => 25, 'marriage' => 20, 'death' => 30],
    'Mar' => ['birth' => 15, 'marriage' => 25, 'death' => 10],
    'Apr' => ['birth' => 30, 'marriage' => 10, 'death' => 10]
];

// Weekly data for the current month (dummy data)
$weeklyData = [
    'Week 1' => ['birth' => 5, 'marriage' => 3, 'death' => 2],
    'Week 2' => ['birth' => 8, 'marriage' => 4, 'death' => 3],
    'Week 3' => ['birth' => 4, 'marriage' => 7, 'death' => 1],
    'Week 4' => ['birth' => 3, 'marriage' => 6, 'death' => 4]
];

// Yearly data
$yearlyData = [
    '2022' => ['birth' => 70, 'marriage' => 45, 'death' => 40],
    '2023' => ['birth' => 75, 'marriage' => 50, 'death' => 45],
    '2024' => ['birth' => 78, 'marriage' => 55, 'death' => 48],
    '2025' => ['birth' => 80, 'marriage' => 60, 'death' => 50]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytical Reports – CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .chart-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .card-box {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
      margin-bottom: 20px;
    }
    .date-range-selector {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .breadcrumb-container {
      margin-bottom: 20px;
    }
    .breadcrumb {
      background: transparent;
      padding: 0;
    }
    .time-period-toggle {
      margin-bottom: 20px;
    }
    .btn-export {
      margin-right: 5px;
    }
  </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="main">
  <div class="content">
    <!-- Breadcrumb and navigation -->
    <div class="row">
      <div class="col-md-6">
        <div class="breadcrumb-container">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href='http://localhost/CCRO-Request/AdminDashboard/includes/home.php'>Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
            </ol>
          </nav>
          <h2>Analytical Reports</h2>
        </div>
      </div>
      <div class="col-md-6 text-end">
        <div class="date-range-selector d-inline-block me-2">
          <span>Date Range:</span>
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            This Month
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">This Week</a></li>
            <li><a class="dropdown-item" href="#">This Month</a></li>
            <li><a class="dropdown-item" href="#">This Year</a></li>
            <li><a class="dropdown-item" href="#">Custom Range</a></li>
          </ul>
        </div>
        <div class="d-inline-block">
          <button class="btn btn-success btn-export">Excel</button>
          <button class="btn btn-danger btn-export">PDF</button>
          <button class="btn btn-secondary btn-export">Print</button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card-box">
          <h4>Overall Requests</h4>
          <h2><?= $overallCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Birth Requests</h4>
          <h2><?= $birthCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Marriage Requests</h4>
          <h2><?= $marriageCount ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card-box">
          <h4>Death Requests</h4>
          <h2><?= $deathCount ?></h2>
        </div>
      </div>
    </div>

    <!-- Time Period Toggle -->
    <div class="time-period-toggle">
      <ul class="nav nav-tabs" id="timeToggle" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="weekly-tab" data-bs-toggle="tab" data-bs-target="#weekly" type="button" role="tab" aria-controls="weekly" aria-selected="true">Weekly</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="monthly" aria-selected="false">Monthly</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab" aria-controls="yearly" aria-selected="false">Yearly</button>
        </li>
      </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="timeToggleContent">
      <!-- Weekly View -->
      <div class="tab-pane fade show active" id="weekly" role="tabpanel" aria-labelledby="weekly-tab">
        <div class="row">
          <!-- Pie Chart -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="weeklyPieChart"></canvas>
            </div>
          </div>
          <!-- Bar Graph -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="weeklyBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Monthly View -->
      <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
        <div class="row">
          <!-- Pie Chart -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="monthlyPieChart"></canvas>
            </div>
          </div>
          <!-- Bar Graph -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="monthlyBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Yearly View -->
      <div class="tab-pane fade" id="yearly" role="tabpanel" aria-labelledby="yearly-tab">
        <div class="row">
          <!-- Pie Chart -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="yearlyPieChart"></canvas>
            </div>
          </div>
          <!-- Bar Graph -->
          <div class="col-md-6">
            <div class="chart-container">
              <canvas id="yearlyBarChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Chart configuration
  const chartColors = {
    birth: 'rgb(76, 175, 80)',    // Green
    marriage: 'rgb(233, 30, 99)',  // Pink
    death: 'rgb(158, 158, 158)'    // Grey
  };

  // Weekly Chart Data
  const weeklyData = <?= json_encode($weeklyData) ?>;
  const weeklyLabels = Object.keys(weeklyData);
  const weeklyBirthData = weeklyLabels.map(week => weeklyData[week]['birth']);
  const weeklyMarriageData = weeklyLabels.map(week => weeklyData[week]['marriage']);
  const weeklyDeathData = weeklyLabels.map(week => weeklyData[week]['death']);
  
  // Monthly Chart Data
  const monthlyData = <?= json_encode($monthlyData) ?>;
  const monthlyLabels = Object.keys(monthlyData);
  const monthlyBirthData = monthlyLabels.map(month => monthlyData[month]['birth']);
  const monthlyMarriageData = monthlyLabels.map(month => monthlyData[month]['marriage']);
  const monthlyDeathData = monthlyLabels.map(month => monthlyData[month]['death']);

  // Yearly Chart Data
  const yearlyData = <?= json_encode($yearlyData) ?>;
  const yearlyLabels = Object.keys(yearlyData);
  const yearlyBirthData = yearlyLabels.map(year => yearlyData[year]['birth']);
  const yearlyMarriageData = yearlyLabels.map(year => yearlyData[year]['marriage']);
  const yearlyDeathData = yearlyLabels.map(year => yearlyData[year]['death']);

  // Function to calculate totals for pie charts
  function calculateTotals(data) {
    let birth = 0, marriage = 0, death = 0;
    Object.values(data).forEach(item => {
      birth += item.birth;
      marriage += item.marriage;
      death += item.death;
    });
    return { birth, marriage, death };
  }

  // Weekly pie chart
  const weeklyTotals = calculateTotals(weeklyData);
  new Chart(document.getElementById('weeklyPieChart'), {
    type: 'pie',
    data: {
      labels: ['Birth', 'Marriage', 'Death'],
      datasets: [{
        data: [weeklyTotals.birth, weeklyTotals.marriage, weeklyTotals.death],
        backgroundColor: [chartColors.birth, chartColors.marriage, chartColors.death]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Weekly Distribution by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Weekly bar chart
  new Chart(document.getElementById('weeklyBarChart'), {
    type: 'bar',
    data: {
      labels: weeklyLabels,
      datasets: [
        {
          label: 'Birth',
          data: weeklyBirthData,
          backgroundColor: chartColors.birth
        },
        {
          label: 'Marriage',
          data: weeklyMarriageData,
          backgroundColor: chartColors.marriage
        },
        {
          label: 'Death',
          data: weeklyDeathData,
          backgroundColor: chartColors.death
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Weekly Requests by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Monthly pie chart
  const monthlyTotals = calculateTotals(monthlyData);
  new Chart(document.getElementById('monthlyPieChart'), {
    type: 'pie',
    data: {
      labels: ['Birth', 'Marriage', 'Death'],
      datasets: [{
        data: [monthlyTotals.birth, monthlyTotals.marriage, monthlyTotals.death],
        backgroundColor: [chartColors.birth, chartColors.marriage, chartColors.death]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Monthly Distribution by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Monthly bar chart
  new Chart(document.getElementById('monthlyBarChart'), {
    type: 'bar',
    data: {
      labels: monthlyLabels,
      datasets: [
        {
          label: 'Birth',
          data: monthlyBirthData,
          backgroundColor: chartColors.birth
        },
        {
          label: 'Marriage',
          data: monthlyMarriageData,
          backgroundColor: chartColors.marriage
        },
        {
          label: 'Death',
          data: monthlyDeathData,
          backgroundColor: chartColors.death
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Monthly Requests by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Yearly pie chart
  const yearlyTotals = calculateTotals(yearlyData);
  new Chart(document.getElementById('yearlyPieChart'), {
    type: 'pie',
    data: {
      labels: ['Birth', 'Marriage', 'Death'],
      datasets: [{
        data: [yearlyTotals.birth, yearlyTotals.marriage, yearlyTotals.death],
        backgroundColor: [chartColors.birth, chartColors.marriage, chartColors.death]
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Yearly Distribution by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Yearly bar chart
  new Chart(document.getElementById('yearlyBarChart'), {
    type: 'bar',
    data: {
      labels: yearlyLabels,
      datasets: [
        {
          label: 'Birth',
          data: yearlyBirthData,
          backgroundColor: chartColors.birth
        },
        {
          label: 'Marriage',
          data: yearlyMarriageData,
          backgroundColor: chartColors.marriage
        },
        {
          label: 'Death',
          data: yearlyDeathData,
          backgroundColor: chartColors.death
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Yearly Requests by Certificate Type'
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>
</body>
</html>
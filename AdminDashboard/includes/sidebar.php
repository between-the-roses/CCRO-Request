<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<!-- sidebar.php -->
<div class="sidebar" id="sidebar">
    <div class="brand">
        <a href="http://localhost/Thesis-/AdminDashboard/includes/home.php" style="text-decoration: none; color: inherit;">
            <h4>Appointment System</h4>
        </a>
    </div>
    
    <nav class="nav flex-column mt-3">
        <div class="nav-item">
            <a class="nav-link <?php if($current == 'home.php') echo 'active'; ?>" href='http://localhost/Thesis-/AdminDashboard/includes/home.php'>
                <i class='bx bx-home'></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?php if($current == 'transactions.php') echo 'active'; ?>" href='http://localhost/Thesis-/AdminDashboard/transactions.php'>
                <i class='bx bx-list-ul'></i>
                <span>View Transactions</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?php if($current == 'report.php') echo 'active'; ?>" href='http://localhost/Thesis-/AdminDashboard/report.php'>
                <i class='bx bx-bar-chart-alt-2'></i>
                <span>Report</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?php if($current == 'settings.php') echo 'active'; ?>" href='http://localhost/Thesis-/AdminDashboard/settings.php'>
                <i class='bx bx-cog'></i>
                <span>Settings</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?php if($current == 'admin.php') echo 'active'; ?>" href='http://localhost/Thesis-/admin.php'>
                <i class='bx bx-log-out'></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
    
    <div class="user-info">
        <div class="user-avatar">
            <span>B</span>
        </div>
        <div class="user-name">Bernadette Marande</div>
        <div class="user-email">bernadette@ccro.gov.ph</div>
    </div>
</div>

<style>
/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    z-index: 1000;
    overflow-y: auto;
    transition: all 0.3s ease;
}

.sidebar.collapsed {
    width: 0px;
}

.sidebar .brand {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar .brand h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.sidebar .nav-item {
    margin: 5px 0;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0 25px 25px 0;
    margin-right: 20px;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: #3498db;
    color: white;
    transform: translateX(5px);
}

.sidebar .nav-link i {
    margin-right: 15px;
    font-size: 18px;
    width: 20px;
    text-align: center;
}

.sidebar .user-info {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.1);
}

.sidebar .user-avatar {
    width: 40px;
    height: 40px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.sidebar .user-name {
    font-size: 14px;
    font-weight: 600;
}

.sidebar .user-email {
    font-size: 12px;
    color: rgba(255,255,255,0.7);
}

/* Hidden state for collapsed sidebar */
.sidebar.collapsed .brand h4,
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .user-info {
    display: none;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    margin-right: 0;
    border-radius: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 60px;
    }
    
    .sidebar .brand h4,
    .sidebar .nav-link span,
    .sidebar .user-info {
        display: none;
    }
    
    .sidebar .nav-link {
        justify-content: center;
        margin-right: 0;
        border-radius: 0;
    }


</style>}}
</style>
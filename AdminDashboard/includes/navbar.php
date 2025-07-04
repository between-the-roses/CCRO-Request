<div class="navbar">
    <!-- Toggle Sidebar Button -->
    <button class="btn-toggle-sidebar" onclick="toggleSidebar()">
        <i id="sidebarToggleIcon" class="bx bx-menu"></i>
    </button>
    
    <!-- Dashboard Title -->
    <div class="dashboard-title">
        <h5>Civil Registry Office Dashboard</h5>
        <small>Admin Panel</small>
    </div>
    
    <!-- Notifications -->
    <div class="navbar-notifications">
        <button class="notification-btn" onclick="toggleNotifications()">
            <i class="bx bx-bell"></i>
            <span class="notification-badge">3</span>
        </button>
        <div id="notificationMenu" class="notification-menu" style="display: none;">
            <div class="notification-header">
                <h6>Notifications</h6>
                <span class="notification-count">3 new</span>
            </div>
            <div class="notification-item">
                <i class="bx bx-info-circle text-info"></i>
                <div>
                    <p>New birth certificate request</p>
                    <small>2 minutes ago</small>
                </div>
            </div>
            <div class="notification-item">
                <i class="bx bx-check-circle text-success"></i>
                <div>
                    <p>Marriage certificate approved</p>
                    <small>10 minutes ago</small>
                </div>
            </div>
            <div class="notification-item">
                <i class="bx bx-error-circle text-warning"></i>
                <div>
                    <p>System maintenance reminder</p>
                    <small>1 hour ago</small>
                </div>
            </div>
            <div class="notification-footer">
                <a href="#">View all notifications</a>
            </div>
        </div>
    </div>
    
    <!-- User Info -->
    <div class="user">
        <div class="user-avatar">
            <i class="bx bx-user-circle"></i>
        </div>
        <div class="user-info">
            <span class="user-name">Administrator</span>
            <small class="user-role">System Admin</small>
        </div>
        <button class="btn-toggle" onclick="toggleUserMenu()">
            <i id="toggleIcon" class="bx bx-chevron-down"></i>
        </button>
        <div id="userMenu" class="user-menu" style="display: none;">
            <div class="user-menu-header">
                <div class="user-avatar-large">
                    <i class="bx bx-user-circle"></i>
                </div>
                <div>
                    <strong>Administrator</strong>
                    <small>admin@ccro.gov.ph</small>
                </div>
            </div>
            <hr class="menu-divider">
            <a href="#" class="menu-item">
                <i class="bx bx-user"></i> Profile
            </a>
            <a href="#" class="menu-item">
                <i class="bx bx-cog"></i> Settings
            </a>
            <a href="#" class="menu-item">
                <i class="bx bx-help-circle"></i> Help
            </a>
            <hr class="menu-divider">
            <a href="#" class="menu-item logout">
                <i class="bx bx-log-out"></i> Logout
            </a>
        </div>
    </div>
</div>

<script>
    // Toggle Sidebar Visibility
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const navbar = document.querySelector('.navbar');
        const content = document.querySelector('.main');
        const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');
        
        if (sidebar && navbar && content && sidebarToggleIcon) {
            if (sidebar.classList.contains('collapsed')) {
                // Expand the sidebar
                sidebar.classList.remove('collapsed');
                navbar.style.left = '250px';
                navbar.style.width = 'calc(100% - 250px)';
                content.style.marginLeft = '250px';
                sidebarToggleIcon.classList.remove('bx-menu-alt-right');
                sidebarToggleIcon.classList.add('bx-menu');
            } else {
                // Collapse the sidebar
                sidebar.classList.add('collapsed');
                navbar.style.left = '0';
                navbar.style.width = '100%';
                content.style.marginLeft = '0';
                sidebarToggleIcon.classList.remove('bx-menu');
                sidebarToggleIcon.classList.add('bx-menu-alt-right');
            }
        } else {
            console.error('One or more elements are missing in the DOM.');
        }
    }

    // Toggle User Menu
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
            toggleIcon.classList.remove('bx-chevron-down');
            toggleIcon.classList.add('bx-chevron-up');
            
            // Close other menus
            closeNotifications();
        } else {
            menu.style.display = 'none';
            toggleIcon.classList.remove('bx-chevron-up');
            toggleIcon.classList.add('bx-chevron-down');
        }
    }

    // Toggle Notifications
    function toggleNotifications() {
        const menu = document.getElementById('notificationMenu');
        
        if (menu.style.display === 'none') {
            menu.style.display = 'block';
            
            // Close other menus
            closeUserMenu();
        } else {
            menu.style.display = 'none';
        }
    }

    // Close user menu
    function closeUserMenu() {
        const menu = document.getElementById('userMenu');
        const toggleIcon = document.getElementById('toggleIcon');
        
        menu.style.display = 'none';
        toggleIcon.classList.remove('bx-chevron-up');
        toggleIcon.classList.add('bx-chevron-down');
    }

    // Close notifications
    function closeNotifications() {
        const menu = document.getElementById('notificationMenu');
        menu.style.display = 'none';
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('userMenu');
        const notificationMenu = document.getElementById('notificationMenu');
        const userButton = event.target.closest('.user');
        const notificationButton = event.target.closest('.navbar-notifications');

        if (!userButton && userMenu.style.display === 'block') {
            closeUserMenu();
        }

        if (!notificationButton && notificationMenu.style.display === 'block') {
            closeNotifications();
        }
    });
</script>

<style>
/* Navbar Styles */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-bottom: none;
    height: 65px;
    position: fixed;
    top: 0;
    left: 250px;
    width: calc(100% - 250px);
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    color: white;
}

.sidebar.collapsed + .navbar {
    left: 0;
    width: 100%;
}

/* Toggle Sidebar Button */
.btn-toggle-sidebar {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 20px;
    color: white;
    padding: 8px 12px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-toggle-sidebar:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.btn-toggle-sidebar:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
}

/* Dashboard Title */
.dashboard-title {
    flex-grow: 1;
    text-align: center;
    margin: 0 20px;
}

.dashboard-title h5 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.dashboard-title small {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    margin-top: 2px;
}

/* Notifications */
.navbar-notifications {
    position: relative;
    margin: 0 15px;
}

.notification-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    color: white;
    padding: 10px;
    position: relative;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.notification-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-menu {
    position: absolute;
    right: 0;
    top: 50px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    width: 320px;
    max-height: 400px;
    overflow-y: auto;
    color: #333;
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
    color: #333;
}

.notification-count {
    background: #007bff;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f8f9fa;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item i {
    font-size: 20px;
    margin-top: 2px;
}

.notification-item p {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: #333;
}

.notification-item small {
    color: #666;
    font-size: 12px;
}

.notification-footer {
    padding: 12px 20px;
    text-align: center;
    border-top: 1px solid #eee;
}

.notification-footer a {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.notification-footer a:hover {
    text-decoration: underline;
}

/* User Section */
.user {
    display: flex;
    align-items: center;
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 12px;
    border-radius: 25px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.user:hover {
    background: rgba(255, 255, 255, 0.2);
}

.user-avatar {
    margin-right: 10px;
}

.user-avatar i {
    font-size: 32px;
    color: white;
}

.user-info {
    margin-right: 10px;
    text-align: left;
}

.user-name {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: white;
    line-height: 1.2;
}

.user-role {
    display: block;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.2;
}

.btn-toggle {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: white;
    padding: 4px;
    transition: all 0.3s ease;
}

.btn-toggle:hover {
    transform: scale(1.1);
}

.btn-toggle:focus {
    outline: none;
}

/* User Menu */
.user-menu {
    position: absolute;
    right: 0;
    top: 55px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 250px;
    color: #333;
    overflow: hidden;
}

.user-menu-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-large i {
    font-size: 40px;
    color: #6c757d;
}

.user-menu-header strong {
    display: block;
    font-size: 16px;
    color: #333;
    margin-bottom: 2px;
}

.user-menu-header small {
    display: block;
    font-size: 12px;
    color: #666;
}

.menu-divider {
    margin: 0;
    border: none;
    height: 1px;
    background: #eee;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
    gap: 12px;
}

.menu-item:hover {
    background: #f8f9fa;
    color: #007bff;
}

.menu-item.logout {
    color: #dc3545;
}

.menu-item.logout:hover {
    background: #fff5f5;
    color: #dc3545;
}

.menu-item i {
    font-size: 16px;
    width: 20px;
}

/* Text Color Utilities */
.text-info { color: #17a2b8; }
.text-success { color: #28a745; }
.text-warning { color: #ffc107; }

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        left: 0;
        width: 100%;
        padding: 0 15px;
    }
    
    .dashboard-title {
        display: none;
    }
    
    .user-info {
        display: none;
    }
    
    .notification-menu,
    .user-menu {
        right: -10px;
        width: 280px;
    }
}

@media (max-width: 480px) {
    
    .notification-menu,
    .user-menu {
        right: -20px;
        width: calc(100vw - 40px);
    }
}
</style>
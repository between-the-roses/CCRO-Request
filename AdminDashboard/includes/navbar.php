<div class="navbar">
    <!-- Toggle Sidebar Button -->
    <button class="btn btn-toggle-sidebar" onclick="toggleSidebar()">
        <i id="sidebarToggleIcon" class="bx bx-menu"></i>
    </button>
    <!-- User Info -->
    <div class="user">
        <i class="bx bx-user-circle me-2"></i> Administrator
        <button class="btn btn-toggle" onclick="toggleUserMenu()">
            <span id="toggleIcon">▼</span>
        </button>
        <div id="userMenu" class="user-menu" style="display: none;">
            <a href="#">Profile</a>
            <a href="#">Settings</a>
            <a href="#">Logout</a>
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
                navbar.style.left = '250px'; // Adjust navbar position
                navbar.style.width = 'calc(100% - 250px)'; // Adjust navbar width
                content.style.marginLeft = '250px'; // Adjust content margin
                sidebarToggleIcon.classList.remove('bx-menu-alt-right');
                sidebarToggleIcon.classList.add('bx-menu');
            } else {
                // Collapse the sidebar
                sidebar.classList.add('collapsed');
                navbar.style.left = '0'; // Reset navbar position
                navbar.style.width = '100%'; // Expand navbar width
                content.style.marginLeft = '0'; // Reset content margin
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
            toggleIcon.textContent = '▲'; // Change to collapse icon
        } else {
            menu.style.display = 'none';
            toggleIcon.textContent = '▼'; // Change to expand icon
        }
    }
</script>

<style>
/* Navbar Styles */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    height: 60px;
    position: fixed;
    top: 0;
    left: 250px; /* Default position when sidebar is expanded */
    width: calc(100% - 250px); /* Adjust width to exclude sidebar */
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: left 0.3s ease, width 0.3s ease; /* Smooth transition for navbar adjustment */
}
.sidebar.collapsed + .navbar {
    left: 0; /* Adjust navbar position when sidebar is collapsed */
    width: 100%; /* Navbar spans full width when sidebar is collapsed */
}
.btn-toggle-sidebar {
background: none;
border: none;
cursor: pointer;
font-size: 24px; /* Adjust icon size */
margin-right: 15px;
}
.btn-toggle-sidebar:focus {
outline: none;
}
.dashboard-title {
flex-grow: 1;
text-align: center;
}
.dashboard-title h5 {
margin: 0;
font-size: 18px;
font-weight: 600;
}
.dashboard-title small {
font-size: 14px;
color: #6c757d;
}
.user {
display: flex;
align-items: center;
position: relative;
}
.user-menu {
position: absolute;
right: 0;
top: 60px; /* Adjust dropdown position to match navbar height */
background-color: #ffffff;
border: 1px solid #dee2e6;
border-radius: 5px;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
z-index: 1000;
min-width: 150px;
}
.user-menu a {
display: block;
padding: 10px 15px;
color: #212529;
text-decoration: none;
font-size: 14px;
}
.user-menu a:hover {
background-color: #f1f1f1;
}
.btn-toggle {
background: none;
border: none;
cursor: pointer;
font-size: 16px;
margin-left: 5px;
}
.btn-toggle:focus {
outline: none;
}
.bx-user-circle {
font-size: 24px;
margin-right: 5px;
}
</style>
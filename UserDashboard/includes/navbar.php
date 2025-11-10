<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\includes\navbar.php

// Function to get the correct path to images
function getImagePath($filename) {
    $current_file = basename($_SERVER['PHP_SELF']);
    $current_dir = dirname($_SERVER['PHP_SELF']);
    
    // If we're in the root directory (index.php)
    if ($current_file == 'index.php' && $current_dir == '/CCRO-Request') {
        return 'images/' . $filename;
    }
    
    // Calculate how many directories deep we are
    $depth = substr_count($current_dir, '/') - 1; // -1 because root CCRO-Request doesn't count
    
    // Build the relative path
    $path = str_repeat('../', $depth) . 'images/' . $filename;
    
    return $path;
}

// Function to get correct path to tracker.php
function getTrackerPath() {
    $current_file = basename($_SERVER['PHP_SELF']);
    $current_dir = dirname($_SERVER['PHP_SELF']);
    
    // If we're in the root directory (index.php)
    if ($current_file == 'index.php' && $current_dir == '/CCRO-Request') {
        return 'UserDashboard/tracker.php';
    }
    
    // Calculate how many directories deep we are from UserDashboard
    $depth = substr_count($current_dir, '/') - 2; // -2 because we want to get to UserDashboard level
    
    // Build the relative path
    if ($depth <= 0) {
        return 'tracker.php'; // We're in UserDashboard or subdirectory
    } else {
        return str_repeat('../', $depth) . 'UserDashboard/tracker.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Navbar Specific Styles */
        :root {
            --primary-blue: #1e40af;
            --secondary-blue: #3b82f6;
            --light-blue: #dbeafe;
            --dark-blue: #1e3a8a;
            --accent-gold: #f59e0b;
            --accent-orange: #ea580c;
            --text-dark: #1a1a1a;
            --text-light: #666666;
            --bg-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            --header-gradient: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(30, 64, 175, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.2);
        }

        .navbar.scrolled {
            background: rgba(30, 64, 175, 0.98);
            backdrop-filter: blur(25px);
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.3);
            padding: 0.5rem 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: white !important;
            font-weight: 700;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            color: rgba(255, 255, 255, 0.9) !important;
            transform: translateY(-1px);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 0.75rem;
        }

        .logo-img {
            height: 40px;
            width: auto;
            filter: brightness(1.1);
            transition: all 0.3s ease;
        }

        .brand-text {
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.2;
        }

        /* Navigation Container - FIXED */
        .navbar-nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.75rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            text-decoration: none;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: 25px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .navbar-nav .nav-link:hover::before {
            opacity: 1;
        }

        .navbar-toggler {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .status-tracker-btn {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: white;
            padding: 0.6rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-left: 1rem;
        }

        .status-tracker-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            color: white;
            text-decoration: none;
        }

        .status-tracker-btn i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        /* Mobile Responsive - FIXED */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(30, 64, 175, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                margin-top: 1rem;
                padding: 1rem;
                box-shadow: 0 8px 32px rgba(30, 64, 175, 0.3);
            }

            .navbar-nav-container {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .navbar-nav {
                flex-direction: column;
                width: 100%;
                text-align: center;
            }

            .nav-link {
                margin: 0.3rem 0;
                text-align: center;
                width: 100%;
            }

            .status-tracker-btn {
                margin: 0;
                display: flex;
                justify-content: center;
                width: fit-content;
            }

            .logo-img {
                height: 35px;
            }

            .brand-text {
                font-size: 1rem;
            }

            .logo-container {
                gap: 0.3rem;
                margin-right: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .logo-img {
                height: 30px;
            }

            .brand-text {
                font-size: 0.9rem;
            }

            .navbar {
                padding: 0.8rem 0;
            }

            .logo-container {
                gap: 0.25rem;
                margin-right: 0.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="http://localhost/CCRO-Request/index.php">
            <div class="logo-container">
                <img src="<?php echo getImagePath('Logo 1.png'); ?>" alt="Logo 1" class="logo-img">
                <img src="<?php echo getImagePath('Logo 2.png'); ?>" alt="Logo 2" class="logo-img">
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div style="width: 25x;">
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav-container">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>

                <!-- Status Tracker Button - Now redirects to tracker.php -->
                <a href="<?php echo getTrackerPath(); ?>" class="status-tracker-btn">
                    <i class="bx bx-search-alt"></i>
                    Track Status
                </a>
            </div>
            </div>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Navbar Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Navbar scroll effect
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('.navbar-nav .nav-link[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start',
                    inline: 'nearest'
                });
            }
        });
    });

    // Status tracker button with loading effect
    const statusBtn = document.querySelector('.status-tracker-btn');
    if (statusBtn) {
        statusBtn.addEventListener('click', function (e) {
            // Add loading state before redirect
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Loading Tracker...';
            this.style.pointerEvents = 'none';
            
            // Small delay to show loading state, then proceed with redirect
            setTimeout(() => {
                window.location.href = this.href;
            }, 300);
        });
    }

    // Navigation active state on scroll
    window.addEventListener('scroll', function () {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 150; // Account for navbar height
            if (pageYOffset >= sectionTop) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        });
    });

    // Enhanced mobile menu animation
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            // Add animation class when opening
            setTimeout(() => {
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.style.animation = 'fadeInDown 0.3s ease-out';
                }
            }, 50);
        });
    }

    console.log('✅ Navbar functionality loaded successfully');
    console.log('📍 Tracker path:', '<?php echo getTrackerPath(); ?>');
});

// Additional utility functions for navbar
function highlightActiveNavItem(activeId) {
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    const activeLink = document.querySelector(`.navbar-nav .nav-link[href="#${activeId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// Export functions for use in other pages
window.navbarUtils = {
    highlightActiveNavItem: highlightActiveNavItem,
    scrollToSection: function(sectionId) {
        const target = document.querySelector(`#${sectionId}`);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    },
    getTrackerPath: function() {
        return '<?php echo getTrackerPath(); ?>';
    }
};
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Civil Registry Online Appointment System</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Enhanced Header/Navbar */
        .navbar {
            background: var(--header-gradient);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.3);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(30, 64, 175, 0.95);
            backdrop-filter: blur(25px);
            box-shadow: 0 12px 40px rgba(30, 64, 175, 0.4);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .logo-container {
            display: flex;
            align-items: center;
            margin-right: 1rem;
        }

        .logo-img {
            height: 45px;
            width: auto;
            margin: 0 5px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.1);
        }

        .brand-text {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            margin-left: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .navbar-nav {
            margin-left: auto;
        }

        .nav-link {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9) !important;
            margin: 0 0.8rem;
            padding: 0.8rem 1.2rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .status-tracker-btn {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
            margin-left: 1rem;
            display: inline-flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .status-tracker-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .status-tracker-btn:hover::before {
            left: 100%;
        }

        .status-tracker-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.4);
            color: white;
            text-decoration: none;
        }

        .status-tracker-btn i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .navbar-toggler {
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(30, 64, 175, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                margin-top: 1rem;
                padding: 1rem;
                box-shadow: 0 8px 32px rgba(30, 64, 175, 0.3);
            }

            .nav-link {
                margin: 0.3rem 0;
                text-align: center;
            }

            .status-tracker-btn {
                margin: 1rem auto 0;
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
        }

        /* Animated Background */
        .hero-bg {
            position: relative;
            background: var(--bg-gradient);
            min-height: 100vh;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:rgba(255,255,255,0.1)"/><stop offset="100%" style="stop-color:rgba(255,255,255,0)"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/><circle cx="900" cy="800" r="80" fill="url(%23a)"/></svg>') no-repeat center center;
            background-size: cover;
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Glass Card Effect */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.2);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.3);
        }

        /* Typography */
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: clamp(2.5rem, 6vw, 4rem);
            line-height: 1.2;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: clamp(1.2rem, 3vw, 1.8rem);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        /* Modern Button */
        .btn-modern {
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-orange));
            color: white;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(245, 158, 11, 0.4);
        }

        /* Feature Cards */
       .feature-card {
    background: white;
    border-radius: 15px;
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: 0 5px 25px rgba(30, 64, 175, 0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(30, 64, 175, 0.1);
    height: 100%;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(30, 64, 175, 0.2);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.8rem;
    color: white;
    box-shadow: 0 8px 25px rgba(30, 64, 175, 0.3);
    flex-shrink: 0;
}

.feature-card h4 {
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0 0 1rem;
    color: var(--text-dark);
    line-height: 1.3;
}

.feature-card p {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--text-light);
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    text-align: center;
}

.row .col-md-4 {
    display: flex;
    margin-bottom: 2rem;
}

.row .col-md-4 .feature-card {
    width: 100%;
}

@media (max-width: 768px) {
    .feature-card {
        padding: 1.5rem;
        min-height: 250px;
        margin-bottom: 1.5rem;
    }
    
    .feature-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .feature-card h4 {
        font-size: 1.2rem;
    }
    
    .feature-card p {
        font-size: 0.95rem;
    }
}

@media (max-width: 576px) {
    .feature-card {
        min-height: 220px;
        padding: 1.2rem;
    }
}
        /* Section Styling */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--text-dark);
            margin-bottom: 3rem;
            position: relative;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border-radius: 2px;
        }

        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.1);
            border: 1px solid rgba(30, 64, 175, 0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(30, 64, 175, 0.05);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: rgba(30, 64, 175, 0.1);
            transform: translateX(5px);
        }

   .contact-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1rem;
    color: white;
    flex-shrink: 0;
}

        

        /* Map Styling */
        .map-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(30, 64, 175, 0.1);
    position: relative;
    height: 100%;
    min-height: 400px;
}

.map-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(30, 64, 175, 0.1), transparent);
    z-index: 1;
    pointer-events: none;
}

.contact-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 15px 40px rgba(30, 64, 175, 0.1);
    border: 1px solid rgba(30, 64, 175, 0.1);
    height: 100%;
    min-height: 400px;
}

        /* Modal Improvements */
        .modal-custom {
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1.5rem 2rem;
            background: rgba(0, 0, 0, 0.02);
            border-radius: 0 0 20px 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.4);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                text-align: center;
            }
            
            .glass-card {
                padding: 1.5rem;
            }
            
            .feature-card {
                margin-bottom: 2rem;
            }
        }

        /* Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            font-size: 2rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            40% {
                transform: translateX(-50%) translateY(-10px);
            }
            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }
    </style>
</head>

<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#" id="navbarBrand">
                <div class="logo-container">
                    <img src="./images/Logo 1.png" alt="Logo 1" class="logo-img">
                    <img src="./images/Logo 2.png" alt="Logo 2" class="logo-img">
                </div>
                
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="status-tracker-btn" href="#status">
                            <i class="bx bx-search-alt"></i>
                            Status Tracker
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg" style="padding-top: 120px;">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 fade-in-up">
                    <div class="glass-card">
                        <h1 class="hero-title">Iligan City Civil Registry</h1>
                        <h2 class="hero-subtitle">Online Appointment System</h2>
                        <p class="hero-description">
                            Experience seamless civil registry services with our modern online platform. 
                            Schedule appointments, track requests, and manage your documents with ease—all 
                            from the comfort of your home.
                        </p>
                        <button class="btn btn-modern" id="startButton">
                            Get Started
                            <i class="bx bx-right-arrow-alt ms-2"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="images/pic.png" alt="Registry Illustration" class="img-fluid" style="max-width: 80%; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.2));">
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="bx bx-chevron-down"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container">
        <h2 class="section-title">Why Choose Our Service?</h2>
        <div class="row">
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-card w-100">
                    <div class="feature-icon">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <h4>Fast & Efficient</h4>
                    <p>Quick processing times and streamlined procedures for all your civil registry needs.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-card w-100">
                    <div class="feature-icon">
                        <i class="bx bx-lock-alt"></i>
                    </div>
                    <h4>Secure & Reliable</h4>
                    <p>Your personal information is protected with state-of-the-art security measures.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-card w-100">
                    <div class="feature-icon">
                        <i class="bx bx-mobile-alt"></i>
                    </div>
                    <h4>24/7 Accessibility</h4>
                    <p>Access our services anytime, anywhere with our mobile-friendly platform.</p>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- About Section -->
    <section id="about" class="py-5" style="background: white;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title text-start">About LGU Iligan</h2>
                    <h3 style="color: var(--primary-blue); margin-bottom: 1.5rem;">City Civil Registrar's Office</h3>
                    <p class="lead" style="color: var(--text-light); margin-bottom: 2rem;">
                        Civil Registration is a fundamental right and an essential aspect of our daily lives. 
                        Every individual deserves to be registered, recognized, and counted.
                    </p>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle text-success me-3" style="font-size: 1.5rem;"></i>
                                <span>Birth Registration</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle text-success me-3" style="font-size: 1.5rem;"></i>
                                <span>Marriage Registration</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle text-success me-3" style="font-size: 1.5rem;"></i>
                                <span>Death Registration</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-circle text-success me-3" style="font-size: 1.5rem;"></i>
                                <span>Document Certification</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <div style="background: linear-gradient(135deg, var(--light-blue), rgba(77, 148, 255, 0.1)); border-radius: 20px; padding: 2rem; margin: 2rem 0;">
                            <i class="bx bx-building" style="font-size: 5rem; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                            <h4 style="color: var(--primary-blue);">Serving the Community</h4>
                            <p style="color: var(--text-light); margin: 0;">Dedicated to providing excellent civil registry services to the people of Iligan City.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
    <div class="container">
        <h2 class="section-title">Get in Touch</h2>
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-card">
                    <h3 style="color: var(--primary-blue); margin-bottom: 2rem;">Contact Information</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bx bxs-map"></i>
                        </div>
                        <div>
                            <h5>Address</h5>
                            <p class="mb-0">Iligan City Hall, Quezon Avenue Extension, Palao, Buhanginan Hills, Iligan City, 9200, Lanao del Norte</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bx bxs-phone"></i>
                        </div>
                        <div>
                            <h5>Phone</h5>
                            <p class="mb-0">0927 074 6624</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bx bxs-envelope"></i>
                        </div>
                        <div>
                            <h5>Email</h5>
                            <p class="mb-0">civilregistrar.iligan@gmail.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bx bxs-time"></i>
                        </div>
                        <div>
                            <h5>Office Hours</h5>
                            <p class="mb-0">Monday - Friday: 8:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="map-container">
                    <iframe
                        src="https://maps.google.com/maps?q=8.226083,124.251917&hl=en&z=16&amp;output=embed"
                        width="100%"
                        height="100%"
                        style="border: 0; min-height: 400px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>
                
<!-- Important Reminders Modal (Replaced Get Started Modal) -->
<div
  class="modal fade"
  id="reminderModal"
  tabindex="-1"
  aria-labelledby="reminderModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <i class="bx bxs-error-circle text-danger fs-3 me-2"></i>
        <h5 class="modal-title fw-bold" id="reminderModalLabel">
          Important Reminders
        </h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body text-start">
        <h5 class="fw-bold">Welcome to the Iligan City Civil Registrar Online Document Request System!</h5>
        <p>The City Civil Registrar’s Office (CCRO) of Iligan City respects your privacy. When using this system, your personal and sensitive information—such as your name, contact number, and civil registry details—is collected only for the purpose of processing your request for:</p>
        <ul>
          <li>Birth Certificate</li>
          <li>Marriage Certificate</li>
          <li>Death Certificate</li>
        </ul>

        <p><strong>🔍 Important Reminders</strong></p>
        <ul>
          <li>Minors are not allowed to book an appointment.</li>
          <li>If you are an authorized representative, you must upload:
            <ul>
              <li>A copy of the Authorization Letter signed by the document owner</li>
              <li>A copy of your valid ID</li>
              <li>A copy of the document owner's valid ID</li>
            </ul>
          </li>
          <li>Please ensure all information provided is complete and accurate to avoid delays.</li>
          <li>Appointment slots are limited and are processed on a first-come, first-served basis.</li>
        </ul>

        <p><strong>👥 Who Can Request Documents?</strong></p>
        <ul>
          <li>The document owner</li>
          <li>Spouse of the document owner</li>
          <li>Daughter or Son</li>
          <li>An Authorized Representative</li>
        </ul>

        <p><strong>🔐 Why We Collect Your Information</strong></p>
        <ul>
          <li>Verify the records in our office</li>
          <li>Confirm your identity as the requester</li>
          <li>Process and deliver your requested document</li>
          <li>Generate non-personal statistical reports to improve our services</li>
        </ul>

        <p><strong>🛡 How We Protect Your Information</strong></p>
        <p>Your data is handled confidentially and is only accessible by trained and authorized CCRO staff. We implement security measures in compliance with the Data Privacy Act of 2012 (RA 10173).</p>

        <p><strong>📄 Your Rights</strong></p>
        <ul>
          <li>Be informed about how your data is handled</li>
          <li>Access or request correction of your personal data</li>
          <li>Withdraw your consent at any time</li>
          <li>File a complaint in case of misuse or mishandling</li>
        </ul>

        <p>For concerns or questions, you may contact our Data Privacy Officer:<br>
        📧 Email: <a href="mailto:ccro@iligan.gov.ph">ccro@iligan.gov.ph</a><br>
        📞 Contact: (063) 221-4308 / 0956-932-2306</p>

        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="agreeCheckbox" />
          <label class="form-check-label" for="agreeCheckbox">
            By clicking “I Agree”, you confirm that you have read and understood this notice, and consent to the collection and use of your information as stated.
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="nextButton" class="btn btn-primary" disabled>
          Proceed
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Footer (unchanged) -->
<footer class="py-4" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); color: white;">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-0">&copy; 2024 Iligan City Civil Registry. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <!-- Additional footer content -->
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main Script -->
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

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Open Reminder Modal instead of Get Started
    const startButton = document.getElementById('startButton');
    if (startButton) {
      startButton.addEventListener('click', function () {
        const reminderModal = new bootstrap.Modal(document.getElementById('reminderModal'));
        reminderModal.show();
      });
    }

    // Status tracker modal
    const statusBtn = document.querySelector('.status-tracker-btn');
    if (statusBtn) {
      statusBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
      });
    }

    // Track button logic
    const trackButton = document.getElementById('trackButton');
    if (trackButton) {
      trackButton.addEventListener('click', function () {
        const referenceNumber = document.getElementById('referenceNumber').value;
        const lastName = document.getElementById('lastName').value;
        if (referenceNumber && lastName) {
          document.getElementById('statusResult').style.display = 'block';
        } else {
          alert('Please fill in all required fields.');
        }
      });
    }

    // ✅ FIXED: Enable Proceed button when checkbox is checked
    const agreeCheckbox = document.getElementById('agreeCheckbox');
    const nextButton = document.getElementById('nextButton');

    if (agreeCheckbox && nextButton) {
      agreeCheckbox.addEventListener('change', function () {
        nextButton.disabled = !this.checked;
      });

      nextButton.addEventListener('click', function () {
        const reminderModal = bootstrap.Modal.getInstance(document.getElementById('reminderModal'));
        reminderModal.hide();
        window.location.href = 'UserDashboard/certificatetype.php';
      });
    }

    // Scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in-up');
        }
      });
    }, observerOptions);

    document.querySelectorAll('.feature-card, .contact-card').forEach(el => {
      observer.observe(el);
    });

    // Navigation active state on scroll
    window.addEventListener('scroll', function () {
      const sections = document.querySelectorAll('section[id]');
      const navLinks = document.querySelectorAll('.nav-link');
      let current = '';
      sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
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
  });
</script>

</body>
</html>

    

   
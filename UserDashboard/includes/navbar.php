<style>
header {
    background-color: #fff;
    box-shadow: 0 4px 16px rgba(14, 55, 54, 0.10);
    padding: 14px 30px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    min-height: 40px;
    display: flex;
    align-items: center;
}

.logo-img {
    height: 48px;
    width: auto;
    margin-right: 10px;
    transition: transform 0.2s;
}
.logo-img:last-child {
    margin-right: 0;
}
.logo-img:hover {
    transform: scale(1.07);
}

/* Remove underline by default */
header a {
    text-decoration: none !important;
}

/* Show underline only when link is active or focused */
header a:active,
header a:focus {
    text-decoration: underline !important;
}

@media (max-width: 600px) {
    header {
        flex-direction: column;
        padding: 10px 10px;
        min-height: unset;
    }
    .logo-img {
        height: 36px;
        margin-right: 6px;
    }
    header .btn-info {
        margin-top: 10px;
        width: 100%;
    }
    content {
        margin-bottom: 100px; /* Adjust for fixed header */
    }
}
</style>

<!-- Navbar -->
<header class="d-flex justify-content-between align-items-center">
    <a href="http://localhost/Thesis-/index.php" class="d-flex align-items-center gap-0" style="text-decoration: none;" aria-label="Go to homepage">
        <img src="/Thesis-/images/Logo 2.png" alt="Logo 2" class="logo-img" />
        <img src="/Thesis-/images/Logo 1.png" alt="Logo 1" class="logo-img" />
    </a>
<!-- Navbar links/buttons with gap -->
    <div class="d-flex align-items-center" style="gap: 25px;">
        <a href="http://localhost/Thesis-/index.php" class="btn-info" style="font-size: 1.1rem; color: black;">Home</a>
        <a href="http://localhost/Thesis-/index.php#about" class="btn-info" style="font-size: 1.1rem; color: black;">About</a>
        <a href="http://localhost/Thesis-/index.php#contact" class="btn-info" style="font-size: 1.1rem; color: black;">Contact</a>
        <a href="http://localhost/Thesis-/UserDashboard/verification.php" class="btn btn-info" style="font-size: 1.1rem; padding: 10px 20px; font-weight: 600; color: black; text-decoration: none;">Status Tracker</a>
    </div>
</header>

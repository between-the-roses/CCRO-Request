<style>
header {
    background-color: #fff;
    box-shadow: 0 4px 16px rgba(14, 55, 54, 0.10);
    padding: 14px 40px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    min-height: 70px;
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

header .btn-info {
    background-color: #17c1e8;
    border: none;
    font-weight: 600;
    padding: 8px 22px;
    border-radius: 8px;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(23, 193, 232, 0.08);
}
header .btn-info:hover, header .btn-info:focus {
    background-color: #139ab6;
    color: #fff;
    box-shadow: 0 4px 16px rgba(23, 193, 232, 0.15);
    text-decoration: none;
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
}
</style>

<!-- Navbar -->
<header class="d-flex justify-content-between align-items-center">
    <a href="http://localhost/Thesis-/index.php" class="d-flex align-items-center gap-0" style="text-decoration: none;" aria-label="Go to homepage">
        <img src="/Thesis-/images/Logo 2.png" alt="Logo 2" class="logo-img" />
        <img src="/Thesis-/images/Logo 1.png" alt="Logo 1" class="logo-img" />
    </a>
    <a href="http://localhost/Thesis-/admin.php" class="btn btn-info text-white">Admin Panel</a>
</header>

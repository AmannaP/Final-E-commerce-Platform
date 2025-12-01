<?php
// 1. Session & Cart Logic
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get cart count safely
$cart_count = 0;
// Check file path based on where this file is included from
$controller_path = dirname(__DIR__) . '/controllers/cart_controller.php';
if (file_exists($controller_path)) {
    require_once($controller_path);
    
    // Use ID if logged in, otherwise IP
    $uid = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    
    // Fetch items
    $c_items = get_user_cart_ctr($uid ?? $ip_addr);
    if ($c_items) {
        $cart_count = count($c_items);
    }
}

// User State
$is_logged_in = isset($_SESSION['id']);
$customer_name = $is_logged_in ? ($_SESSION['name'] ?? 'User') : 'Guest';
?>

<style>
    /* Navbar Theme: Purple background with White text */
    .navbar-custom {
        background-color: #c453eaff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 12px 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .brand-logo {
        color: white !important;
        font-weight: 800;
        font-size: 24px;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .brand-logo:hover {
        opacity: 0.9;
    }

    /* Links styling */
    .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 600;
        font-size: 15px;
        margin: 0 8px;
        transition: all 0.3s;
    }
    
    .nav-link:hover {
        color: white !important;
        transform: translateY(-1px);
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .nav-link.active {
        color: white !important;
        font-weight: 700;
    }
    
    /* Dropdown Styling */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border-radius: 12px;
        margin-top: 10px;
    }
    
    .dropdown-item {
        padding: 8px 20px;
        font-weight: 500;
    }

    .dropdown-item:hover {
        background-color: #f3e8ff; /* Light Purple hover */
        color: #c453eaff;
    }
    
    /* Cart Badge - White on Purple bg needs to be inverse or Red */
    .badge-notification {
        background-color: white;
        color: #c453eaff;
        font-size: 0.7rem;
        font-weight: 800;
    }
    
    /* Auth Buttons */
    .btn-auth {
        border-radius: 50px;
        padding: 6px 20px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .btn-login {
        background-color: white;
        color: #c453eaff;
        border: 2px solid white;
    }
    .btn-login:hover {
        background-color: transparent;
        color: white;
    }
    
    .btn-register {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }
    .btn-register:hover {
        background-color: white;
        color: #c453eaff;
        border-color: white;
    }
    
    /* Mobile Toggler */
    .navbar-toggler {
        border-color: rgba(255,255,255,0.5);
    }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand brand-logo" href="../user/product_page.php">
            <i class="bi bi-shop-window me-2"></i>GBVAid
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <a class="nav-link" href="../user/dashboard.php">Home</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="../user/product_page.php">Services</a>
                </li>

                <li class="nav-item position-relative">
                    <a class="nav-link" href="../views/cart.php">
                        <i class="bi bi-cart3" style="font-size: 1.2rem;"></i> Bookings
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-notification">
                                <?= $cart_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item d-none d-lg-block mx-2">
                    <div style="border-left: 2px solid rgba(255,255,255,0.3); height: 25px;"></div>
                </li>

                <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div style="width: 30px; height: 30px; background: white; color: #c453eaff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <?= htmlspecialchars($customer_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="../user/profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="../user/my_appointments.php"><i class="bi bi-box-seam me-2"></i>My Appointments</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../login/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-2">
                        <a class="btn btn-auth btn-login" href="../login/login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-auth btn-register" href="../login/register.php">Register</a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>
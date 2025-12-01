<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';

// 1. Set Timezone to Ghana
date_default_timezone_set('Africa/Accra');

requireLogin('../login/login.php');

$customer_id = getUserId();
$invoice_no = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : '';
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Purple Background */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #c453eaff; 
            min-height: 100vh;
        }
        
        /* Navbar */
        .navbar { 
            background: white; 
            padding: 15px 0; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }
        .navbar-brand { 
            font-weight: bold;
            font-size: 24px; 
            color: #c453eaff !important; 
        }
        .nav-link {
            color: #555;
            font-weight: 600;
            text-decoration: none;
        }
        
        /* Success Box */
        .success-box { 
            background: white; 
            border-radius: 20px; 
            padding: 50px 40px; 
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-top: 60px;
        }
        
        .success-icon { 
            font-size: 80px; 
            margin-bottom: 20px; 
            animation: bounce 1s ease-in-out;
            color: #10b981; /* Green for success */
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .confirmation-message { 
            background: #d1fae5; 
            border: 1px solid #10b981; 
            padding: 20px; 
            border-radius: 12px; 
            color: #065f46;
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .booking-details { 
            background: #f8f9fa; 
            padding: 30px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            text-align: left;
            border: 1px solid #eee;
        }
        
        .detail-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 0; 
            border-bottom: 1px solid #eee;
            color: #555;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; }
        
        /* Buttons */
        .btn-purple { 
            background-color: #c453eaff; 
            color: white; 
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            border: 2px solid #c453eaff;
            transition: all 0.3s;
        }
        .btn-purple:hover { 
            background-color: white; 
            color: #c453eaff; 
        }
        
        .btn-secondary-custom { 
            background: white; 
            color: #555; 
            border: 2px solid #eee; 
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-secondary-custom:hover { 
            border-color: #c453eaff;
            color: #c453eaff;
        }
        
        .buttons-container { 
            display: flex; 
            justify-content: center; 
            gap: 15px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <nav class="navbar fixed-top">
        <div class="container">
            <a href="../index.php" class="navbar-brand">GBVAid</a>
            <a href="../user/product_page.php" class="nav-link">← Return to Services</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="success-box">
                    <div class="success-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <h2 class="fw-bold mb-3">Booking Confirmed!</h2>
                    <p class="text-muted mb-4">Your session has been scheduled successfully.</p>
                    
                    <div class="confirmation-message">
                        <strong><i class="bi bi-shield-check me-2"></i> Payment Secured</strong><br>
                        Thank you. Your support session details have been sent to your email.
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-row">
                            <span class="detail-label">Invoice Number</span>
                            <span class="fw-bold" style="color: #c453eaff;"><?php echo $invoice_no; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Reference</span>
                            <span><?php echo $reference; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date Processed</span>
                            <span><?php echo date('F j, Y - h:i A'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="text-success fw-bold">Active <i class="bi bi-check-lg"></i></span>
                        </div>
                    </div>
                    
                    <div class="buttons-container">
                        <a href="../user/my_appointments.php" class="btn-purple">
                            <i class="bi bi-calendar-event me-2"></i>View Appointments
                        </a>
                        <a href="../user/product_page.php" class="btn-secondary-custom">
                            Find More Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Confetti effect
        function createConfetti() {
            const colors = ['#c453eaff', '#a020f0', '#ffffff', '#ffd700', '#10b981'];
            const confettiCount = 60;
            
            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    const color = colors[Math.floor(Math.random() * colors.length)];
                    
                    confetti.style.cssText = `
                        position: fixed;
                        width: 10px;
                        height: 10px;
                        background: ${color};
                        left: ${Math.random() * 100}%;
                        top: -10px;
                        opacity: 1;
                        transform: rotate(${Math.random() * 360}deg);
                        z-index: 10001;
                        pointer-events: none;
                        border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                    `;
                    
                    document.body.appendChild(confetti);
                    
                    const duration = 2000 + Math.random() * 1000;
                    const startTime = Date.now();
                    
                    function animateConfetti() {
                        const elapsed = Date.now() - startTime;
                        const progress = elapsed / duration;
                        
                        if (progress < 1) {
                            const top = progress * (window.innerHeight + 50);
                            const wobble = Math.sin(progress * 10) * 30;
                            confetti.style.top = top + 'px';
                            confetti.style.left = `calc(${parseFloat(confetti.style.left)}% + ${wobble}px)`;
                            confetti.style.opacity = 1 - progress;
                            confetti.style.transform = `rotate(${progress * 720}deg)`;
                            requestAnimationFrame(animateConfetti);
                        } else {
                            confetti.remove();
                        }
                    }
                    animateConfetti();
                }, i * 30);
            }
        }
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
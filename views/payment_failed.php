<?php
session_start();

// Get error message from URL parameter
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'Payment processing failed. Please try again.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Failed | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Purple Background */
        body {
            background-color: #c453eaff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            color: #c453eaff !important;
        }

        /* Container Card */
        .failed-container {
            background-color: white;
            border-radius: 15px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-top: 60px;
            text-align: center;
        }

        .failed-icon {
            font-size: 80px;
            color: #dc3545;
            animation: shake 0.5s;
            margin-bottom: 20px;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .btn-purple {
            background-color: #c453eaff;
            color: white;
            border: 2px solid #c453eaff;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-purple:hover {
            background-color: white;
            color: #c453eaff;
        }

        .btn-outline-custom {
            border: 2px solid #ddd;
            color: #555;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-outline-custom:hover {
            border-color: #c453eaff;
            color: #c453eaff;
            background-color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">GBVAid</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="failed-container">
                <i class="bi bi-x-circle-fill failed-icon"></i>
                <h2 class="fw-bold mb-3 text-danger">Booking Failed</h2>
                <p class="text-muted mb-4">We couldn't secure your booking slot at this time.</p>

                <div class="alert alert-danger mb-4 text-start">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error:</strong> <?= $error_message ?>
                </div>

                <div class="bg-light p-4 rounded mb-4 text-start">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Possible Reasons:</h6>
                    <ul class="mb-0 text-muted small ps-3">
                        <li>Payment declined or insufficient funds</li>
                        <li>Network interruption during processing</li>
                        <li>The session slot might have been taken</li>
                        <li>Payment gateway timeout</li>
                    </ul>
                </div>

                <div class="d-grid gap-3 d-sm-flex justify-content-center">
                    <a href="checkout.php" class="btn-purple">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </a>
                    <a href="cart.php" class="btn-outline-custom">
                        <i class="bi bi-list-check me-2"></i>Back to Booking List
                    </a>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small">
                        Need help? <a href="../user/contact.php" style="color: #c453eaff; font-weight: bold;">Contact Support</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
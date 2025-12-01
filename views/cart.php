<?php
session_start();
require_once "../controllers/cart_controller.php";

// Determine user identity
$customer_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
$ip_add = $_SERVER['REMOTE_ADDR'];

// Fetch cart items
$cart_items = get_user_cart_ctr($customer_id ?? $ip_add);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking List | GBVAid</title>     
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="../js/cart.js"></script>

    <style>
        /* 1. Purple Background */
        body {
            background-color: #c453eaff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* 2. Navbar Styling */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 24px;
            color: #c453eaff !important;
        }
        .navbar-text {
            font-weight: 600;
            color: #555;
            border-left: 2px solid #ddd;
            padding-left: 15px;
            margin-left: 15px;
        }

        /* 3. White Card Container */
        .cart-container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 50px;
            margin-bottom: 50px;
        }

        /* 4. Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .table thead th {
            border: none;
            border-bottom: 2px solid #e598ffff;
            color: #c453eaff;
            font-weight: 700;
            padding: 15px;
        }
        .table tbody tr {
            vertical-align: middle;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .table tbody td {
            border: none;
            padding: 15px;
            background-color: #fcfcfc;
        }
        .table tbody tr td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .table tbody tr td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* 5. Button Styles */
        .btn-purple {
            background-color: #c453eaff;
            color: white;
            border: 2px solid #c453eaff;
            font-weight: 600;
            padding: 10px 25px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-purple:hover {
            background-color: #a020f0;
            color: white;
            border-color: #a020f0;
        }

        .btn-outline-custom {
            color: #555;
            border: 2px solid #eee;
            background: white;
            font-weight: 600;
        }
        .btn-outline-custom:hover {
            border-color: #c453eaff;
            color: #c453eaff;
        }

        .btn-outline-danger-custom {
            color: #dc3545;
            border: 2px solid #fee2e2;
            background: #fff5f5;
            font-weight: 600;
        }
        .btn-outline-danger-custom:hover {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .qty-input {
            width: 70px;
            text-align: center;
            border: 1px solid #e598ffff;
            border-radius: 8px;
            padding: 5px;
        }
        
        .product-img {
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="../user/product_page.php">GBVAid</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text">Your Booking List</span>
        </div>
    </div>
</nav>

<div class="container">
    <div class="cart-container">
        
        <?php if (empty($cart_items)) : ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x" style="font-size: 80px; color: #e598ffff;"></i>
                <h4 class="mt-4 text-muted">No services selected</h4>
                <p class="text-muted mb-4">You haven't selected any support services yet.</p>
                <a href="../user/product_page.php" class="btn btn-purple">
                    Browse Services
                </a>
            </div>
        <?php else : ?>
            
            <h4 class="mb-4" style="color: #555;">Selected Services</h4>

            <div class="table-responsive">
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Cost</th>
                            <th>Sessions</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $total_amount = 0;
                        foreach ($cart_items as $item):
                            $subtotal = $item['product_price'] * $item['qty'];
                            $total_amount += $subtotal;
                        ?>

                        <tr id="cart-row-<?= $item['p_id'] ?>">
                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                    <img src="../uploads/products/<?= htmlspecialchars($item['product_image']) ?>"
                                         width="60" height="60" class="product-img me-3">
                                    <div>
                                        <span class="fw-bold text-dark d-block"><?= htmlspecialchars($item['product_title']) ?></span>
                                        <?php if (!empty($item['booking_date'])): ?>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event me-1"></i><?= $item['booking_date'] ?>
                                                <i class="bi bi-clock ms-1 me-1"></i><?= $item['booking_time'] ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <td>GH₵ <?= number_format($item['product_price'], 2) ?></td>

                            <td>
                                <input type="number"
                                       min="1"
                                       value="<?= $item['qty'] ?>"
                                       class="qty-input"
                                       onchange="updateCartQty(<?= $item['p_id'] ?>, this.value)">
                            </td>

                            <td class="fw-bold" style="color: #c453eaff;">
                                GH₵ <?= number_format($subtotal, 2) ?>
                            </td>

                            <td>
                                <button class="btn btn-sm text-danger"
                                        title="Remove Service"
                                        onclick="removeFromCart(<?= $item['p_id'] ?>)">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </button>
                            </td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 mb-4 align-items-center">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-end">
                    <div class="p-3 bg-light rounded d-inline-block border" style="min-width: 250px">
                        <span class="text-muted">Total Cost:</span>
                        <h3 class="mb-0 fw-bold" style="color: #c453eaff;">
                            GH₵ <?= number_format($total_amount, 2) ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <button class="btn btn-outline-custom"
                        onclick="window.location.href='../user/product_page.php'">
                    <i class="bi bi-arrow-left me-1"></i> Add More Services
                </button>

                <div>
                    <button class="btn btn-outline-danger-custom me-2"
                            onclick="emptyCart()">
                        Clear List
                    </button>

                    <?php if (isset($_SESSION['id'])): ?>
                        <button class="btn btn-purple"
                                onclick="window.location.href='checkout.php'">
                            Confirm Bookings <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-purple"
                                onclick="window.location.href='../login/login.php?redirect=cart'">
                            Login to Book <i class="bi bi-box-arrow-in-right ms-1"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

</body>
</html>
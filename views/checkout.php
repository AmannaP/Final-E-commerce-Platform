<?php
require_once '../settings/core.php';
requireLogin('../login/login.php');

// Check if cart is not empty
require_once '../controllers/cart_controller.php';
$customer_id = getUserId();
$cart_items = get_user_cart_ctr($customer_id);

if (!$cart_items || count($cart_items) == 0) {
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Booking - GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* 1. Purple Background */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #c453eaff; 
            min-height: 100vh;
        }
        
        /* 2. Navbar Styling (White) */
        .navbar { 
            background: white; 
            padding: 15px 0; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        }
        .logo { 
            font-family: 'Segoe UI', serif; 
            font-weight: bold;
            font-size: 26px; 
            color: #c453eaff; 
            text-decoration: none; 
        }
        .nav-link-custom {
            color: #555;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .nav-link-custom:hover {
            color: #c453eaff;
        }
        
        /* 3. Checkout Container (White Card) */
        .checkout-card { 
            background: white; 
            border-radius: 15px; 
            padding: 40px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); 
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .page-title {
            color: white;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .section-header {
            border-bottom: 2px solid #e598ffff;
            padding-bottom: 15px;
            margin-bottom: 25px;
            color: #333;
            font-weight: 700;
        }
        
        /* 4. Booking Summary Styles */
        .checkout-item {
            transition: background-color 0.2s;
        }
        .checkout-item:hover {
            background-color: #f9f9f9;
        }
        
        .summary-total-container { 
            background-color: #f3e8ff; /* Light purple bg */
            border: 1px solid #e598ffff;
            border-radius: 10px;
            padding: 20px;
            text-align: right;
            margin: 30px 0; 
        }
        
        .total-label {
            font-size: 16px;
            color: #555;
        }
        
        .total-amount {
            font-size: 32px; 
            font-weight: 800; 
            color: #c453eaff; 
        }
        
        /* 5. Buttons */
        .btn-purple { 
            background-color: #c453eaff; 
            color: white; 
            border: 2px solid #c453eaff; 
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-purple:hover { 
            background-color: white; 
            color: #c453eaff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-secondary-custom {
            background: white;
            color: #555;
            border: 2px solid #eee;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-secondary-custom:hover {
            border-color: #c453eaff;
            color: #c453eaff;
        }
        
        /* 6. Modals */
        .modal { 
            background: rgba(0,0,0,0.6); 
            backdrop-filter: blur(5px);
        }
        
        .modal-content { 
            border-radius: 20px; 
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
        }
        
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .secure-badge {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <nav class="navbar fixed-top">
        <div class="container">
            <a href="../index.php" class="logo">GBVAid</a>
            <a href="cart.php" class="nav-link-custom">
                <i class="bi bi-arrow-left me-1"></i> Back to Booking List
            </a>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <div class="mb-4">
            <h2 class="page-title"><i class="bi bi-calendar-check me-2"></i>Finalize Booking</h2>
            <p style="color: rgba(255,255,255,0.8);">Review your selected services and confirm details</p>
        </div>

        <div class="checkout-card">
            <h4 class="section-header">Booking Summary</h4>
            
            <div id="checkoutItemsContainer">
                <div class="text-center py-4">
                    <div class="spinner-border text-purple" role="status" style="color: #c453eaff;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            
            <div class="summary-total-container">
                <span class="total-label">Total Cost:</span><br>
                <span id="checkoutTotal" class="total-amount">GH₵ 0.00</span>
            </div>
            
            <button onclick="showPaymentModal()" class="btn btn-purple btn-lg">
                <i class="bi bi-shield-lock-fill me-2"></i> Confirm & Secure Booking
            </button>
        </div>
    </div>

    <div id="paymentModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Secure Booking</h5>
                    <button type="button" class="btn-close" onclick="closePaymentModal()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <small class="text-muted">TOTAL COST</small>
                        <div id="paymentAmount" style="font-size: 36px; font-weight: 800; color: #c453eaff;"></div>
                    </div>
                    
                    <div class="secure-badge">
                        <div class="fw-bold mb-1"><i class="bi bi-shield-lock-fill text-success me-1"></i> SSL SECURED PAYMENT</div>
                        <small class="text-muted">Powered by Paystack. Your details are encrypted.</small>
                    </div>
                    
                    <p class="text-center text-muted small mb-4">
                        You will be redirected to Paystack to secure your session.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <button onclick="processCheckout()" id="confirmPaymentBtn" class="btn btn-purple">
                            Confirm Payment
                        </button>
                        <button onclick="closePaymentModal()" class="btn btn-light text-muted">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-5 text-center">
                    <div style="font-size: 60px; color: #10b981; margin-bottom: 20px;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Booking Confirmed!</h2>
                    
                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Invoice:</span>
                            <span id="successInvoice" class="fw-bold text-dark"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Amount:</span>
                            <span id="successAmount" class="fw-bold text-success"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Date:</span>
                            <span id="successDate"></span>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-4">Thank you! Your support session has been scheduled successfully.</p>
                    
                    <div class="d-flex gap-2 justify-content-center">
                        <button onclick="continueShopping()" class="btn btn-secondary-custom">Find Services</button>
                        <button onclick="viewOrders()" class="btn btn-purple" style="width: auto;">View Appointments</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../js/checkout.js"></script>
    
    <script>
        // Store original functions
        const originalShowPayment = window.showPaymentModal;
        const originalClosePayment = window.closePaymentModal;
        const originalShowSuccess = window.showSuccessModal;

        // Bootstrap Modal Instances
        let paymentModalBS;
        let successModalBS;

        window.addEventListener('DOMContentLoaded', () => {
            paymentModalBS = new bootstrap.Modal(document.getElementById('paymentModal'));
            successModalBS = new bootstrap.Modal(document.getElementById('successModal'));
        });

        // Override to use Bootstrap
        window.showPaymentModal = function() {
            // Update amount text first (logic from original JS)
            const amountDisplay = document.getElementById('paymentAmount');
            if (amountDisplay) {
                amountDisplay.textContent = `GH₵ ${window.checkoutTotal || '0.00'}`;
            }
            paymentModalBS.show();
        };

        window.closePaymentModal = function() {
            paymentModalBS.hide();
        };

        window.showSuccessModal = function(orderData) {
            // Populate data (logic from original JS)
            const invoiceEl = document.getElementById('successInvoice');
            const amountEl = document.getElementById('successAmount');
            const dateEl = document.getElementById('successDate');
            
            if (invoiceEl) invoiceEl.textContent = orderData.invoice_no || 'N/A';
            if (amountEl) amountEl.textContent = `GH₵ ${orderData.total_amount || '0.00'}`;
            if (dateEl) dateEl.textContent = orderData.order_date || new Date().toLocaleString();
            
            successModalBS.show();
            
            // Trigger confetti if function exists
            if (typeof createConfetti === 'function') createConfetti();
        };
        
        // Navigation overrides for the new context
        function continueShopping() {
            window.location.href = '../user/product_page.php';
        }
        
        function viewOrders() {
            // Changed link to My Appointments
            window.location.href = '../user/my_appointments.php';
        }
    </script>
</body>
</html>
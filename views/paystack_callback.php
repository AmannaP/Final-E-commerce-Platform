<?php
// views/paystack_callback.php
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

if (!checkLogin()) {
    header('Location: ../login/login.php');
    exit();
}

$reference = isset($_GET['reference']) ? trim($_GET['reference']) : null;

if (!$reference) {
    header('Location: checkout.php?error=cancelled');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Booking - GBVAid</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background-color: #c453eaff; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        
        .container { 
            max-width: 500px; 
            width: 90%; 
            background: white; 
            padding: 60px 40px; 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); 
            text-align: center; 
        }
        
        .spinner {
            display: inline-block;
            width: 60px;
            height: 60px;
            border: 5px solid #f3e8ff;
            border-top: 5px solid #c453eaff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 30px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        h1 { color: #333; margin-bottom: 15px; font-weight: 700; }
        p { color: #666; margin-bottom: 20px; }
        
        .reference { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 10px; 
            font-family: monospace; 
            color: #555;
            border: 1px solid #eee; 
        }
        
        .error, .success { padding: 15px; border-radius: 8px; margin: 20px 0; display: none; }
        .error { color: #dc2626; background: #fee2e2; border: 1px solid #fecaca; }
        .success { color: #065f46; background: #d1fae5; border: 1px solid #6ee7b7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner" id="spinner"></div>
        
        <h1>Securing Session</h1>
        <p>Please wait while we confirm your booking...</p>
        
        <div class="reference">
            Ref: <strong><?php echo htmlspecialchars($reference); ?></strong>
        </div>
        
        <div class="error" id="errorBox">
            <strong>Error:</strong> <span id="errorMessage"></span>
        </div>
        
        <div class="success" id="successBox">
            <strong>Success!</strong> Booking confirmed. Redirecting...
        </div>
    </div>

    <script>
        async function verifyPayment() {
            const reference = '<?php echo htmlspecialchars($reference); ?>';
            
            try {
                // Call the backend to verify and SAVE bookings
                const response = await fetch('../actions/paystack_verify_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ reference: reference })
                });
                
                const data = await response.json();
                
                document.getElementById('spinner').style.display = 'none';
                
                if (data.status === 'success') {
                    document.getElementById('successBox').style.display = 'block';
                    setTimeout(() => {
                        window.location.replace(`payment_success.php?reference=${encodeURIComponent(reference)}&invoice=${encodeURIComponent(data.invoice_no)}`);
                    }, 1000);
                } else {
                    showError(data.message || 'Verification failed');
                    setTimeout(() => { window.location.href = 'checkout.php?error=failed'; }, 4000);
                }
            } catch (error) {
                console.error(error);
                showError('Connection error. Please contact support.');
            }
        }
        
        function showError(msg) {
            document.getElementById('errorBox').style.display = 'block';
            document.getElementById('errorMessage').textContent = msg;
        }
        
        window.addEventListener('load', verifyPayment);
    </script>
</body>
</html>
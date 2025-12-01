<?php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!isset($_GET['id'])) {
    header("Location: product_page.php");
    exit();
}

$service_id = $_GET['id'];
$service = get_one_product_ctr($service_id);

if (!$service) {
    echo "Service not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($service['product_title']) ?> | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .text-purple { color: #c453eaff; }
        .btn-purple { background-color: #c453eaff; color: white; border: none; }
        .btn-purple:hover { background-color: #a020f0; color: white; }
        .service-img { border-radius: 15px; width: 100%; height: 400px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php 
if (file_exists('../includes/navbar.php')) {
    include '../includes/navbar.php';
} else {
    include '../views/navbar.php';
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="../uploads/products/<?= htmlspecialchars($service['product_image']) ?>" class="service-img mb-4" alt="Service">
            <h2 class="fw-bold mb-2"><?= htmlspecialchars($service['product_title']) ?></h2>
            <h4 class="text-purple fw-bold mb-3">
                GH₵ <?= number_format($service['product_price'], 2) ?> <small class="text-muted fs-6">/ Session</small>
            </h4>
            <p class="text-muted lead"><?= nl2br(htmlspecialchars($service['product_desc'])) ?></p>
        </div>

        <div class="col-md-5 offset-md-1">
            <div class="card p-4">
                <h4 class="fw-bold mb-4 text-center">Schedule Session</h4>
                
                <form id="booking-form">
                    <input type="hidden" name="product_id" value="<?= $service['product_id'] ?>">
                    <input type="hidden" name="qty" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preferred Date</label>
                        <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Preferred Time</label>
                        <select name="time" class="form-select" required>
                            <option value="">-- Choose a Slot --</option>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="13:00">01:00 PM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                            <option value="16:00">04:00 PM</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Notes / Requirements</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Briefly describe what you need help with..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-purple w-100 py-3 fw-bold rounded-pill">
                        Add to Booking List
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.text();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Adding...').prop('disabled', true);

        // Send to add_to_cart_action
        $.ajax({
            url: '../actions/add_to_cart_action.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Added to Booking List',
                        text: 'This service has been added to your list.',
                        showCancelButton: true,
                        confirmButtonText: 'View List',
                        cancelButtonText: 'Add More',
                        confirmButtonColor: '#c453eaff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../views/cart.php';
                        } else {
                            window.location.href = '../user/product_page.php';
                        }
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                Swal.fire('Error', 'Server connection failed', 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
</script>

</body>
</html>
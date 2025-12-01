<?php
// views/single_product.php
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';
require_once '../controllers/cart_controller.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$product = view_single_product_ctr($product_id);

if (!$product) {
    echo "<h3 class='text-center text-danger mt-5'>Product not found.</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_title']) ?> | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .product-img {
            width: 100%;
            border-radius: 10px;
            max-height: 450px;
            object-fit: cover;
            
        }
        .card {
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #c453eaff;
            border-color: #c453eaff;
        }
        .btn-primary:hover {
            background-color: #e598ffff;
            border-color: #e598ffff;
        }
        .product-label {
            font-weight: bold;
            color: #555;
        }

    </style>
</head>
<body>
    <!-- In single_product.php, right after <body> tag -->
<?php include '../views/navbar.php'; ?>

<!-- Rest of your content -->

<div class="container mt-5 mb-5">
    <a href="../user/product_page.php" class="btn btn-secondary mb-4">&larr; Back</a>

    <div class="card shadow p-4 align-items-center">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="../uploads/products/<?= htmlspecialchars($product['product_image'] ?: '../uploads/products/default.jpg') ?>" 
                     alt="<?= htmlspecialchars($product['product_title']) ?>" 
                     class="product-img shadow-sm">
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h3 class="fw-bold mb-3"><?= htmlspecialchars($product['product_title']) ?></h3>

                <p><span class="product-label">Product ID:</span> <?= htmlspecialchars($product['product_id']) ?></p>
                <p><span class="product-label">Category:</span> <?= htmlspecialchars($product['cat_name'] ?? 'N/A') ?></p>
                <p><span class="product-label">Brand:</span> <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></p>
                
                <h4 class="text-success mb-4">Price: GHS <?= number_format($product['product_price'], 2) ?></h4>
                
                <p><span class="product-label">Description:</span><br>
                    <?= nl2br(htmlspecialchars($product['product_desc'])) ?>
                </p>

                <?php if (!empty($product['product_keywords'])): ?>
                    <p><span class="product-label">Keywords:</span> 
                        <?= htmlspecialchars($product['product_keywords']) ?>
                    </p>
                <?php endif; ?>

                <!-- Make the add to cart button to be functional. -->
                <button class="btn btn-primary add-to-cart-btn"
                    data-id="<?php echo $product['product_id']; ?>"
                    data-title="<?php echo htmlspecialchars($product['product_title']); ?>"
                    data-price="<?php echo $product['product_price']; ?>"
                    data-image="<?php echo htmlspecialchars($product['product_image']); ?>"
                    >Book Session
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Floating Cart Button -->
<a href="../views/cart.php" class="btn btn-primary position-fixed bottom-0 end-0 m-4 shadow-lg" 
   style="z-index: 1000; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
    <i class="bi bi-cart-fill fs-4"></i>
    <?php
    $customer_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $cart_items = get_user_cart_ctr($customer_id ?? $ip_add);
    $cart_count = count($cart_items);
    
    if ($cart_count > 0):
    ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $cart_count ?>
            <span class="visually-hidden">items in cart</span>
        </span>
    <?php endif; ?>
</a>
<footer class="text-center text-muted mt-4 mb-3">
    <small>© <?= date('Y'); ?> GBVAid — Empowering safety and access.</small>
</footer>
    <script src="../js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

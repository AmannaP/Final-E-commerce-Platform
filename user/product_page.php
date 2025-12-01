<?php
// user/product_page.php

require_once '../settings/core.php';
require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Fetch filters
$categories = fetch_categories_ctr();
$brands = fetch_brands_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Products | GBVAid</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
      body {
          background-color: #f8f9fa;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      }
      
      /* Product Card Styling */
      .product-card {
          border: none;
          border-radius: 12px;
          background: white;
          box-shadow: 0 4px 10px rgba(0,0,0,0.08);
          transition: transform 0.2s, box-shadow 0.2s;
          height: 100%;
      }
      .product-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.12);
      }
      
      /* Custom Colors */
      h2, h4 {
          color: #c453eaff;
          font-weight: 700;
      }
      
      .btn-custom {
          background-color: #c453eaff;
          color: white;
          border: none;
      }
      .btn-custom:hover {
          background-color: #a020f0;
          color: white;
      }
      
      footer {
          margin-top: 60px;
          padding: 20px 0;
          border-top: 1px solid #eee;
      }
  </style>
</head>
<body>

<?php 
// Ensure this path matches where you saved the file. 
// Usually it is '../includes/navbar.php'
if (file_exists('../includes/navbar.php')) {
    include '../includes/navbar.php';
} else {
    // Fallback if file is in views folder
    include '../views/navbar.php';
}
?>

<div class="container mt-5">
  <h2 class="fw-bold text-center mb-3">Browse Support Services</h2>
  <p class="text-center text-muted mb-5">
      Explore available services and products. Filter by category or brand, or search by name.
  </p>

  <div class="row mb-4 align-items-end g-3">
      <div class="col-md-4">
          <label for="category_filter" class="form-label fw-bold text-muted">Filter by Category</label>
          <select id="category_filter" class="form-select">
              <option value="">All Categories</option>
              <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
              <?php endforeach; ?>
          </select>
      </div>

      <div class="col-md-4">
          <label for="brand_filter" class="form-label fw-bold text-muted">Filter by Brand</label>
          <select id="brand_filter" class="form-select">
              <option value="">All Brands</option>
              <?php foreach ($brands as $brand): ?>
                  <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
              <?php endforeach; ?>
          </select>
      </div>

      <div class="col-md-4">
          <label for="search_box" class="form-label fw-bold text-muted">Search Product</label>
          <div class="input-group">
              <input type="text" id="search_box" class="form-control" placeholder="Search services...">
              <button class="btn btn-custom" id="search_btn">
                  <i class="bi bi-search"></i> Search
              </button>
          </div>
      </div>
  </div>

  <h4 class="mb-4 border-bottom pb-2">All Products</h4>
  
  <div class="row g-4" id="product-list">
      <div class="text-center py-5">
          <div class="spinner-border text-purple" style="color: #c453eaff;" role="status"></div>
          <p class="mt-2 text-muted">Loading products...</p>
      </div>
  </div>

  <div class="pagination-container text-center mt-5">
      <div id="pagination" class="pagination-buttons d-flex justify-content-center gap-2"></div>
  </div>
</div>

<footer class="text-center text-muted">
  <small>© <?= date('Y'); ?> GBVAid — Empowering safety and support for all.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/product.js"></script>
</body>
</html>
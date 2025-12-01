<?php
// views/product_search_result.php

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Ensure user is logged in
if (!checkLogin()) {
    header("Location: ../login/login.php");
    exit();
}

// Redirect admin to their dashboard
if (isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

// Fetch categories and brands for filters
$categories = fetch_categories_ctr();
$brands = fetch_brands_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results | GBVAid </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #b77a7a;
        }
        .product-card {
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-card:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }
        .btn-custom {
            background-color: #b77a7a;
            color: white;
        }
        .btn-custom:hover {
            background-color: #a46868;
            color: white;
        }
        h2, h4 {
            color: #b77a7a;
        }
        footer {
            margin-top: 40px;
        }
        .pagination-btn {
            border: none;
            background: #b77a7a;
            color: white;
            border-radius: 20px;
            padding: 6px 14px;
            margin: 2px;
            cursor: pointer;
        }
        .pagination-btn:hover {
            background-color: #a46868;
        }
        .pagination-btn.active {
            background: #a46868;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">GBVAid</a>
    <div class="d-flex">
        <span class="navbar-text text-white me-3">
            Welcome, <?= htmlspecialchars($_SESSION['name']); ?> 💜
        </span>
        <a href="../login/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Search Section -->
<div class="container mt-5">
    <h2 class="fw-bold text-center mb-4">Search Results</h2>
    <p class="text-center text-muted mb-4">Browse available products or refine your search by category and brand.</p>

    <!-- Filters -->
    <div class="row mb-4 align-items-end">
        <div class="col-md-4">
            <label for="category_filter" class="form-label">Filter by Category</label>
            <select id="category_filter" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="brand_filter" class="form-label">Filter by Brand</label>
            <select id="brand_filter" class="form-select">
                <option value="">All Brands</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="search_box" class="form-label">Search Product</label>
            <div class="input-group">
                <input type="text" id="search_box" class="form-control" placeholder="Search by name..." 
                       value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button class="btn btn-custom" id="search_btn">Search</button>
            </div>
        </div>
    </div>

    <!-- Products Display -->
    <div class="row g-4" id="product-list">
        <!-- AJAX products will populate here -->
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-4 text-center"></div>
</div>

<footer class="text-center text-muted mt-5 mb-3">
    <small>© <?= date('Y'); ?> GBVAid — Empowering safety and support for all.</small>
</footer>

<!-- External Product JS -->
<script src="../js/product.js"></script>
</body>
</html>

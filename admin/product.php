<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';
require_once '../controllers/product_controller.php';

// Only admin access
if (!checkLogin() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit;
}

$categories = fetch_categories_ctr(); 
$brands = fetch_brands_ctr(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Services | GBVAid Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Admin Navbar */
        .navbar-admin {
            background-color: #c453eaff;
            box-shadow: 0 4px 12px rgba(196, 83, 234, 0.3);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 800;
            color: #fff !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s;
        }
        .nav-link:hover {
            color: #fff !important;
            transform: translateY(-2px);
        }

        .btn-logout {
            background-color: white;
            color: #c453eaff;
            border: 2px solid white;
            border-radius: 50px;
            padding: 5px 20px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-logout:hover {
            background-color: transparent;
            color: white;
        }

        /* Content Styling */
        .content-card {
            border: none;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header-custom {
            background-color: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 20px 30px;
        }

        .card-body {
            padding: 30px;
        }

        /* Form & Inputs */
        .form-control, .form-select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            border-color: #c453eaff;
            box-shadow: 0 0 0 4px rgba(196, 83, 234, 0.1);
        }

        /* Table */
        .table thead th {
            background-color: #c453eaff;
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            vertical-align: middle;
            padding: 15px;
            color: #555;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(196, 83, 234, 0.02);
        }

        /* Buttons */
        .btn-purple {
            background-color: #c453eaff;
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .btn-purple:hover {
            background-color: #a020f0;
            color: white;
        }

        .btn-secondary-custom {
            background-color: #e9ecef;
            color: #495057;
            border: none;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
        }
        .btn-secondary-custom:hover {
            background-color: #dee2e6;
        }

        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-admin navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i>GBVAid Admin</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item mx-2"><a href="../admin/dashboard.php" class="nav-link">Dashboard</a></li>
                    <li class="nav-item mx-2"><a href="../admin/brand.php" class="nav-link">Brands</a></li>
                    <li class="nav-item mx-2"><a href="../admin/category.php" class="nav-link">Categories</a></li>
                    <li class="nav-item ms-4">
                        <a href="../login/logout.php" class="btn-logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #c453eaff;">Service Management</h2>
            <p class="text-muted">Add, edit, and organize support services.</p>
        </div>

        <div class="content-card">
            <div class="card-header-custom">
                <h5 class="fw-bold mb-0"><i class="bi bi-plus-square me-2"></i>Add / Edit Services</h5>
            </div>
            <div class="card-body">
                <form id="product-form" enctype="multipart/form-data">
                    <input type="hidden" id="product_id" name="product_id" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Category</label>
                            <select id="cat_id" name="cat_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= htmlspecialchars($c['cat_id']) ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Brand</label>
                            <select id="brand_id" name="brand_id" class="form-select" required>
                                <option value="">-- Select Brand --</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= htmlspecialchars($b['brand_id']) ?>"><?= htmlspecialchars($b['brand_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Title</label>
                            <input type="text" id="product_title" name="product_title" class="form-control" placeholder="e.g. Safety Kit" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Price (GHS)</label>
                            <div class="input-group">
                                <span class="input-group-text">GH₵</span>
                                <input type="number" id="product_price" step="0.01" min="0" name="product_price" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Keywords</label>
                            <input type="text" id="product_keywords" name="product_keywords" class="form-control" placeholder="comma, separated, tags">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">Description</label>
                            <textarea id="product_desc" name="product_desc" class="form-control" rows="4" placeholder="Describe the service ..."></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">Service Image</label>
                            <input type="file" id="product_image" name="product_image" class="form-control" accept="image/*">
                            <div class="form-text">Accepted formats: JPG, PNG, WEBP, GIF. Max size: 5MB.</div>
                        </div>
                    </div>        
                    
                    <div class="mt-4 text-end border-top pt-3">
                        <button type="button" id="reset-form" class="btn btn-secondary-custom me-2">Reset</button>
                        <button type="submit" id="save-product" class="btn btn-purple px-4"><i class="bi bi-save me-2"></i>Save Service</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header-custom">
                <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2"></i>Existing Services</h5>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0" id="product-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-5 mb-4">
        <small>© <?= date('Y'); ?> GBVAid Platform. Secure Admin Access.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product.js"></script>
</body>
</html>
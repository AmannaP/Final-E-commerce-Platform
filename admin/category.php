<?php
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Restrict access to admins only
if (!checkLogin()) {
    header("Location: ../login/login.php");
    exit();
}
if (!isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage GBV Services | GBVAid Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
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

        /* Page Title */
        .page-title {
            color: #c453eaff;
            font-weight: 800;
            margin-bottom: 10px;
        }

        /* Cards */
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

        /* Table Styling */
        .table thead th {
            background-color: #c453eaff;
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
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
            transition: background 0.3s;
        }
        .btn-purple:hover {
            background-color: #a020f0;
            color: white;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #888;
            padding-bottom: 30px;
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
                    <li class="nav-item mx-2"><a href="../admin/category.php" class="nav-link active">Categories</a></li>
                    <li class="nav-item ms-4">
                        <a href="../login/logout.php" class="btn-logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="text-center mb-5">
                    <h2 class="page-title">Manage Service Categories</h2>
                    <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                        Create and organize support categories like <em>Legal Aid</em>, <em>Counseling</em>, <em>Medical Support</em>, and <em>Safe Shelters</em>.
                    </p>
                </div>

                <div class="content-card">
                    <div class="card-header-custom">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-folder-plus me-2"></i>Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="add-category-form">
                            <div class="input-group input-group-lg">
                                <input type="text" id="cat_name" name="cat_name" class="form-control" placeholder="Enter new category name..." required>
                                <button type="submit" class="btn btn-purple">
                                    <i class="bi bi-plus-lg me-2"></i>Add Category
                                </button>
                            </div>
                            <small class="text-muted ms-1 mt-2 d-block">Tip: Keep names concise (e.g., "Legal Aid", not "We provide legal help")</small>
                        </form>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-task me-2"></i>Existing Categories</h5>
                    </div>
                    <div class="p-0">
                        <table class="table table-striped mb-0" id="category-table">
                            <thead>
                                <tr>
                                    <th width="10%" class="text-center">ID</th>
                                    <th>Service Category Name</th>
                                    <th width="20%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer">
        <p>GBVAid © <?= date('Y') ?> — Empowering communities. Protecting lives.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/category.js"></script>
</body>
</html>
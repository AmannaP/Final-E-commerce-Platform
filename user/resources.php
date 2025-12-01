<?php
require_once '../settings/core.php';
require_once '../controllers/awareness_controller.php'; // NEW: Include the controller

if (!checkLogin()) header("Location: ../login/login.php");

// Fetch content from the database (Managed by Admin)
$resources = get_all_awareness_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Safety Resources | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .page-header {
            color: #c453eaff;
            font-weight: 800;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Emergency Box */
        .emergency-box {
            background: linear-gradient(135deg, #dc3545 0%, #b91c1c 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
            margin-bottom: 40px;
        }

        /* Resource Cards */
        .resource-card {
            border: none;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: 100%;
            transition: transform 0.3s ease;
            overflow: hidden;
        }
        .resource-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(196, 83, 234, 0.15);
        }

        .card-body {
            padding: 30px;
        }

        .card-title {
            color: #c453eaff;
            font-weight: 700;
            margin-bottom: 15px;
            border-bottom: 2px solid #f3e8ff;
            padding-bottom: 10px;
        }

        .card-text {
            color: #555;
            line-height: 1.7;
            font-size: 0.95rem;
        }
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
    <h2 class="page-header">Safety Resources & Guides</h2>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="emergency-box d-md-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div style="background: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                        <i class="bi bi-telephone-fill fs-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">Emergency Hotlines (Ghana)</h4>
                        <p class="mb-0 opacity-75">Immediate help is available 24/7.</p>
                    </div>
                </div>
                <div class="text-end text-md-right">
                    <div class="fs-5">DOVVSU: <strong>18555</strong></div>
                    <div class="fs-5">Police: <strong>191</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($resources)): ?>
            
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-bookmark text-muted opacity-25 display-1"></i>
                <h5 class="mt-3 text-muted">No resources added yet.</h5>
                <p class="text-muted small">Please check back later for safety guides and educational content.</p>
            </div>

        <?php else: ?>
            
            <?php foreach ($resources as $res): ?>
            <div class="col-md-6">
                <div class="card resource-card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-shield-check me-2"></i><?= htmlspecialchars($res['title']) ?>
                        </h4>
                        <div class="card-text">
                            <?= nl2br(htmlspecialchars($res['content'])) ?>
                        </div>
                        <p class="text-muted small mt-3 mb-0 text-end">
                            <em>Posted on <?= date('M d, Y', strtotime($res['created_at'])) ?></em>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<footer class="text-center text-muted mt-5 mb-3">
    <small>© <?= date('Y'); ?> GBVAid — Empowering safety and support for all.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
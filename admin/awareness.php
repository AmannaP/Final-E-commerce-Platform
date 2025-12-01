<?php
require_once '../settings/core.php';
require_once '../controllers/awareness_controller.php';

requireAdmin();

// Handle Form Submit (Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    if(add_awareness_ctr($title, $content)) {
        header("Location: awareness.php?success=added");
    } else {
        header("Location: awareness.php?error=failed");
    }
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    if(delete_awareness_ctr($_GET['delete'])) {
        header("Location: awareness.php?success=deleted");
    }
    exit();
}

// Fetch Data
$contents = get_all_awareness_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Awareness Content | GBVAid Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-admin { background-color: #c453eaff; padding: 15px 0; }
        .navbar-brand { color: white !important; font-weight: 800; }
        .content-card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 30px; }
        .btn-purple { background-color: #c453eaff; color: white; border: none; }
        .btn-purple:hover { background-color: #a020f0; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-admin">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">GBVAid Admin</a>
        <a href="dashboard.php" class="btn btn-sm btn-light text-purple fw-bold">Back to Dashboard</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="content-card">
                <h5 class="fw-bold mb-3" style="color: #333;">Add New Content</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Signs of Abuse">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Content / Tips</label>
                        <textarea name="content" class="form-control" rows="6" required placeholder="Enter educational text here..."></textarea>
                    </div>
                    <button type="submit" name="add_content" class="btn btn-purple w-100">Post Content</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="content-card">
                <h5 class="fw-bold mb-3" style="color: #333;">Existing Resources</h5>
                
                <?php if(empty($contents)): ?>
                    <p class="text-muted text-center py-4">No awareness content added yet.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($contents as $c): ?>
                        <div class="list-group-item p-4 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <h5 class="mb-0 fw-bold text-primary"><?= htmlspecialchars($c['title']) ?></h5>
                                <small class="text-muted"><?= date('M d, Y', strtotime($c['created_at'])) ?></small>
                            </div>
                            <p class="mb-3 text-muted"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                            
                            <div class="text-end">
                                <a href="?delete=<?= $c['awareness_id'] ?>" class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Are you sure you want to delete this post?')">
                                   Delete
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
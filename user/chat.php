<?php
require_once '../settings/core.php';
require_once '../controllers/chat_controller.php';

if (!checkLogin()) header("Location: ../login/login.php");

$groups = get_chat_groups_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support Groups | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .group-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: white;
        }
        .group-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(196, 83, 234, 0.2);
        }
        .icon-box {
            width: 60px; height: 60px;
            background-color: #f3e8ff; color: #c453eaff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; margin-bottom: 15px;
        }
    </style>
</head>
<body>

<?php include '../views/navbar.php'; ?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold" style="color: #c453eaff;">Community Support Groups</h2>
        <p class="text-muted">Join a safe space to share, listen, and heal together.</p>
    </div>

    <div class="row g-4">
        <?php foreach ($groups as $group): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card group-card h-100 p-4 text-center">
                <div class="d-flex justify-content-center">
                    <div class="icon-box">
                        <i class="bi <?= htmlspecialchars($group['icon'] ?? 'bi-people') ?>"></i>
                    </div>
                </div>
                <h5 class="fw-bold"><?= htmlspecialchars($group['group_name']) ?></h5>
                <p class="text-muted small mb-4"><?= htmlspecialchars($group['description']) ?></p>
                <a href="chat_room.php?id=<?= $group['group_id'] ?>" class="btn w-100 text-white fw-bold" style="background-color: #c453eaff; border-radius: 50px;">
                    Join Discussion
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
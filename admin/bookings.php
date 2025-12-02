<?php
require_once '../settings/core.php';
require_once '../controllers/appointment_controller.php';

// Restrict to Admin
requireAdmin();

// 1. Fetch all bookings
$all_bookings = get_all_bookings_admin_ctr();

// 2. Filter & Sort Logic
$upcoming = [];
$history = [];
$now = new DateTime();
// Set timezone to match your database/server setting
$now->setTimezone(new DateTimeZone('Africa/Accra'));

foreach ($all_bookings as $b) {
    $apptTime = new DateTime($b['appointment_date'] . ' ' . $b['appointment_time']);
    
    // Sort into buckets
    if ($apptTime >= $now && $b['status'] != 'Cancelled' && $b['status'] != 'Completed') {
        $upcoming[] = $b;
    } else {
        $history[] = $b;
    }
}

// Sort UPCOMING: Earliest date at the top (Ascending)
usort($upcoming, function($a, $b) {
    $t1 = strtotime($a['appointment_date'] . ' ' . $a['appointment_time']);
    $t2 = strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
    return $t1 - $t2;
});

// Sort HISTORY: Most recent at the top (Descending)
usort($history, function($a, $b) {
    $t1 = strtotime($a['appointment_date'] . ' ' . $a['appointment_time']);
    $t2 = strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
    return $t2 - $t1;
});

// Extract unique categories for filter
$categories = [];
foreach ($all_bookings as $b) {
    if (!empty($b['cat_name'])) $categories[$b['cat_name']] = true;
}
$categories = array_keys($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings | GBVAid Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        
        /* Admin Navbar */
        .navbar-admin { background-color: #c453eaff; padding: 15px 0; }
        .navbar-brand { color: white !important; font-weight: 800; }
        
        /* FIX: Scope white text ONLY to navbar links, not tabs */
        .navbar-nav .nav-link { 
            color: rgba(255,255,255,0.9) !important; 
            font-weight: 500; 
        }
        .navbar-nav .nav-link:hover { color: white !important; }
        
        .btn-logout { border: 2px solid white; color: white; border-radius: 50px; font-weight: 700; text-decoration: none; padding: 5px 20px; }
        .btn-logout:hover { background: white; color: #c453eaff; }

        /* Content */
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 30px;
            margin-bottom: 50px;
        }

        /* Tabs Styling - Now Visible! */
        .nav-tabs { border-bottom: 2px solid #eee; margin-bottom: 20px; }
        
        .nav-tabs .nav-link {
            color: #666 !important; /* Force Grey for inactive tabs */
            font-weight: 600;
            border: none;
            background: transparent;
            font-size: 1.1rem;
            padding-bottom: 10px;
        }
        
        .nav-tabs .nav-link.active {
            color: #c453eaff !important; /* Force Purple for active tab */
            border-bottom: 3px solid #c453eaff;
        }
        
        .nav-tabs .nav-link:hover { color: #c453eaff !important; }

        .table thead th {
            background-color: #c453eaff;
            color: white;
            border: none;
            padding: 15px;
        }
        
        .status-badge { font-size: 0.8rem; padding: 5px 12px; border-radius: 50px; }
        .bg-pending { background-color: #fff3cd; color: #856404; }
        .bg-confirmed { background-color: #d1fae5; color: #065f46; }
        .bg-cancelled { background-color: #f8d7da; color: #721c24; }
        .bg-completed { background-color: #cce5ff; color: #004085; }
        
        .filter-select {
            border: 2px solid #c453eaff;
            color: #c453eaff;
            font-weight: 600;
            border-radius: 50px;
            padding-left: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-admin navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">GBVAid Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item mx-2"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li class="nav-item mx-2"><a href="bookings.php" class="nav-link active fw-bold">Bookings</a></li>
                <li class="nav-item mx-2"><a href="product.php" class="nav-link">Services</a></li>
                <li class="nav-item ms-4"><a href="../login/logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="content-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #c453eaff;">All Service Bookings</h3>
                <p class="text-muted">Track user appointments, categories, and service providers.</p>
            </div>
            
            <div class="d-flex align-items-center">
                <label class="me-2 fw-bold text-muted">Filter by:</label>
                <select id="categoryFilter" class="form-select filter-select" style="width: 250px;">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <ul class="nav nav-tabs" id="bookingTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                    Upcoming <span class="badge bg-light text-dark ms-2"><?= count($upcoming) ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                    History / Cancelled
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="bookingTabContent">
            
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Service / Product</th>
                                <th>Category</th>
                                <th>Note</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="booking-list">
                            <?php if (empty($upcoming)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No upcoming bookings found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($upcoming as $b): 
                                    $dateObj = new DateTime($b['appointment_date']);
                                    $timeObj = new DateTime($b['appointment_time']);
                                    $statusClass = 'bg-confirmed'; // Default for upcoming paid slots
                                ?>
                                <tr class="booking-row" data-category="<?= htmlspecialchars($b['cat_name']) ?>">
                                    <td>
                                        <div class="fw-bold text-dark"><?= $dateObj->format('M d, Y') ?></div>
                                        <small class="text-muted"><?= $timeObj->format('h:i A') ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($b['customer_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($b['customer_contact']) ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($b['product_title']) ?><br>
                                        <small class="text-muted"><i class="bi bi-building me-1"></i><?= htmlspecialchars($b['brand_name']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #f3e8ff; color: #c453eaff;">
                                            <?= htmlspecialchars($b['cat_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!empty($b['notes'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" 
                                                    onclick="showNote('<?= htmlspecialchars(addslashes($b['notes'])) ?>')">
                                                View Note
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $statusClass ?>"><?= $b['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Customer</th>
                                <th>Service / Product</th>
                                <th>Category</th>
                                <th>Note</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="booking-list">
                            <?php if (empty($history)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No past history found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($history as $b): 
                                    $dateObj = new DateTime($b['appointment_date']);
                                    $timeObj = new DateTime($b['appointment_time']);
                                    
                                    $statusClass = 'bg-completed';
                                    if($b['status'] == 'Cancelled') $statusClass = 'bg-cancelled';
                                ?>
                                <tr class="booking-row" data-category="<?= htmlspecialchars($b['cat_name']) ?>">
                                    <td>
                                        <div class="text-muted fw-bold"><?= $dateObj->format('M d, Y') ?></div>
                                        <small class="text-muted"><?= $timeObj->format('h:i A') ?></small>
                                    </td>
                                    <td>
                                        <div class="text-secondary"><?= htmlspecialchars($b['customer_name']) ?></div>
                                    </td>
                                    <td class="text-muted">
                                        <?= htmlspecialchars($b['product_title']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">
                                            <?= htmlspecialchars($b['cat_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if(!empty($b['notes'])): ?>
                                            <button type="button" class="btn btn-sm btn-light text-secondary border rounded-pill px-3" 
                                                    onclick="showNote('<?= htmlspecialchars(addslashes($b['notes'])) ?>')">
                                                View Note
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $statusClass ?>"><?= $b['status'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Customer Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="noteContent" class="text-secondary" style="font-size: 1.1rem; line-height: 1.6;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show Note Modal Logic
    function showNote(note) {
        document.getElementById('noteContent').textContent = note;
        new bootstrap.Modal(document.getElementById('noteModal')).show();
    }

    // Filter Logic (Works on both tabs)
    document.getElementById('categoryFilter').addEventListener('change', function() {
        const selected = this.value;
        const rows = document.querySelectorAll('.booking-row');
        
        rows.forEach(row => {
            if (selected === 'all' || row.getAttribute('data-category') === selected) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
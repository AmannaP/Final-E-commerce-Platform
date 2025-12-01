<?php
require_once '../settings/core.php';
require_once '../controllers/appointment_controller.php';

// 1. Set Timezone
date_default_timezone_set('Africa/Accra');

if (!checkLogin()) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch all appointments
$appointments = get_user_appointments_ctr($_SESSION['id']);

// Filter appointments
$upcoming = [];
$past = [];
$now = new DateTime();

foreach ($appointments as $appt) {
    $apptTime = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);
    
    // Logic: If time is in future AND not cancelled -> Upcoming
    if ($apptTime >= $now && $appt['status'] != 'Cancelled') {
        $upcoming[] = $appt;
    } else {
        $past[] = $appt; // Cancelled or Past dates go to History
    }
}

// 2. SORTING LOGIC
// Sort UPCOMING: Closest date at the top (Ascending)
usort($upcoming, function($a, $b) {
    $t1 = strtotime($a['appointment_date'] . ' ' . $a['appointment_time']);
    $t2 = strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
    return $t1 - $t2; // Ascending
});

// Sort PAST: Most recent history at the top (Descending)
usort($past, function($a, $b) {
    $t1 = strtotime($a['appointment_date'] . ' ' . $a['appointment_time']);
    $t2 = strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
    return $t2 - $t1; // Descending
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments | GBVAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #c453eaff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .page-container {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 40px;
            margin-bottom: 40px;
            min-height: 60vh;
        }
        
        /* FIXED TAB COLORS */
        .nav-tabs {
            border-bottom: 2px solid #f0f0f0;
        }
        .nav-tabs .nav-link {
            color: #666 !important; /* Visible Grey for inactive */
            font-weight: 600;
            border: none;
            background: transparent;
        }
        .nav-tabs .nav-link.active {
            color: #c453eaff !important; /* Purple for active */
            border-bottom: 3px solid #c453eaff;
            background: transparent;
        }
        .nav-tabs .nav-link:hover {
            color: #c453eaff !important;
            border-color: transparent;
        }

        .appt-card {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        .appt-card:hover {
            transform: translateY(-2px);
            border-color: #e598ffff;
        }
        
        .date-box {
            text-align: center;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 8px 12px;
            min-width: 90px;
            border: 1px solid #eee;
        }
        .date-day { font-size: 1.4rem; font-weight: 800; color: #333; line-height: 1; }
        .date-month { font-size: 0.8rem; color: #666; text-transform: uppercase; }
        .date-year { font-size: 0.7rem; color: #999; font-weight: 600; }
        
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<?php include '../views/navbar.php'; ?>

<div class="container">
    <div class="page-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0 text-dark">My Sessions</h2>
            <a href="product_page.php" class="btn text-white fw-bold" style="background-color: #c453eaff; border-radius: 50px;">
                <i class="bi bi-plus-lg me-1"></i> Book New
            </a>
        </div>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">Upcoming (<?= count($upcoming) ?>)</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">History</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                <?php if (empty($upcoming)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-check text-muted opacity-25 display-1"></i>
                        <p class="mt-3 text-muted">No upcoming sessions scheduled.</p>
                        <a href="product_page.php" class="btn btn-sm btn-outline-secondary rounded-pill mt-2">Find a Service</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcoming as $appt): 
                        $dateObj = new DateTime($appt['appointment_date']);
                        $timeObj = new DateTime($appt['appointment_time']);
                    ?>
                    <div class="appt-card p-3 d-flex align-items-center">
                        <div class="date-box me-3">
                            <div class="date-day"><?= $dateObj->format('d') ?></div>
                            <div class="date-month"><?= $dateObj->format('M') ?></div>
                            <div class="date-year"><?= $dateObj->format('Y') ?></div>
                        </div>
                        
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($appt['product_title']) ?></h5>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> <?= $timeObj->format('h:i A') ?> 
                                <span class="mx-2">•</span> 
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Confirmed</span>
                            </div>
                            <?php if(!empty($appt['notes'])): ?>
                                <div class="text-muted small mt-1 fst-italic text-truncate" style="max-width: 300px;">
                                    Note: <?= htmlspecialchars($appt['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <button class="btn btn-outline-danger btn-sm rounded-pill cancel-btn px-3" data-id="<?= $appt['appointment_id'] ?>">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="history" role="tabpanel">
                <?php if (empty($past)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No past appointment history.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($past as $appt): 
                        $dateObj = new DateTime($appt['appointment_date']);
                        $statusColor = ($appt['status'] == 'Cancelled') ? 'text-danger' : 'text-muted';
                        $statusBg = ($appt['status'] == 'Cancelled') ? 'bg-danger bg-opacity-10' : 'bg-secondary bg-opacity-10';
                    ?>
                    <div class="appt-card p-3 d-flex align-items-center opacity-75" style="background-color: #fcfcfc;">
                        <div class="date-box me-3 bg-light border-0">
                            <div class="date-day text-muted"><?= $dateObj->format('d') ?></div>
                            <div class="date-month text-muted"><?= $dateObj->format('M') ?></div>
                            <div class="date-year text-muted"><?= $dateObj->format('Y') ?></div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-secondary"><?= htmlspecialchars($appt['product_title']) ?></h6>
                            <small class="badge <?= $statusBg ?> <?= $statusColor ?>">
                                <?= $appt['status'] ?>
                            </small>
                            <small class="text-muted ms-2">
                                <?= $dateObj->format('F d, Y') ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Cancel Appointment Logic
    $('.cancel-btn').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Cancel Session?',
            text: "This will cancel your upcoming appointment. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Cancel it'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({title: 'Processing...', didOpen: () => Swal.showLoading()});
                
                $.post('../actions/cancel_appointment_action.php', { appointment_id: id }, function(res) {
                    if (res.trim() === 'success') {
                        Swal.fire('Cancelled', 'Your appointment has been cancelled.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', 'Could not cancel appointment. Please try again.', 'error');
                    }
                }).fail(function() {
                    Swal.fire('Error', 'Network error.', 'error');
                });
            }
        });
    });
</script>

</body>
</html>
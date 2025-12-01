<?php
require_once '../settings/core.php';
require_once '../classes/report_class.php';

requireAdmin();

$reportObj = new Report();
$reports = $reportObj->get_all_reports();

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['status'])) {
    $reportObj->update_status($_POST['report_id'], $_POST['status']);
    header("Location: reports.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survivor Reports | GBVAid Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-admin { background-color: #c453eaff; padding: 15px 0; }
        .navbar-brand { color: white !important; font-weight: 800; }
        .content-card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; margin-top: 30px; }
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

<div class="container">
    <div class="content-card">
        <h3 class="fw-bold mb-4" style="color: #c453eaff;">Incident Reports</h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Reporter</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($reports)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No reports found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r): ?>
                        <tr>
                            <td><?= date('M d, Y', strtotime($r['incident_date'])) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($r['incident_type']) ?></span></td>
                            <td><?= htmlspecialchars($r['location']) ?></td>
                            <td>
                                <?php if($r['is_anonymous']): ?>
                                    <span class="badge bg-dark"><i class="bi bi-eye-slash-fill me-1"></i>Anonymous</span>
                                <?php else: ?>
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($r['customer_name']) ?></span><br>
                                    <small class="text-muted"><?= htmlspecialchars($r['customer_contact']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reportModal" 
                                        data-desc="<?= htmlspecialchars($r['description']) ?>"
                                        data-type="<?= htmlspecialchars($r['incident_type']) ?>"
                                        data-reporter="<?= $r['is_anonymous'] ? 'Anonymous' : htmlspecialchars($r['customer_name']) ?>">
                                    <i class="bi bi-file-text me-1"></i>View
                                </button>
                            </td>
                            <td>
                                <?php 
                                    $statusColor = 'bg-warning text-dark';
                                    if($r['report_status'] == 'Resolved') $statusColor = 'bg-success';
                                    if($r['report_status'] == 'Investigating') $statusColor = 'bg-info';
                                ?>
                                <span class="badge <?= $statusColor ?>"><?= $r['report_status'] ?></span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="report_id" value="<?= $r['report_id'] ?>">
                                    <select name="status" class="form-select form-select-sm" style="width: 130px; border-color: #c453eaff;" onchange="this.form.submit()">
                                        <option value="" disabled selected>Action...</option>
                                        <option value="Investigating">Investigate</option>
                                        <option value="Resolved">Resolve</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="modalTitle">Incident Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="text-muted small uppercase">Description</h6>
        <p id="modalDesc" class="lead fs-6" style="white-space: pre-wrap;"></p>
        <hr>
        <div class="d-flex justify-content-between text-muted small">
            <span>Reporter: <strong id="modalReporter"></strong></span>
            <span>Type: <strong id="modalType"></strong></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Script to pass data into the Modal
    var reportModal = document.getElementById('reportModal');
    reportModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        
        var desc = button.getAttribute('data-desc');
        var type = button.getAttribute('data-type');
        var reporter = button.getAttribute('data-reporter');
        
        reportModal.querySelector('#modalDesc').textContent = desc;
        reportModal.querySelector('#modalType').textContent = type;
        reportModal.querySelector('#modalReporter').textContent = reporter;
    });
</script>

</body>
</html>
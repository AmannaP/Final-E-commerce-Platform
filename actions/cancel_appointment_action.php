<?php
session_start();
require_once '../controllers/appointment_controller.php';

$uid = $_SESSION['id'] ?? null;
$aid = $_POST['appointment_id'] ?? null;

if ($uid && $aid) {
    if (cancel_appointment_ctr($aid, $uid)) {
        echo "success";
    } else {
        echo "failed";
    }
} else {
    echo "invalid";
}
?>
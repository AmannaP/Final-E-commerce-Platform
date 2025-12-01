<?php
// actions/book_appointment_action.php
session_start();
require_once '../controllers/appointment_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to book an appointment.']);
    exit();
}

$uid = $_SESSION['id'];
$sid = $_POST['service_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$notes = $_POST['notes'];

if (empty($date) || empty($time)) {
    echo json_encode(['status' => 'error', 'message' => 'Date and Time are required.']);
    exit();
}

$result = book_appointment_ctr($uid, $sid, $date, $time, $notes);

if ($result === "taken") {
    echo json_encode(['status' => 'error', 'message' => 'Sorry, this time slot is already booked.']);
} elseif ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to book appointment. Try again.']);
}
?>
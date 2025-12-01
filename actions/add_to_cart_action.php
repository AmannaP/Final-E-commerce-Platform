<?php
session_start();
header('Content-Type: application/json');

require_once("../controllers/cart_controller.php");

try {
    $json_input = file_get_contents('php://input');
    $request_data = json_decode($json_input, true);

    // If we received valid JSON, merge it into $_POST so your existing code works
    if (is_array($request_data)) {
        $_POST = array_merge($_POST, $request_data);
    }

    // Ensure product ID is provided
    if (!isset($_POST['product_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "No service selected."
        ]);
        exit();
    }

    $p_id = intval($_POST['product_id']);

    // Check Login status
    // Always get the IP address to satisfy database constraints
    $ip_add = $_SERVER['REMOTE_ADDR'];

    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
        $c_id = $_SESSION['id'];
    } else {
        $c_id = null;
    }

    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;

    // NEW: Capture Booking Details
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : null;

    // Determine User
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
        $c_id = $_SESSION['id'];
    } else {
        $c_id = null;
    }

    // CALL CONTROLLER FUNCTION
    $result = add_to_cart_ctr($p_id, $ip_add, $c_id, $qty, $date, $time, $notes);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Service added to booking list."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to add service to booking list."
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Server error: " . $e->getMessage(),
    ]);
}
?>
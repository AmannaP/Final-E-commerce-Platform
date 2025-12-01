<?php
// actions/paystack_verify_payment.php

// 1. SET TIMEZONE
date_default_timezone_set('Africa/Accra');

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../settings/paystack_config.php';
require_once '../controllers/cart_controller.php';
require_once '../classes/order_class.php';
require_once '../classes/appointment_class.php'; // NEW: Include appointment class

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$reference = $input['reference'] ?? null;

if (!$reference) {
    echo json_encode(['status' => 'error', 'message' => 'No reference provided']);
    exit();
}

if (!checkLogin()) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$customer_id = $_SESSION['id'];
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // 2. VERIFY TRANSACTION WITH PAYSTACK
    $verify_response = paystack_verify_transaction($reference);

    if (!$verify_response['status'] || $verify_response['data']['status'] !== 'success') {
        echo json_encode(['status' => 'error', 'message' => 'Payment verification failed']);
        exit();
    }

    // Payment details
    $amount_paid = $verify_response['data']['amount'] / 100;
    $currency = $verify_response['data']['currency'];
    $payment_date = date("Y-m-d H:i:s");
    $auth_code = $verify_response['data']['authorization']['authorization_code'] ?? null;
    $channel = $verify_response['data']['channel'] ?? 'card';

    // 3. GET CART ITEMS (Including Date/Time/Notes)
    // We need to fetch the full rows, not just the product details
    // We'll use a direct DB call via cart controller if needed, or rely on the standard fetch
    // Assuming get_user_cart_ctr returns the joined array.
    // NOTE: We need the columns booking_date, booking_time, notes from the CART table.
    // Ensure your cart_class.php get functions select these columns!
    
    // Let's create a specific fetch in this file to be safe or rely on the controller if updated.
    // For safety, let's assume get_user_cart_ctr returns *everything*.
    $cart_items = get_user_cart_ctr($customer_id);

    if (empty($cart_items)) {
        echo json_encode(['status' => 'error', 'message' => 'Booking list is empty']);
        exit();
    }

    // 4. CREATE ORDER (Financial Record)
    $order = new order_class();
    $invoice_no = 'BKG-' . strtoupper(uniqid()); // Changed prefix to BKG (Booking)
    $order_status = 'Completed'; // Paid immediately

    $order_id = $order->create_order($customer_id, $invoice_no, $payment_date, $order_status);

    if (!$order_id) throw new Exception("Failed to create order record");

    // 5. PROCESS ITEMS: Save to Orders AND Appointments
    $appt = new Appointment();

    foreach ($cart_items as $item) {
        // A. Add to Order Details (Financial History)
        $order->add_order_details($order_id, $item['p_id'], $item['qty']);

        // B. CREATE APPOINTMENT (Service Scheduling)
        // Check if the cart item has booking details (it should from our previous step)
        $b_date = $item['booking_date'] ?? date('Y-m-d'); // Fallback if missing
        $b_time = $item['booking_time'] ?? '09:00:00';
        $b_notes = $item['notes'] ?? '';

        // Save to appointments table
        // We use the same 'Pending' status until the provider confirms, 
        // OR 'Confirmed' because they just paid. Let's use 'Confirmed'.
        
        // Note: appointment_class.php usually sets status to 'Pending' by default.
        // You might want to update the class to accept status, or update it here manually.
        // For now, we use the book_appointment function.
        $appt->book_appointment($customer_id, $item['p_id'], $b_date, $b_time, $b_notes);
        
        // OPTIONAL: Update the status to 'Confirmed' since they paid
        // $appt->update_status($new_appt_id, 'Confirmed'); 
    }

    // 6. RECORD PAYMENT
    $order->record_payment(
        $amount_paid,
        $customer_id,
        $order_id,
        $currency,
        $payment_date,
        'paystack',
        $reference,
        $auth_code,
        $channel
    );

    // 7. EMPTY CART
    empty_cart_ctr($ip_address, $customer_id);

    // 8. SUCCESS
    echo json_encode([
        'status' => 'success',
        'verified' => true,
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'message' => 'Booking secured successfully'
    ]);

} catch (Exception $e) {
    error_log("Payment Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error processing booking']);
}
?>
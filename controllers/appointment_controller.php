<?php
// controllers/appointment_controller.php
require_once '../classes/appointment_class.php';

function book_appointment_ctr($customer_id, $service_id, $date, $time, $notes) {
    $appt = new Appointment();
    
    // Optional: Check availability first
    if ($appt->is_slot_taken($service_id, $date, $time)) {
        return "taken";
    }

    return $appt->book_appointment($customer_id, $service_id, $date, $time, $notes);
}

function get_user_appointments_ctr($customer_id) {
    $appt = new Appointment();
    return $appt->get_user_appointments($customer_id);
}

function cancel_appointment_ctr($appt_id, $customer_id){
    $appt = new Appointment();
    return $appt->cancel_appointment($appt_id, $customer_id);
}

function get_all_bookings_admin_ctr() {
    $appt = new Appointment();
    return $appt->get_all_bookings_admin();
}

?>
<?php
// classes/appointment_class.php
require_once '../settings/db_class.php';

class Appointment extends db_conn {

    /**
     * Create new appointment
     */
    public function book_appointment($customer_id, $service_id, $date, $time, $notes) {
        if (!$this->db_connect()) return false;

        $sql = "INSERT INTO appointments (customer_id, service_id, appointment_date, appointment_time, notes, status) 
                VALUES (?, ?, ?, ?, ?, 'Pending')";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$customer_id, $service_id, $date, $time, $notes]);
    }

    /**
     * Check if a slot is already taken (Basic conflict check)
     */
    public function is_slot_taken($service_id, $date, $time) {
        if (!$this->db_connect()) return false;

        $sql = "SELECT count(*) as count FROM appointments 
                WHERE service_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$service_id, $date, $time]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Get all appointments for a specific user
     */
    public function get_user_appointments($customer_id) {
        if (!$this->db_connect()) return [];

        // Join with 'products' table to get the Service Name (product_title)
        $sql = "SELECT a.*, p.product_title, p.product_image 
                FROM appointments a
                JOIN products p ON a.service_id = p.product_id
                WHERE a.customer_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel an appointment
     */
    public function cancel_appointment($appt_id, $customer_id) {
        if (!$this->db_connect()) return false;
        
        $sql = "UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND customer_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$appt_id, $customer_id]);
    }
}
?>
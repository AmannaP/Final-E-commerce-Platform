<?php
// classes/report_class.php
require_once '../settings/db_class.php';

class Report extends db_conn {
    
    // Get all reports joined with customer data
    public function get_all_reports() {
        if (!$this->db_connect()) return [];
        
        $sql = "SELECT r.*, c.customer_name, c.customer_contact 
                FROM reports r
                LEFT JOIN customer c ON r.customer_id = c.customer_id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update report status
    public function update_status($id, $status) {
        if (!$this->db_connect()) return false;
        
        $sql = "UPDATE reports SET report_status = ? WHERE report_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
?>
<?php
// classes/awareness_class.php
require_once '../settings/db_class.php';

class Awareness extends db_conn {
    
    // Add new content
    public function add_awareness($title, $content) {
        if (!$this->db_connect()) return false;
        
        $sql = "INSERT INTO awareness (title, content) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $content]);
    }

    // Get all content
    public function get_all_awareness() {
        if (!$this->db_connect()) return [];
        
        $sql = "SELECT * FROM awareness ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete content
    public function delete_awareness($id) {
        if (!$this->db_connect()) return false;
        
        $sql = "DELETE FROM awareness WHERE awareness_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
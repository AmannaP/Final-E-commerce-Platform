<?php
// classes/chat_class.php
require_once '../settings/db_class.php';

class Chat extends db_conn {

    // 1. Get all chat groups
    public function get_all_groups() {
        if (!$this->db_connect()) return [];
        $sql = "SELECT * FROM chat_groups";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Get specific group details
    public function get_group_details($group_id) {
        if (!$this->db_connect()) return false;
        $sql = "SELECT * FROM chat_groups WHERE group_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Send a message
    public function add_message($group_id, $user_id, $message) {
        if (!$this->db_connect()) return false;
        $sql = "INSERT INTO chat_messages (group_id, user_id, message) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$group_id, $user_id, $message]);
    }

    // 4. Fetch messages for a group
    public function get_messages($group_id) {
        if (!$this->db_connect()) return [];
        
        // Join with customer table to get Sender Name
        $sql = "SELECT m.*, c.customer_name 
                FROM chat_messages m
                JOIN customer c ON m.user_id = c.customer_id
                WHERE m.group_id = ?
                ORDER BY m.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<?php
// classes/cart_class.php

require_once("../settings/db_class.php");

class cart_class extends db_conn
{
    /**
     * Add a service to cart with booking details
     */
    public function add_to_cart($p_id, $ip_add, $c_id, $qty, $date = null, $time = null, $notes = null) {
        if (!$this->db_connect()) return false;

        try {
            $notes = $notes ? htmlspecialchars($notes) : null;

            if ($c_id !== null) {
                // LOGGED-IN USER
                $check_sql = "SELECT qty FROM cart WHERE p_id = ? AND c_id = ?";
                $check_stmt = $this->db->prepare($check_sql);
                $check_stmt->execute([$p_id, $c_id]);
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $new_qty = $existing['qty'] + $qty;
                    $sql = "UPDATE cart SET qty = ?, booking_date = ?, booking_time = ?, notes = ? WHERE p_id = ? AND c_id = ?";
                    $stmt = $this->db->prepare($sql);
                    return $stmt->execute([$new_qty, $date, $time, $notes, $p_id, $c_id]);
                } else {
                    $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $this->db->prepare($sql);
                    return $stmt->execute([$p_id, $c_id, $ip_add, $qty, $date, $time, $notes]);
                }
            } else {
                // GUEST USER
                $check_sql = "SELECT qty FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
                $check_stmt = $this->db->prepare($check_sql);
                $check_stmt->execute([$p_id, $ip_add]);
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    $new_qty = $existing['qty'] + $qty;
                    $sql = "UPDATE cart SET qty = ?, booking_date = ?, booking_time = ?, notes = ? WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
                    $stmt = $this->db->prepare($sql);
                    return $stmt->execute([$new_qty, $date, $time, $notes, $p_id, $ip_add]);
                } else {
                    $sql = "INSERT INTO cart (p_id, ip_add, qty, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $this->db->prepare($sql);
                    return $stmt->execute([$p_id, $ip_add, $qty, $date, $time, $notes]);
                }
            }
        } catch (Exception $e) {
            error_log("Add to cart error: " . $e->getMessage());
            return false;
        }
    } 

    /**
     * Check duplicate
     */
    public function check_cart_duplicate($p_id, $ip_add, $c_id)
    {
        $sql = "SELECT * FROM cart 
                WHERE p_id='$p_id' 
                AND (ip_add='$ip_add' OR c_id='$c_id')
                LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Update quantity
     */
    public function update_quantity($p_id, $ip_add, $c_id, $qty)
    {
        if (!$this->db_connect()) return false;

        try {
            if ($c_id !== null && $c_id > 0) {
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$qty, $p_id, $c_id]);
            } else {
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$qty, $p_id, $ip_add]);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Remove from cart
     */
    public function remove_from_cart($p_id, $ip_add, $c_id) {
        if (!$this->db_connect()) return false;
        
        try {
            if ($c_id !== null) {
                $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$p_id, $c_id]);
            } else {
                $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$p_id, $ip_add]);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get cart items by customer ID
     * UPDATED: Now selects booking_date, booking_time, notes
     */
    function get_cart_by_customer($customer_id) {
        if (!$this->db_connect()) return [];

        $sql = "SELECT c.p_id, c.qty, c.booking_date, c.booking_time, c.notes, 
                       p.product_title, p.product_price, p.product_image, p.product_desc
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get cart items by IP
     * UPDATED: Now selects booking_date, booking_time, notes
     */
    function get_cart_by_ip($ip_address) {
        if (!$this->db_connect()) return [];

        $sql = "SELECT c.p_id, c.qty, c.booking_date, c.booking_time, c.notes, 
                       p.product_title, p.product_price, p.product_image, p.product_desc
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.ip_add = ? AND c.c_id IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ip_address]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Empty cart
     */
    public function empty_cart($ip_add, $c_id) {
        if (!$this->db_connect()) return false;

        try {
            if ($c_id !== null) {
                $sql = "DELETE FROM cart WHERE c_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$c_id]);
            } else {
                $sql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$ip_add]);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    // Merge logic...
    function merge_guest_cart($customer_id, $ip_address) {
        if (!$this->db_connect()) return false;
        
        try {
            // Updated to select all booking details
            $sql = "SELECT p_id, qty, booking_date, booking_time, notes FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ip_address]);
            $guest_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($guest_items)) return true;

            foreach ($guest_items as $item) {
                $p_id = $item['p_id'];
                $qty = $item['qty'];
                $date = $item['booking_date'];
                $time = $item['booking_time'];
                $notes = $item['notes'];
                
                $check_sql = "SELECT qty FROM cart WHERE p_id = ? AND c_id = ?";
                $check_stmt = $this->db->prepare($check_sql);
                $check_stmt->execute([$p_id, $customer_id]);
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    $new_qty = $existing['qty'] + $qty;
                    // Update existing with guest details if provided
                    $up_sql = "UPDATE cart SET qty = ?, booking_date = ?, booking_time = ?, notes = ? WHERE p_id = ? AND c_id = ?";
                    $up_stmt = $this->db->prepare($up_sql);
                    $up_stmt->execute([$new_qty, $date, $time, $notes, $p_id, $customer_id]);
                } else {
                    $in_sql = "INSERT INTO cart (p_id, c_id, ip_add, qty, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $in_stmt = $this->db->prepare($in_sql);
                    $in_stmt->execute([$p_id, $customer_id, $ip_address, $qty, $date, $time, $notes]);
                }
            }
            
            $del_sql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $del_stmt = $this->db->prepare($del_sql);
            $del_stmt->execute([$ip_address]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
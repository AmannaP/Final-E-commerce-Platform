<?php
// Classes/customer_class.php
require_once '../settings/db_class.php';

class Customer extends db_conn {

    public function getCustomerByEmail($email) {
        $sql = "SELECT * FROM customer WHERE customer_email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function verifyPassword($email, $password) {
        $customer = $this->getCustomerByEmail($email);
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            return $customer; // return customer details if valid
        }
        return false;
    }

    /**
     * Update customer details including image
     */
    public function update_customer($id, $name, $contact, $city, $country, $image = null) {
        if (!$this->db_connect()) return false;

        // If an image is provided, update it. If not, keep the old one.
        if ($image) {
            $sql = "UPDATE customer SET 
                    customer_name = ?, 
                    customer_contact = ?, 
                    customer_city = ?, 
                    customer_country = ?,
                    customer_image = ?
                    WHERE customer_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$name, $contact, $city, $country, $image, $id]);
        } else {
            // Update without changing the image
            $sql = "UPDATE customer SET 
                    customer_name = ?, 
                    customer_contact = ?, 
                    customer_city = ?, 
                    customer_country = ? 
                    WHERE customer_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$name, $contact, $city, $country, $id]);
        }
    }

    /**
     * Get customer details by ID
     */
    public function get_customer($customer_id)
    {
        $sql = "SELECT * FROM customer WHERE customer_id = '$customer_id'";
        return $this->db_fetch_one($sql);
    }
}
?>

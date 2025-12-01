<?php
// controllers/cart_controller.php

require_once("../classes/cart_class.php");

/**
 * Add services to cart
 * Automatically handles service-exists logic in cart_class
 */
function add_to_cart_ctr($product_id, $ip_add, $customer_id, $qty, $date = null, $time = null, $notes = null)
{
    $cart = new cart_class();
    return $cart->add_to_cart($product_id, $ip_add, $customer_id, $qty, $date, $time, $notes);
}

/**
 * Update quantity of an item already in the cart
 */
function update_cart_item_ctr($product_id, $customer_id, $qty)
{
    $cart = new cart_class();
    return $cart->update_cart_quantity($product_id, $customer_id, $qty);
}

/**
 * Remove a specific product from cart
 */
function remove_from_cart_ctr($p_id, $ip_add, $c_id) {
    require_once(__DIR__ . "/../classes/cart_class.php");
    $cart = new Cart();
    return $cart->remove_from_cart($p_id, $ip_add, $c_id);
}


/**
 * Retrieve all cart items for a given user
 */
function get_user_cart_ctr($identifier) {
    require_once(__DIR__ . "/../classes/cart_class.php");
    $cart = new cart_class();
    
    // Check if identifier is a customer_id (numeric) or IP address
    if (is_numeric($identifier)) {
        // Logged-in user
        return $cart->get_cart_by_customer($identifier);
    } else {
        // Guest user (IP address)
        return $cart->get_cart_by_ip($identifier);
    }
}
/**
 * Empty a customer’s entire cart
 */
function empty_cart_ctr($ip_add, $c_id) {
    require_once(__DIR__ . "/../classes/cart_class.php");
    $cart = new cart_class();
    return $cart->empty_cart($ip_add, $c_id);
}

/**
 * Get single item (useful for checking existence)
 */
function check_cart_item_ctr($product_id, $customer_id)
{
    $cart = new cart_class();
    return $cart->check_product_in_cart($product_id, $customer_id);
}

/**
 * Merge guest cart into user cart upon login
 */
function merge_guest_cart_on_login_ctr($customer_id, $ip_address) {
    require_once(__DIR__ . "/../classes/cart_class.php");
    $cart = new cart_class();
    return $cart->merge_guest_cart($customer_id, $ip_address);
}

?>

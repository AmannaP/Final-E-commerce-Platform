<?php
// settings/core.php

// Start session only if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    
    // Set session save path (important for some shared hosting)
    $session_path = __DIR__ . '/../sessions';
    session_start();
}

// For header redirection
ob_start();

/**
 * Redirect user to login page if not logged in
 * @return bool
 */
function checkLogin() {
   return isset($_SESSION['id']);
}

/**
 * Force login: redirect to login page if user not logged in
 */
function requireLogin(): void {
    if (!checkLogin()) {
        header("Location: ../login/login.php");
        exit;
    }
}

/**
 * Check if current user is an admin
 * @return bool
 */
function isAdmin() {

   return isset($_SESSION['role']) && (int)$_SESSION['role'] === 2;
}

/**
 * Force only admins to access a page redirect if not admin
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        header("Location: ../login/login.php");
        exit;
    }
}

/**
 * Get logged-in user ID
 * @return int|null
 */
function getUserId() {
    return isset($_SESSION['id']) ? $_SESSION['id'] : null;
}

/**
 * Get logged in user email
 */
function getUserEmail(){
    return isset($_SESSION['name']) ? $_SESSION['name'] : null;
}

/**
 * Get logged-in user role
 * @return string|null
 */
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

/**
 * Check if user has a specific role
 * @param string $role
 * @return bool
 */
function checkUserRole($role) {
    return (isset($_SESSION['role']) && $_SESSION['role'] === $role);
}


/**
 * Securely log out user
 */
function logout(): void {
    // Clear session data
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Redirect to login page
    header("Location: ../login/login.php");
    exit;
}

?>

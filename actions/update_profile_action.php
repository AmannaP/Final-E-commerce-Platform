<?php
// actions/update_profile_action.php
session_start();
require_once '../controllers/customer_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['id'];
$name = $_POST['full_name'] ?? '';
$contact = $_POST['phone_number'] ?? '';
$city = $_POST['city'] ?? '';
$country = $_POST['country'] ?? '';

// Basic validation
if (empty($name) || empty($contact)) {
    echo json_encode(['status' => 'error', 'message' => 'Name and Phone are required']);
    exit();
}

// IMAGE UPLOAD LOGIC
$image_name = null;

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $filename = $_FILES['profile_image']['name'];
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        // Create unique name
        $new_name = "user_" . $user_id . "_" . uniqid() . "." . $ext;
        $upload_dir = "../uploads/users/";

        // Create folder if not exists
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
            $image_name = $new_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload image. Check folder permissions.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, WEBP allowed.']);
        exit();
    }
}

// Update Database
$result = update_customer_ctr($user_id, $name, $contact, $city, $country, $image_name);

if ($result) {
    $_SESSION['name'] = $name; // Update session name
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
}
?>
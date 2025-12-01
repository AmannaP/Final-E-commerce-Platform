<?php
session_start();
require_once '../controllers/chat_controller.php';

$uid = $_SESSION['id'] ?? null;
$gid = $_POST['group_id'] ?? null;
$msg = $_POST['message'] ?? '';

if ($uid && $gid && !empty(trim($msg))) {
    add_message_ctr($gid, $uid, trim($msg));
    echo "success";
} else {
    echo "error";
}
?>
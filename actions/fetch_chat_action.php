<?php
session_start();
require_once '../controllers/chat_controller.php';

$gid = $_GET['group_id'] ?? null;
$current_user_id = $_SESSION['id'] ?? 0;

if (!$gid) exit();

$messages = get_messages_ctr($gid);

// Loop through messages and create HTML for each
foreach ($messages as $msg) {
    $is_me = ($msg['user_id'] == $current_user_id) ? 'sent' : 'received';
    $name = ($is_me == 'sent') ? 'You' : htmlspecialchars($msg['customer_name']);
    $time = date('h:i A', strtotime($msg['created_at']));
    $text = htmlspecialchars($msg['message']);

    echo "
    <div class='message $is_me'>
        <div class='msg-info'>
            <span class='msg-name'>$name</span>
            <span class='msg-time'>$time</span>
        </div>
        <div class='msg-text'>$text</div>
    </div>
    ";
}
?>
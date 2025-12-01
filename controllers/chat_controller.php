<?php
// controllers/chat_controller.php
require_once '../classes/chat_class.php';

function get_chat_groups_ctr() {
    $chat = new Chat();
    return $chat->get_all_groups();
}

function get_group_details_ctr($id) {
    $chat = new Chat();
    return $chat->get_group_details($id);
}

function add_message_ctr($group_id, $user_id, $msg) {
    $chat = new Chat();
    return $chat->add_message($group_id, $user_id, $msg);
}

function get_messages_ctr($group_id) {
    $chat = new Chat();
    return $chat->get_messages($group_id);
}
?>
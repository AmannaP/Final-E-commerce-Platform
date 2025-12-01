<?php
// controllers/awareness_controller.php
require_once '../classes/awareness_class.php';

function add_awareness_ctr($title, $content) {
    $awareness = new Awareness();
    return $awareness->add_awareness($title, $content);
}

function get_all_awareness_ctr() {
    $awareness = new Awareness();
    return $awareness->get_all_awareness();
}

function delete_awareness_ctr($id) {
    $awareness = new Awareness();
    return $awareness->delete_awareness($id);
}
?>
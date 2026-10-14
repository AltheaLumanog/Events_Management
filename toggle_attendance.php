<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    exit;
}

$id = (int) $_POST['id'];
$attended = $_POST['attended'] == '1' ? 1 : 0;

$stmt = $db->prepare('UPDATE registrations SET attended = ? WHERE id = ?');
$stmt->execute([$attended, $id]);

echo 'ok';

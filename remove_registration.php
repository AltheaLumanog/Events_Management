<?php
require 'config.php';
need_admin();

$event_id = (int) ($_POST['event_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $stmt = $db->prepare('DELETE FROM registrations WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: attendees.php?event_id=' . $event_id);
exit;

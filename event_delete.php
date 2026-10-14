<?php
require 'config.php';
need_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];
    $stmt = $db->prepare('DELETE FROM events WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: admin_events.php?msg=' . urlencode('Event deleted.'));
exit;

<?php
require 'config.php';
need_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int) $_POST['id'];

    $stmt = $db->prepare(
        'DELETE registrations FROM registrations
         JOIN events ON registrations.event_id = events.id
         WHERE registrations.id = ? AND registrations.user_id = ? AND events.event_date > NOW()'
    );
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header('Location: my_events.php?msg=' . urlencode('Registration cancelled.'));
exit;

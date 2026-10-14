<?php
require 'config.php';
need_admin();

$event_id = (int) ($_GET['event_id'] ?? 0);

$stmt = $db->prepare(
    'SELECT users.full_name, users.username, registrations.registered_at, registrations.attended
     FROM registrations JOIN users ON registrations.user_id = users.id
     WHERE registrations.event_id = ?
     ORDER BY users.full_name'
);
$stmt->execute([$event_id]);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendees_event_' . $event_id . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Full Name', 'Username', 'Registered On', 'Present']);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, [$row['full_name'], $row['username'], $row['registered_at'], $row['attended'] ? 'Yes' : 'No']);
}
fclose($out);

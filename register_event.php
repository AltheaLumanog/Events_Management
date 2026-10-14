<?php
require 'config.php';
header('Content-Type: application/json');

function fail($db, $message) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['ok' => false, 'message' => $message]);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    fail($db, 'Please login with a user account to register.');
}

$event_id = (int) ($_POST['event_id'] ?? 0);
$user_id = $_SESSION['user_id'];

$db->beginTransaction();

$stmt = $db->prepare('SELECT * FROM events WHERE id = ? FOR UPDATE');
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    fail($db, 'Event not found.');
}
if (strtotime($event['event_date']) < time()) {
    fail($db, 'This event has already ended.');
}

$stmt = $db->prepare('SELECT COUNT(*) FROM registrations WHERE event_id = ?');
$stmt->execute([$event_id]);
$registered = $stmt->fetchColumn();

if ($registered >= $event['capacity']) {
    fail($db, 'Sorry, this event is fully booked.');
}

$stmt = $db->prepare('SELECT id FROM registrations WHERE user_id = ? AND event_id = ?');
$stmt->execute([$user_id, $event_id]);
if ($stmt->fetch()) {
    fail($db, 'You are already registered.');
}

$stmt = $db->prepare('INSERT INTO registrations (user_id, event_id) VALUES (?, ?)');
$stmt->execute([$user_id, $event_id]);
$db->commit();

$registered++;
echo json_encode([
    'ok' => true,
    'message' => 'You are registered! See you there.',
    'seats_left' => $event['capacity'] - $registered,
    'percent' => percent_full($registered, $event['capacity'])
]);

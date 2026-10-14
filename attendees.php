<?php
require 'config.php';
need_admin();

$event_id = (int) ($_GET['event_id'] ?? 0);

$stmt = $db->prepare('SELECT * FROM events WHERE id = ?');
$stmt->execute([$event_id]);
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ev) {
    header('Location: admin_events.php');
    exit;
}

$stmt = $db->prepare(
    'SELECT registrations.*, users.full_name, users.username
     FROM registrations JOIN users ON registrations.user_id = users.id
     WHERE registrations.event_id = ?
     ORDER BY registrations.registered_at'
);
$stmt->execute([$event_id]);
$people = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <a href="admin_events.php">&larr; Back to events</a>

    <div class="d-flex flex-wrap justify-content-between align-items-center my-3">
        <div>
            <h3 class="mb-0"><?= e($ev['title']) ?></h3>
            <small class="text-muted"><?= e(format_date($ev['event_date'])) ?> - <?= count($people) ?> / <?= $ev['capacity'] ?> registered</small>
        </div>
        <a href="export_attendees.php?event_id=<?= $ev['id'] ?>" class="btn btn-outline-success"><i class="bi bi-download"></i> Download CSV</a>
    </div>

    <div class="card event-card">
    <div class="table-responsive">
    <table class="table mb-0 align-middle">
        <thead class="table-light">
            <tr><th>Present</th><th>Name</th><th>Username</th><th>Registered on</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (count($people) == 0): ?>
            <tr><td colspan="5" class="text-center py-3">Nobody has registered yet.</td></tr>
        <?php endif; ?>

        <?php foreach ($people as $p): ?>
            <tr>
                <td><input type="checkbox" class="form-check-input attend-box" data-id="<?= $p['id'] ?>" <?= $p['attended'] ? 'checked' : '' ?>></td>
                <td><?= e($p['full_name']) ?></td>
                <td><?= e($p['username']) ?></td>
                <td><?= e(date('M j, Y g:i A', strtotime($p['registered_at']))) ?></td>
                <td>
                    <form method="post" action="remove_registration.php" onsubmit="return confirm('Remove this person from the event?');">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="event_id" value="<?= $event_id ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>
</div>

<?php include 'footer.php'; ?>

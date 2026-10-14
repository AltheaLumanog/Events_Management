<?php
require 'config.php';
need_login();

if ($_SESSION['role'] == 'admin') {
    header('Location: admin_events.php');
    exit;
}

$stmt = $db->prepare(
    'SELECT registrations.id AS reg_id, registrations.attended, events.*
     FROM registrations JOIN events ON registrations.event_id = events.id
     WHERE registrations.user_id = ?
     ORDER BY events.event_date DESC'
);
$stmt->execute([$_SESSION['user_id']]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <h3>My Registrations</h3>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= e($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card event-card">
    <div class="table-responsive">
    <table class="table mb-0 align-middle">
        <thead class="table-light">
            <tr><th>Event</th><th>Date</th><th>Venue</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (count($list) == 0): ?>
            <tr><td colspan="5" class="text-center py-3">You have not registered for any event. <a href="index.php">Browse events</a></td></tr>
        <?php endif; ?>

        <?php foreach ($list as $row): ?>
            <?php $past = strtotime($row['event_date']) < time(); ?>
            <tr>
                <td><a href="event.php?id=<?= $row['id'] ?>"><?= e($row['title']) ?></a></td>
                <td><?= e(format_date($row['event_date'])) ?></td>
                <td><?= e($row['venue']) ?></td>
                <td>
                    <?php if (!$past): ?>
                        <span class="badge bg-primary">Upcoming</span>
                    <?php elseif ($row['attended']): ?>
                        <span class="badge bg-success">Attended</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Done</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$past): ?>
                        <form method="post" action="cancel_registration.php" onsubmit="return confirm('Cancel your registration?');">
                            <input type="hidden" name="id" value="<?= $row['reg_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php
// admin_events.php - organizer sees ALL events (Read)
require 'config.php';
need_admin();

$events = $db->query(
    'SELECT events.*,
            (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.id) AS registered
     FROM events ORDER BY event_date DESC'
)->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Manage Events</h3>
        <a href="event_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Event</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= e($_GET['msg']) ?></div>
    <?php endif; ?>

    <div class="card event-card">
    <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
            <tr><th>Event</th><th>Date</th><th>Venue</th><th>Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
            <tr>
                <td>
                    <?= e($ev['title']) ?>
                    <span class="badge bg-secondary"><?= e($ev['category']) ?></span>
                    <?php if (strtotime($ev['event_date']) < time()): ?>
                        <span class="badge bg-dark">past</span>
                    <?php endif; ?>
                </td>
                <td><?= e(format_date($ev['event_date'])) ?></td>
                <td><?= e($ev['venue']) ?></td>
                <td><?= $ev['registered'] ?> / <?= $ev['capacity'] ?></td>
                <td class="text-nowrap">
                    <a href="attendees.php?event_id=<?= $ev['id'] ?>" class="btn btn-sm btn-info">Attendees</a>
                    <a href="event_form.php?id=<?= $ev['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="post" action="event_delete.php" class="d-inline" onsubmit="return confirm('Delete this event and all its registrations?');">
                        <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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

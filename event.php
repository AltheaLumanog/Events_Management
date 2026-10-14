<?php
require 'config.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $db->prepare(
    'SELECT events.*,
            (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.id) AS registered
     FROM events WHERE id = ?'
);
$stmt->execute([$id]);
$ev = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ev) {
    header('Location: index.php');
    exit;
}

$left = $ev['capacity'] - $ev['registered'];
$percent = percent_full($ev['registered'], $ev['capacity']);
$is_past = strtotime($ev['event_date']) < time();

$already = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare('SELECT id FROM registrations WHERE user_id = ? AND event_id = ?');
    $stmt->execute([$_SESSION['user_id'], $id]);
    $already = $stmt->fetch() ? true : false;
}

include 'header.php';
?>

<div class="container mt-4">
    <a href="index.php">&larr; Back to events</a>

    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card event-card mb-3">
                <div class="card-body p-4">
                    <span class="badge bg-secondary"><?= e($ev['category']) ?></span>
                    <h2 class="mt-2"><?= e($ev['title']) ?></h2>
                    <p class="mb-1"><i class="bi bi-calendar3"></i> <?= e(format_date($ev['event_date'])) ?></p>
                    <p><i class="bi bi-geo-alt"></i> <?= e($ev['venue']) ?></p>
                    <hr>
                    <h5>About this event</h5>
                    <p><?= nl2br(e($ev['description'])) ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card event-card">
                <div class="card-body p-4">
                    <h5>Seats</h5>
                    <div class="progress mb-1" style="height: 10px;">
                        <div class="progress-bar <?= bar_color($percent) ?>" id="seatBar" style="width: <?= $percent ?>%"></div>
                    </div>
                    <p class="mb-3"><b id="seatsLeft"><?= max(0, $left) ?></b> of <?= $ev['capacity'] ?> seats left</p>

                    <div id="message"></div>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn btn-primary w-100">Login to register</a>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <a href="attendees.php?event_id=<?= $ev['id'] ?>" class="btn btn-outline-primary w-100">View attendees</a>
                    <?php elseif ($is_past): ?>
                        <button class="btn btn-secondary w-100" disabled>This event has ended</button>
                    <?php elseif ($already): ?>
                        <button class="btn btn-success w-100" disabled>You are registered</button>
                    <?php elseif ($left < 1): ?>
                        <button class="btn btn-secondary w-100" disabled>Fully booked</button>
                    <?php else: ?>
                        <button class="btn btn-primary w-100" id="registerBtn" data-id="<?= $ev['id'] ?>">Register now</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

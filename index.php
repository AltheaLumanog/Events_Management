<?php
require 'config.php';

$category = $_GET['category'] ?? 'all';

$sql = 'SELECT events.*,
               (SELECT COUNT(*) FROM registrations WHERE registrations.event_id = events.id) AS registered
        FROM events
        WHERE event_date >= NOW()';
$params = [];

if (in_array($category, ['Seminar', 'Workshop', 'Sports', 'Social'])) {
    $sql .= ' AND category = ?';
    $params[] = $category;
}
$sql .= ' ORDER BY event_date';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="hero">
    <div class="container">
        <h1 class="fw-bold">Find and join campus events</h1>
        <p class="lead mb-4">Seminars, workshops, sports and parties. Reserve your slot in a few clicks.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-warning btn-lg">Create a free account</a>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-4">

    <div class="row g-2 mb-4">
        <div class="col-12 col-md-8">
            <input type="text" id="searchBox" class="form-control" placeholder="Search events by title or venue...">
        </div>
        <div class="col-12 col-md-4">
            <form method="get">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="all">All categories</option>
                    <?php foreach (['Seminar', 'Workshop', 'Sports', 'Social'] as $c): ?>
                        <option <?= $category == $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if (count($events) == 0): ?>
        <div class="alert alert-info">No upcoming events right now. Please check back later.</div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="eventList">
    <?php foreach ($events as $ev): ?>
        <?php
            $percent = percent_full($ev['registered'], $ev['capacity']);
            $left = $ev['capacity'] - $ev['registered'];
        ?>
        <div class="col event-item">
            <div class="card event-card h-100">
                <div class="card-body">
                    <div class="d-flex gap-3 mb-3">
                        <div class="date-badge">
                            <div class="month"><?= date('M', strtotime($ev['event_date'])) ?></div>
                            <div class="day"><?= date('j', strtotime($ev['event_date'])) ?></div>
                        </div>
                        <div>
                            <span class="badge bg-secondary"><?= e($ev['category']) ?></span>
                            <h5 class="mt-1 mb-0 event-title"><?= e($ev['title']) ?></h5>
                        </div>
                    </div>
                    <p class="mb-1 text-muted"><i class="bi bi-clock"></i> <?= e(date('g:i A', strtotime($ev['event_date']))) ?></p>
                    <p class="mb-3 text-muted event-venue"><i class="bi bi-geo-alt"></i> <?= e($ev['venue']) ?></p>

                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?= bar_color($percent) ?>" style="width: <?= $percent ?>%"></div>
                    </div>
                    <small class="text-muted">
                        <?= $left > 0 ? $left . ' of ' . $ev['capacity'] . ' seats left' : 'Fully booked' ?>
                    </small>
                </div>
                <div class="card-footer bg-white border-0 pb-3">
                    <a href="event.php?id=<?= $ev['id'] ?>" class="btn btn-outline-primary w-100">View details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

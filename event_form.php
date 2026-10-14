<?php
require 'config.php';
need_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$categories = ['Seminar', 'Workshop', 'Sports', 'Social'];
$ev = ['title' => '', 'category' => 'Seminar', 'event_date' => '', 'venue' => '', 'capacity' => 50, 'description' => ''];

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $ev = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ev) {
        header('Location: admin_events.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ev = [
        'title'       => trim($_POST['title']),
        'category'    => $_POST['category'],
        'event_date'  => str_replace('T', ' ', $_POST['event_date']),   // from datetime-local
        'venue'       => trim($_POST['venue']),
        'capacity'    => (int) $_POST['capacity'],
        'description' => trim($_POST['description']),
    ];

    // how many people already registered (capacity can't go below this)
    $stmt = $db->prepare('SELECT COUNT(*) FROM registrations WHERE event_id = ?');
    $stmt->execute([$id]);
    $registered = $stmt->fetchColumn();

    $date_ok = DateTime::createFromFormat('Y-m-d H:i', $ev['event_date']);

    // server side validation
    if ($ev['title'] == '' || strlen($ev['title']) > 100) {
        $error = 'Title is required (max 100 characters).';
    } elseif (!in_array($ev['category'], $categories)) {
        $error = 'Invalid category.';
    } elseif (!$date_ok) {
        $error = 'Please enter a valid date and time.';
    } elseif ($ev['venue'] == '' || strlen($ev['venue']) > 100) {
        $error = 'Venue is required (max 100 characters).';
    } elseif ($ev['capacity'] < 1 || $ev['capacity'] > 1000) {
        $error = 'Capacity must be from 1 to 1000.';
    } elseif ($ev['capacity'] < $registered) {
        $error = "Capacity can't be lower than the $registered people already registered.";
    } elseif (strlen($ev['description']) > 1000) {
        $error = 'Description is too long (max 1000 characters).';
    } else {
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE events SET title = ?, category = ?, event_date = ?, venue = ?, capacity = ?, description = ? WHERE id = ?');
            $stmt->execute([$ev['title'], $ev['category'], $ev['event_date'], $ev['venue'], $ev['capacity'], $ev['description'], $id]);
            $msg = 'Event updated.';
        } else {
            $stmt = $db->prepare('INSERT INTO events (title, category, event_date, venue, capacity, description) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$ev['title'], $ev['category'], $ev['event_date'], $ev['venue'], $ev['capacity'], $ev['description']]);
            $msg = 'Event created.';
        }
        header('Location: admin_events.php?msg=' . urlencode($msg));
        exit;
    }
}

// datetime-local needs the format 2026-10-25T09:00
$date_value = $ev['event_date'] ? date('Y-m-d\TH:i', strtotime($ev['event_date'])) : '';

include 'header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h3><?= $id > 0 ? 'Edit Event' : 'New Event' ?></h3>

            <div class="card event-card"><div class="card-body p-4">

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= e($ev['title']) ?>" maxlength="100" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <?php foreach ($categories as $c): ?>
                                    <option <?= $ev['category'] == $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date and Time</label>
                            <input type="datetime-local" name="event_date" class="form-control" value="<?= e($date_value) ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" class="form-control" value="<?= e($ev['venue']) ?>" maxlength="100" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="<?= e($ev['capacity']) ?>" min="1" max="1000" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" maxlength="1000"><?= e($ev['description']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="admin_events.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div></div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

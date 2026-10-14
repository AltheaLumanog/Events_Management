<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campus Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark navbar-purple">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-calendar-event"></i> Campus Events</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Events</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin_events.php">Manage Events</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="my_events.php">My Registrations</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><span class="navbar-text me-3">
                        <i class="bi bi-person-circle"></i> <?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?><span class="badge bg-warning text-dark">organizer</span><?php endif; ?>
                    </span></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-md-2" href="register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

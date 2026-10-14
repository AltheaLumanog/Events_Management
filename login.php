<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header('Location: admin_events.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Wrong username or password.';
    }
}

include 'header.php';
?>

<div class="form-box px-3">
    <div class="card event-card">
        <div class="card-body p-4">
            <h4>Login</h4>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Account created! Please login.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="mt-3 mb-0 text-center">New here? <a href="register.php">Sign up</a></p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

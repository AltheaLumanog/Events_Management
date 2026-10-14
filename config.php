<?php

$host = 'localhost';
$dbname = 'events_db';
$dbuser = 'root';
$dbpass = '';


session_name('events_session');
session_start();

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed. Check config.php');
}

function e($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function need_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function need_admin() {
    need_login();
    if ($_SESSION['role'] != 'admin') {
        die('Access denied. Admins only.');
    }
}

function format_date($date) {
    return date('D, M j, Y \a\t g:i A', strtotime($date));
}

function percent_full($registered, $capacity) {
    if ($capacity <= 0) {
        return 100;
    }
    return min(100, round($registered / $capacity * 100));
}

function bar_color($percent) {
    if ($percent >= 100) {
        return 'bg-danger';
    } elseif ($percent >= 70) {
        return 'bg-warning';
    }
    return 'bg-success';
}

<?php
session_name('events_session');
session_start();
session_destroy();
header('Location: login.php');
exit;

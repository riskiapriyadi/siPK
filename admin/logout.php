<?php
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $_SESSION = [];
    session_destroy();
}

header('Location: login.php');
exit;

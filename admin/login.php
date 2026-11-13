<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Sesi tidak valid, muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $stmt = $koneksi->prepare("SELECT id, nama, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_nama'] = $user['nama'];
            header('Location: index.php');
            exit;
        }

        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin - SPK AC</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login">
    <h1>Login admin</h1>
    <div class="card">
        <?php if ($error): ?>
            <div class="error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>

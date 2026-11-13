<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> - Admin SPK AC</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="kriteria.php">Kriteria</a>
    <a href="alternatif.php">Alternatif</a>
    <a href="riwayat.php">Riwayat</a>
    <form method="post" action="logout.php">
        <?= csrf_field() ?>
        <button type="submit">Keluar</button>
    </form>
</nav>
<main>
<?php if ($pesan = ambil_flash()): ?>
    <div class="ok"><?= e($pesan) ?></div>
<?php endif; ?>
<h1><?= e($judul) ?></h1>

<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
wajib_login();

$jml_alternatif = (int) $koneksi->query("SELECT COUNT(*) FROM alternatif")->fetch_row()[0];
$jml_konsultasi = (int) $koneksi->query("SELECT COUNT(*) FROM konsultasi")->fetch_row()[0];
$total_bobot    = round((float) $koneksi->query("SELECT COALESCE(SUM(bobot), 0) FROM kriteria")->fetch_row()[0], 2);

$judul = 'Dashboard';
require __DIR__ . '/_header.php';
?>
<p>Halo, <?= e($_SESSION['admin_nama'] ?? 'Admin') ?>.</p>

<div class="stats">
    <div class="card"><b><?= $jml_alternatif ?></b><span>Alternatif PK</span></div>
    <div class="card"><b><?= $jml_konsultasi ?></b><span>Konsultasi masuk</span></div>
    <div class="card"><b><?= angka($total_bobot, 2) ?></b><span>Total bobot kriteria</span></div>
</div>

<?php if (abs($total_bobot - 1) > 0.001): ?>
    <div class="warn">Total bobot belum 1,00. Perbaiki di menu Kriteria agar hasil perhitungan valid.</div>
<?php endif; ?>

<div class="card">
    <p><a href="kriteria.php">Kriteria</a>: atur bobot penilaian.</p>
    <p><a href="alternatif.php">Alternatif</a>: kelola pilihan PK, daya, dan harga.</p>
    <p><a href="riwayat.php">Riwayat</a>: lihat konsultasi yang masuk.</p>
</div>
<?php require __DIR__ . '/_footer.php'; ?>

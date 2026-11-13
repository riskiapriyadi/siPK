<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
require __DIR__ . '/../includes/saw.php';
wajib_login();

$riwayat = $koneksi->query(
    "SELECT luas, matahari, kebutuhan_btu, rekomendasi, skor, tanggal
     FROM konsultasi ORDER BY id DESC LIMIT 50"
)->fetch_all(MYSQLI_ASSOC);

$judul = 'Riwayat konsultasi';
require __DIR__ . '/_header.php';
?>
<div class="card tabel">
    <?php if (!$riwayat): ?>
        <p>Belum ada konsultasi.</p>
    <?php else: ?>
        <table>
            <tr><th>Tanggal</th><th>Luas (m²)</th><th>Matahari</th><th>Kebutuhan</th><th>Rekomendasi</th><th>Skor</th></tr>
            <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td><?= e($r['tanggal']) ?></td>
                    <td><?= angka((float) $r['luas'], 1) ?></td>
                    <td><?= e(LABEL_MATAHARI[$r['matahari']] ?? $r['matahari']) ?></td>
                    <td><?= angka((float) $r['kebutuhan_btu']) ?> BTU</td>
                    <td><?= e($r['rekomendasi']) ?></td>
                    <td><?= angka((float) $r['skor'], 3) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p class="catatan">Menampilkan 50 konsultasi terbaru.</p>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/_footer.php'; ?>

<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
wajib_login();

$rows  = $koneksi->query("SELECT id, kode, nama, bobot, tipe FROM kriteria ORDER BY kode")->fetch_all(MYSQLI_ASSOC);
$error = '';
$baru  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Sesi tidak valid, muat ulang halaman.';
    } else {
        $input = is_array($_POST['bobot'] ?? null) ? $_POST['bobot'] : [];
        $total = 0.0;
        $valid = true;

        foreach ($rows as $r) {
            $v = round((float) str_replace(',', '.', (string) ($input[$r['id']] ?? '')), 2);
            $baru[$r['id']] = $v;
            if ($v < 0 || $v > 1) {
                $valid = false;
            }
            $total += $v;
        }

        if (!$valid) {
            $error = 'Setiap bobot harus antara 0 dan 1.';
        } elseif (abs($total - 1) > 0.001) {
            $error = 'Total bobot harus 1,00 (sekarang ' . angka($total, 2) . ').';
        } else {
            $stmt = $koneksi->prepare("UPDATE kriteria SET bobot = ? WHERE id = ?");
            foreach ($baru as $id => $v) {
                $id = (int) $id;
                $stmt->bind_param('di', $v, $id);
                $stmt->execute();
            }
            set_flash('Bobot kriteria disimpan.');
            header('Location: kriteria.php');
            exit;
        }
    }
}

$judul = 'Kriteria dan bobot';
require __DIR__ . '/_header.php';
?>
<?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" class="card">
    <?= csrf_field() ?>
    <div class="tabel">
        <table>
            <tr><th>Kode</th><th>Kriteria</th><th>Tipe</th><th>Bobot</th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['kode']) ?></td>
                    <td><?= e($r['nama']) ?></td>
                    <td><?= e($r['tipe']) ?></td>
                    <td>
                        <input type="text" inputmode="decimal" name="bobot[<?= (int) $r['id'] ?>]"
                               value="<?= e(angka($baru[$r['id']] ?? (float) $r['bobot'], 2)) ?>" style="width:90px">
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <p class="catatan">Total bobot harus tepat 1,00. Kode dan tipe kriteria bersifat tetap karena rumus penilaian ada di includes/saw.php.</p>
    <button type="submit">Simpan bobot</button>
</form>
<?php require __DIR__ . '/_footer.php'; ?>

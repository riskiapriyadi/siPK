<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/saw.php';

$hasil    = null;
$error    = '';
$luas     = '';
$matahari = 'sore';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $luas     = (float) str_replace(',', '.', $_POST['luas'] ?? '0');
    $matahari = $_POST['matahari'] ?? 'tidak';

    if ($luas < 1 || $luas > 200) {
        $error = 'Masukkan luas ruangan antara 1 dan 200 m².';
    } elseif (!isset(KOEFISIEN_MATAHARI[$matahari])) {
        $error = 'Pilihan paparan matahari tidak valid.';
    } else {
        $hasil = hitung_saw($koneksi, $luas, $matahari);

        if ($hasil['hasil']) {
            $top       = $hasil['hasil'][0];
            $kebutuhan = (int) round($hasil['kebutuhan_btu']);
            $nama_pk   = $top['nama_pk'];
            $skor      = $top['skor'];

            $stmt = $koneksi->prepare(
                "INSERT INTO konsultasi (luas, matahari, kebutuhan_btu, rekomendasi, skor)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('dsisd', $luas, $matahari, $kebutuhan, $nama_pk, $skor);
            $stmt->execute();
        }
    }
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SPK Pemilihan PK AC</title>
<style>
    body { font-family: system-ui, sans-serif; max-width: 520px; margin: 0 auto; padding: 16px; color: #222; }
    label { display: block; font-size: 14px; margin: 12px 0 4px; color: #555; }
    input, select, button { width: 100%; box-sizing: border-box; padding: 10px; font-size: 16px; }
    button { margin-top: 16px; background: #222; color: #fff; border: 0; border-radius: 6px; }
    .error { background: #fdecea; color: #a32d2d; padding: 10px; border-radius: 6px; margin-top: 12px; }
    .rekomendasi { background: #eaf3de; color: #27500a; padding: 14px; border-radius: 8px; margin: 20px 0 12px; }
    .rekomendasi strong { font-size: 22px; display: block; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { text-align: left; padding: 8px 6px; border-bottom: 1px solid #ddd; }
</style>
</head>
<body>
<h1>SPK pemilihan PK AC</h1>

<form method="post">
    <label for="luas">Luas ruangan (m²)</label>
    <input type="text" id="luas" name="luas" inputmode="decimal" value="<?= e((string) $luas) ?>" placeholder="12" required>

    <label for="matahari">Paparan sinar matahari</label>
    <select id="matahari" name="matahari">
        <?php foreach (LABEL_MATAHARI as $kode => $label): ?>
            <option value="<?= e($kode) ?>" <?= $kode === $matahari ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Hitung rekomendasi</button>
</form>

<?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<?php if ($hasil && $hasil['hasil']): ?>
    <?php $top = $hasil['hasil'][0]; ?>
    <div class="rekomendasi">
        Kebutuhan pendinginan: <?= number_format($hasil['kebutuhan_btu'], 0, ',', '.') ?> BTU
        <strong><?= e($top['nama_pk']) ?></strong>
        Skor <?= number_format($top['skor'], 3, ',', '.') ?>
    </div>

    <table>
        <tr><th>#</th><th>Alternatif</th><th>BTU</th><th>Rasio</th><th>Skor</th></tr>
        <?php foreach ($hasil['hasil'] as $i => $a): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($a['nama_pk']) ?></td>
                <td><?= number_format($a['btu'], 0, ',', '.') ?></td>
                <td><?= number_format($a['rasio'], 2, ',', '.') ?></td>
                <td><?= number_format($a['skor'], 3, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>

<?php
require __DIR__ . '/../config/koneksi.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/helpers.php';
wajib_login();

$error = '';
$form  = ['id' => 0, 'kode' => '', 'nama_pk' => '', 'btu' => '', 'daya_watt' => '', 'harga' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Sesi tidak valid, muat ulang halaman.';
    } else {
        $aksi = $_POST['aksi'] ?? '';

        if ($aksi === 'hapus') {
            $id   = (int) ($_POST['id'] ?? 0);
            $stmt = $koneksi->prepare("DELETE FROM alternatif WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            set_flash('Alternatif dihapus.');
            header('Location: alternatif.php');
            exit;
        }

        if ($aksi === 'simpan') {
            $form = [
                'id'        => (int) ($_POST['id'] ?? 0),
                'kode'      => trim((string) ($_POST['kode'] ?? '')),
                'nama_pk'   => trim((string) ($_POST['nama_pk'] ?? '')),
                'btu'       => (int) ($_POST['btu'] ?? 0),
                'daya_watt' => (int) ($_POST['daya_watt'] ?? 0),
                'harga'     => (int) ($_POST['harga'] ?? 0),
            ];

            if ($form['kode'] === '' || $form['nama_pk'] === ''
                || mb_strlen($form['kode']) > 5 || mb_strlen($form['nama_pk']) > 30) {
                $error = 'Kode (maks. 5 karakter) dan nama (maks. 30 karakter) wajib diisi.';
            } elseif ($form['btu'] <= 0 || $form['daya_watt'] <= 0 || $form['harga'] <= 0) {
                $error = 'BTU, daya, dan harga harus berupa angka lebih dari 0.';
            } else {
                try {
                    if ($form['id'] > 0) {
                        $stmt = $koneksi->prepare(
                            "UPDATE alternatif SET kode = ?, nama_pk = ?, btu = ?, daya_watt = ?, harga = ? WHERE id = ?"
                        );
                        $stmt->bind_param('ssiiii', $form['kode'], $form['nama_pk'], $form['btu'],
                            $form['daya_watt'], $form['harga'], $form['id']);
                    } else {
                        $stmt = $koneksi->prepare(
                            "INSERT INTO alternatif (kode, nama_pk, btu, daya_watt, harga) VALUES (?, ?, ?, ?, ?)"
                        );
                        $stmt->bind_param('ssiii', $form['kode'], $form['nama_pk'], $form['btu'],
                            $form['daya_watt'], $form['harga']);
                    }
                    $stmt->execute();
                    set_flash('Alternatif disimpan.');
                    header('Location: alternatif.php');
                    exit;
                } catch (mysqli_sql_exception $ex) {
                    $error = $ex->getCode() === 1062 ? 'Kode sudah dipakai alternatif lain.' : 'Gagal menyimpan data.';
                }
            }
        }
    }
} elseif (isset($_GET['ubah'])) {
    $id   = (int) $_GET['ubah'];
    $stmt = $koneksi->prepare("SELECT id, kode, nama_pk, btu, daya_watt, harga FROM alternatif WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if ($row) {
        $form = $row;
    }
}

$daftar = $koneksi->query("SELECT id, kode, nama_pk, btu, daya_watt, harga FROM alternatif ORDER BY btu")->fetch_all(MYSQLI_ASSOC);

$judul = 'Data alternatif';
require __DIR__ . '/_header.php';
?>
<?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>
<?php endif; ?>

<div class="card tabel">
    <table>
        <tr><th>Kode</th><th>Alternatif</th><th>BTU</th><th>Daya (W)</th><th>Harga (Rp)</th><th></th></tr>
        <?php foreach ($daftar as $a): ?>
            <tr>
                <td><?= e($a['kode']) ?></td>
                <td><?= e($a['nama_pk']) ?></td>
                <td><?= angka((float) $a['btu']) ?></td>
                <td><?= angka((float) $a['daya_watt']) ?></td>
                <td><?= angka((float) $a['harga']) ?></td>
                <td>
                    <a href="alternatif.php?ubah=<?= (int) $a['id'] ?>">Ubah</a>
                    <form method="post" onsubmit="return confirm('Hapus alternatif ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                        <button type="submit" class="hapus">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<form method="post" class="card">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="simpan">
    <input type="hidden" name="id" value="<?= (int) $form['id'] ?>">
    <strong><?= $form['id'] > 0 ? 'Ubah alternatif' : 'Tambah alternatif' ?></strong>

    <label for="kode">Kode</label>
    <input type="text" id="kode" name="kode" maxlength="5" value="<?= e((string) $form['kode']) ?>" required>
    <label for="nama_pk">Nama</label>
    <input type="text" id="nama_pk" name="nama_pk" maxlength="30" value="<?= e((string) $form['nama_pk']) ?>" required>
    <label for="btu">Kapasitas (BTU/jam)</label>
    <input type="number" id="btu" name="btu" min="1" value="<?= e((string) $form['btu']) ?>" required>
    <label for="daya_watt">Daya listrik (watt)</label>
    <input type="number" id="daya_watt" name="daya_watt" min="1" value="<?= e((string) $form['daya_watt']) ?>" required>
    <label for="harga">Harga (Rp)</label>
    <input type="number" id="harga" name="harga" min="1" value="<?= e((string) $form['harga']) ?>" required>

    <button type="submit">Simpan</button>
    <?php if ($form['id'] > 0): ?>
        <p><a href="alternatif.php">Batal ubah</a></p>
    <?php endif; ?>
</form>
<?php require __DIR__ . '/_footer.php'; ?>

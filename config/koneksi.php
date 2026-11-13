<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'spk_ac';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($host, $user, $pass, $db);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Koneksi database gagal. Periksa pengaturan di config/koneksi.php.');
}

<?php

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function angka(float $n, int $desimal = 0): string
{
    return number_format($n, $desimal, ',', '.');
}

function set_flash(string $pesan): void
{
    $_SESSION['flash'] = $pesan;
}

function ambil_flash(): string
{
    $pesan = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $pesan;
}

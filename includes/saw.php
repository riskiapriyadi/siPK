<?php

const KOEFISIEN_MATAHARI = [
    'tidak'    => 500,
    'pagi'     => 550,
    'sore'     => 600,
    'seharian' => 700,
];

const LABEL_MATAHARI = [
    'tidak'    => 'Tidak terkena matahari',
    'pagi'     => 'Pagi hari',
    'sore'     => 'Sore hari',
    'seharian' => 'Seharian',
];

function kebutuhan_btu(float $luas, string $matahari): float
{
    return $luas * (KOEFISIEN_MATAHARI[$matahari] ?? 500);
}

function skor_kecocokan(float $btu_pk, float $kebutuhan): int
{
    $rasio = round($btu_pk / $kebutuhan, 2);

    if ($rasio >= 1.00 && $rasio <= 1.30) return 5;
    if ($rasio >= 0.90 && $rasio <  1.00) return 4;
    if ($rasio >  1.30 && $rasio <= 1.70) return 3;
    if ($rasio >= 0.70 && $rasio <  0.90) return 2;
    return 1;
}

function hitung_saw(mysqli $db, float $luas, string $matahari): array
{
    $kebutuhan = kebutuhan_btu($luas, $matahari);

    $kriteria = [];
    $q = $db->query("SELECT kode, bobot, tipe FROM kriteria ORDER BY kode");
    while ($r = $q->fetch_assoc()) {
        $kriteria[$r['kode']] = ['bobot' => (float) $r['bobot'], 'tipe' => $r['tipe']];
    }

    $alternatif = [];
    $q = $db->query("SELECT kode, nama_pk, btu, daya_watt, harga FROM alternatif ORDER BY btu");
    while ($r = $q->fetch_assoc()) {
        $r['rasio'] = round($r['btu'] / $kebutuhan, 2);
        $r['nilai'] = [
            'C1' => skor_kecocokan((float) $r['btu'], $kebutuhan),
            'C2' => (float) $r['daya_watt'],
            'C3' => (float) $r['harga'],
        ];
        $alternatif[] = $r;
    }

    if (!$alternatif || !$kriteria) {
        return ['kebutuhan_btu' => $kebutuhan, 'hasil' => []];
    }

    $min = [];
    $max = [];
    foreach ($kriteria as $kode => $k) {
        $kolom = array_column(array_column($alternatif, 'nilai'), $kode);
        $min[$kode] = min($kolom);
        $max[$kode] = max($kolom);
    }

    foreach ($alternatif as &$a) {
        $skor = 0.0;
        $a['normal'] = [];

        foreach ($kriteria as $kode => $k) {
            $x = $a['nilai'][$kode];

            if ($k['tipe'] === 'benefit') {
                $r = $max[$kode] > 0 ? $x / $max[$kode] : 0;
            } else {
                $r = $x > 0 ? $min[$kode] / $x : 0;
            }

            $a['normal'][$kode] = $r;
            $skor += $k['bobot'] * $r;
        }

        $a['skor'] = $skor;
    }
    unset($a);

    usort($alternatif, fn($p, $q) => $q['skor'] <=> $p['skor']);

    return ['kebutuhan_btu' => $kebutuhan, 'hasil' => $alternatif];
}

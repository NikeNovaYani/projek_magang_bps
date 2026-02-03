<?php
session_start();

/* =========================
   UTIL FORMAT TANGGAL
========================= */
$bulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

function formatTanggalIndo($date)
{
    global $bulan;
    if (!$date) return '';
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[date('F', $ts)] . ' ' . date('Y', $ts);
}

/* =========================
   AMBIL DATA DARI FORM
========================= */
$data = [
    'unit_kerja'    => $_POST['unit_kerja'] ?? '',
    'tanggal'       => $_POST['tanggal'] ?? '',
    'tanggal_fmt'   => formatTanggalIndo($_POST['tanggal'] ?? ''),
    'pimpinan'      => $_POST['pimpinan'] ?? '',
    'pukul_mulai'   => $_POST['pukul_mulai'] ?? '',
    'pukul_selesai' => $_POST['pukul_selesai'] ?? '',
    'topik'         => $_POST['topik'] ?? '',
    'tempat'        => $_POST['tempat'] ?? '',
    'lampiran'      => $_POST['lampiran'] ?? '',
    'peserta'       => $_POST['peserta'] ?? '',
    'agenda'        => $_POST['agenda'] ?? '',

    // TinyMCE (HTML – jangan di htmlspecialchars!)
    'pembukaan'     => $_POST['pembukaan'] ?? '',
    'pembahasan'    => $_POST['pembahasan'] ?? '',
    'kesimpulan'    => $_POST['kesimpulan'] ?? '',
];

/* =========================
   SIMPAN KE SESSION
========================= */
$_SESSION['notulensi'] = $data;

echo 'OK';

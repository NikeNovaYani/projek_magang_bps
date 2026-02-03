<?php
session_start();

// 1. Cek Metode Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Method Not Allowed";
    exit;
}

// 2. Ambil Data (Mapping dari nama input 'f_...' ke nama array session yang bersih)
$data = [
    'nomor'         => $_POST['f_nomor'] ?? '',
    'hal'           => $_POST['f_hal'] ?? '',
    'kepada'        => $_POST['f_kepada'] ?? '',
    'tanggal'       => $_POST['f_tglsurat'] ?? date('Y-m-d'),
    
    // Data Acara (Pastikan nanti form inputnya punya nama-nama ini)
    'hari_tanggal'  => $_POST['f_hari'] ?? date('Y-m-d'),
    'pukul_mulai'   => $_POST['f_mulai'] ?? '09:00',
    'pukul_selesai' => $_POST['f_selesai'] ?? 'Selesai',
    'tempat'        => $_POST['f_tempat'] ?? '',
    'agenda'        => $_POST['f_agenda'] ?? '',
    'pimpinan'      => $_POST['f_pimpinan'] ?? ''
];

// 3. Simpan ke Session
$_SESSION['undangan'] = $data;

// 4. Respon
echo 'OK';
?>
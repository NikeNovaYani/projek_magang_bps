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
// 3. Simpan ke Session
$_SESSION['undangan'] = $data;

// 4. Handle Archiving (bika action=archive)
if (isset($_GET['action']) && $_GET['action'] === 'archive') {
    $arsipDir = '../arsip/';
    if (!is_dir($arsipDir)) mkdir($arsipDir, 0777, true);

    // Format Folder: YYYY-MM-DD_NamaKegiatan
    // Gunakan 'hal' sebagai nama kegiatan, sanitasi karakter
    $hal = trim($data['hal'] ?: 'Undangan_Baru');
    $safeHal = preg_replace('/[^A-Za-z0-9\-_]/', '_', $hal);
    $folderName = date('Y-m-d') . '_' . $safeHal;
    $targetDir = $arsipDir . $folderName . '/';

    // Buat Struktur Folder
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
        // Buat subfolder standar arsip (agar kompatibel)
        $subfolders = ['undangan', 'notulensi', 'absensi'];
        foreach ($subfolders as $sub) {
            mkdir($targetDir . $sub . '/', 0777, true);
        }
    }

    // Simpan JSON Data
    $jsonPath = $targetDir . 'undangan.json';
    file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
    
    // Return Folder Name untuk dipakai generate PDF
    echo $folderName;
    exit;
}

// 5. Respon
echo 'OK';
?>
<?php
session_start();
require_once '../koneksi.php'; // Wajib ada koneksi database

// 1. Cek Metode Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Method Not Allowed";
    exit;
}

// 2. Ambil Data dari Form
// (Pastikan 'name' di input HTML sesuai dengan $_POST di sini)
$id_u           = $_POST['id_u'] ?? 0; // ID hidden jika mode edit
$nama_kegiatan  = mysqli_real_escape_string($koneksi, $_POST['f_nama_kegiatan'] ?? '');
$nomor_surat    = mysqli_real_escape_string($koneksi, $_POST['f_nomor'] ?? '');
$perihal        = mysqli_real_escape_string($koneksi, $_POST['f_hal'] ?? ''); // Di DB namanya 'perihal'
$kepada         = mysqli_real_escape_string($koneksi, $_POST['f_kepada'] ?? '');
$isi_undangan   = mysqli_real_escape_string($koneksi, $_POST['f_isi'] ?? ''); // [BARU] Ambil Isi
$tanggal_surat  = $_POST['f_tglsurat'] ?? date('Y-m-d');

// Data Acara
// Data Acara
$hari_tanggal   = $_POST['f_hari'] ?? date('Y-m-d');

// [FIX] Prioritize f_waktu from undangan.php
if (isset($_POST['f_waktu']) && !empty($_POST['f_waktu'])) {
    $waktu_mulai    = $_POST['f_waktu'];
    $waktu_selesai  = 'Selesai';
    $waktu_acara    = $waktu_mulai; // Simpan apa adanya sesuai input user
} else {
    $waktu_mulai    = $_POST['f_mulai'] ?? '09:00';
    $waktu_selesai  = $_POST['f_selesai'] ?? 'Selesai';
    $waktu_acara    = $waktu_mulai . ' s.d ' . $waktu_selesai . ' WIB'; // Gabung biar rapi di DB
}

$tempat         = mysqli_real_escape_string($koneksi, $_POST['f_tempat'] ?? '');
$agenda         = mysqli_real_escape_string($koneksi, $_POST['f_agenda'] ?? '');

// Data Tambahan (Session untuk preview PDF jika perlu)
$_SESSION['undangan'] = [
    'nomor' => $nomor_surat,
    'hal' => $perihal,
    'kepada' => $kepada,
    'isi' => $isi_undangan, // [BARU] Simpan ke Session
    'tanggal' => $tanggal_surat,
    'tanggal_acara' => $hari_tanggal, // [FIX] Key match for template
    'pukul_mulai' => $waktu_mulai,
    'pukul_selesai' => $waktu_selesai,
    'tempat' => $tempat,
    'agenda' => $agenda,
    'nama_kegiatan' => $nama_kegiatan
];

// 3. LOGIKA SIMPAN KE DATABASE (Tabel Undangan)

if ($id_u > 0) {
    // === UPDATE DATA LAMA ===
    $query = "UPDATE undangan SET 
              nama_kegiatan = '$nama_kegiatan',
              nomor_surat = '$nomor_surat',
              perihal = '$perihal',
              kepada = '$kepada',
              isi_undangan = '$isi_undangan',
              tanggal_surat = '$tanggal_surat',
              hari_tanggal_acara = '$hari_tanggal',
              waktu_acara = '$waktu_acara',
              tempat_acara = '$tempat',
              agenda = '$agenda'
              WHERE id_u = '$id_u'";
} else {
    // === INSERT DATA BARU ===
    $query = "INSERT INTO undangan 
              (nama_kegiatan, nomor_surat, perihal, kepada, isi_undangan, tanggal_surat, 
               hari_tanggal_acara, waktu_acara, tempat_acara, agenda, undangan_pdf)
              VALUES 
              ('$nama_kegiatan', '$nomor_surat', '$perihal', '$kepada', '$isi_undangan', '$tanggal_surat', 
               '$hari_tanggal', '$waktu_acara', '$tempat', '$agenda', NULL)";
    // undangan_pdf diset NULL dulu, nanti diupdate oleh generate_undangan.php
}

// Eksekusi Query
if (mysqli_query($koneksi, $query)) {

    // 4. Ambil ID Terbaru
    if ($id_u > 0) {
        $last_id = $id_u;
    } else {
        $last_id = mysqli_insert_id($koneksi);
    }

    // 5. PENTING: Kirim ID balik ke Javascript
    echo $last_id;
} else {
    echo "Error Database: " . mysqli_error($koneksi);
}

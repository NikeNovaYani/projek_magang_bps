<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

// Hubungkan ke Database
require_once __DIR__ . '/../koneksi.php';

/* ================= AMBIL DATA ================= */
// Kita butuh ID Undangan untuk Update Database
$id_undangan = $_GET['id'] ?? 0;

// Ambil data sesi (jika ada) atau ambil ulang dari DB berdasarkan ID agar lebih akurat
$data = $_SESSION['undangan'] ?? [];

// Ambil data pejabat
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);

/* ================= KONFIGURASI MPDF ================= */
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 15,
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_bottom' => 20,
    'tempDir' => __DIR__ . '/../tmp'
]);

$mpdf->showImageErrors = true;

/* ================= LOAD TEMPLATE ================= */
ob_start();
include __DIR__ . '/template_undangan.php';
$html = ob_get_clean();

/* ================= RENDER PDF ================= */
$mpdf->WriteHTML($html);

// 1. Generate Binary Data (String PDF)
$pdfContent = $mpdf->Output('', 'S');

/* ================= [BARU] SIMPAN KE ARSIP OTOMATIS ================= */
// Hanya jalankan jika ID Undangan valid
if ($id_undangan > 0) {

    // Ambil Tanggal Acara & Nama Kegiatan dari DB untuk Folder (Agar Konsisten dengan Notulensi)
    $q_undangan = mysqli_query($koneksi, "SELECT hari_tanggal_acara, nama_kegiatan FROM undangan WHERE id_u = '$id_undangan'");
    $row_undangan = mysqli_fetch_assoc($q_undangan);

    $tgl_folder = $row_undangan['hari_tanggal_acara'] ?? date('Y-m-d');
    $nama_kegiatan_db = $row_undangan['nama_kegiatan'] ?? 'Rapat';

    // A. Tentukan Nama File Unik (Undangan_ID_NamaKegiatan.pdf)
    // Bersihkan nama kegiatan dari simbol aneh
    $nama_kegiatan_bersih = preg_replace('/[^A-Za-z0-9]/', '_', $nama_kegiatan_db);
    $nama_file_pdf = "Undangan_" . $id_undangan . "_" . $nama_kegiatan_bersih . ".pdf";

    // B. Tentukan Lokasi Folder (arsip_pdf/YYYY-MM-DD_NamaKegiatan)
    $folder_name = $tgl_folder . '_' . $nama_kegiatan_bersih;
    $path_folder = __DIR__ . '/../arsip_pdf/' . $folder_name;
    $path_arsip_otomatis = $path_folder . '/' . $nama_file_pdf;

    // C. Simpan File ke Server
    // Pastikan folder ada
    if (!is_dir($path_folder)) {
        mkdir($path_folder, 0777, true);
    }
    file_put_contents($path_arsip_otomatis, $pdfContent);

    // D. UPDATE DATABASE (KUNCI AGAR MUNCUL DI VIEW ARSIP)
    // Update kolom undangan_pdf dengan RELATIVE PATH (NamaFolder/NamaFile)
    $db_path = $folder_name . '/' . $nama_file_pdf;
    $update_query = "UPDATE undangan SET undangan_pdf = '$db_path' WHERE id_u = '$id_undangan'";
    mysqli_query($koneksi, $update_query);
}
/* ================= SELESAI SIMPAN ================= */


/* ================= LOGIKA LAMA (OPSIONAL/MANUAL) ================= */
// Ini kode lama kamu untuk simpan ke folder manual (archive_folder)
// Tetap saya biarkan agar fitur lama tidak rusak
if (isset($_GET['archive_folder']) && !empty($_GET['archive_folder'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['archive_folder']);
    $savePath = __DIR__ . '/../arsip/' . $folder . '/undangan/Undangan_Rapat.pdf';

    $dir = dirname($savePath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    file_put_contents($savePath, $pdfContent);
}

/* ================= OUTPUT KE BROWSER ================= */
$dest = isset($_GET['download']) && $_GET['download'] === 'true' ? 'D' : 'I';
$filename_browser = 'Undangan_Rapat.pdf';

if ($dest === 'D') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename_browser . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $pdfContent;
} else {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename_browser . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $pdfContent;
}

exit;

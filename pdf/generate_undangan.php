<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

/* ================= AMBIL DATA ================= */
$data = $_SESSION['undangan'] ?? [];

/* ================= KONFIGURASI MPDF ================= */
// Kita tambahkan 'tempDir' agar mPDF punya akses tulis gambar sementara jika perlu
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8', 
    'format' => 'A4',
    'margin_top' => 15,
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_bottom' => 20,
    'tempDir' => __DIR__ . '/../tmp' // Pastikan folder tmp ada atau hapus baris ini jika error
]);

/* ================= PENTING: IZINKAN GAMBAR REMOTE/LOKAL ================= */
$mpdf->showImageErrors = true; // Agar ketahuan jika ada error gambar

/* ================= LOAD TEMPLATE ================= */
ob_start();
include __DIR__ . '/template_undangan.php';
$html = ob_get_clean();

/* ================= RENDER PDF ================= */
$mpdf->WriteHTML($html);

// SAVE TO ARCHIVE IF REQUESTED
if (isset($_GET['archive_folder']) && !empty($_GET['archive_folder'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['archive_folder']);
    $savePath = __DIR__ . '/../arsip/' . $folder . '/undangan/Undangan_Rapat.pdf';
    
    // Pastikan folder ada (redundant check, tapi aman)
    $dir = dirname($savePath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    $mpdf->Output($savePath, 'F'); // F = File Save
}

// Cek parameter download
$dest = isset($_GET['download']) && $_GET['download'] === 'true' ? 'D' : 'I';

$mpdf->Output('Undangan_Rapat.pdf', $dest);
exit;
?>
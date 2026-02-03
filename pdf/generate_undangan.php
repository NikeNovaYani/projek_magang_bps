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
$mpdf->Output('Undangan_Rapat.pdf', 'I');
exit;
?>
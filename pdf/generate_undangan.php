<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

/* ================= AMBIL DATA ================= */
$data = $_SESSION['undangan'] ?? [];

// Ambil data pejabat dari Database
require_once __DIR__ . '/../koneksi.php';
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);


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
/* ================= RENDER PDF ================= */
$mpdf->WriteHTML($html);

// 1. Generate Binary Data (String)
$pdfContent = $mpdf->Output('', 'S');

// 2. SAVE TO ARCHIVE IF REQUESTED
if (isset($_GET['archive_folder']) && !empty($_GET['archive_folder'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['archive_folder']);
    $savePath = __DIR__ . '/../arsip/' . $folder . '/undangan/Undangan_Rapat.pdf';

    // Pastikan folder ada
    $dir = dirname($savePath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // Write content to file
    file_put_contents($savePath, $pdfContent);
}

// 3. OUTPUT TO BROWSER (Download/Inline)
$dest = isset($_GET['download']) && $_GET['download'] === 'true' ? 'D' : 'I';
$filename = 'Undangan_Rapat.pdf';

if ($dest === 'D') {
    // Force Download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $pdfContent;
} else {
    // Inline View
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $pdfContent;
}

exit;

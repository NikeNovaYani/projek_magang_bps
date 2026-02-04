<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

/* ================= AMBIL DATA ================= */
$data = $_SESSION['notulensi'] ?? [];

/* ================= MPDF ================= */
$mpdf = new \Mpdf\Mpdf([
    'format'        => 'A4',
    'margin_top'    => 25,
    'margin_bottom' => 25,
    'margin_left'   => 25,
    'margin_right'  => 25,
    'default_font'  => 'arial'
]);

/* ================= LOAD TEMPLATE ================= */
ob_start();
include __DIR__ . '/template_notulensi.php';
$html = ob_get_clean();

/* ================= RENDER PDF ================= */
$mpdf->WriteHTML($html);

// SAVE TO ARCHIVE IF REQUESTED
if (isset($_GET['archive_folder']) && !empty($_GET['archive_folder'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['archive_folder']);
    $savePath = __DIR__ . '/../arsip/' . $folder . '/notulensi/Notulensi_Rapat.pdf';
    
    // Pastikan folder ada
    $dir = dirname($savePath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    $mpdf->Output($savePath, 'F');
}

// Cek output destination
$dest = isset($_GET['download']) && $_GET['download'] === 'true' ? 'D' : 'I';

$mpdf->Output('Notulensi_Rapat.pdf', $dest);
exit;

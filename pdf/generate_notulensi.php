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
$mpdf->Output('Notulensi_Rapat.pdf', 'I');
exit;

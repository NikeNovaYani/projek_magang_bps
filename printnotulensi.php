<?php
require_once __DIR__ . '/vendor/autoload.php';

$page = $_GET['page'] ?? 'notulensi';
$allowed = ['notulensi'];
if (!in_array($page, $allowed, true)) {
    $page = 'notulensi';
}

$fileMap = [
    'notulensi' => 'notulensi.php',
];

$_GET['print'] = '1';

// Capture the HTML output
ob_start();
include $fileMap[$page];
$html = ob_get_clean();

// Create mPDF instance
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 20,
    'margin_bottom' => 20,
    'margin_header' => 0,
    'margin_footer' => 0,
]);

// Write HTML to PDF
$mpdf->WriteHTML($html);

// Output PDF
$mpdf->Output('notulensi.pdf', 'D');
?>

<?php
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

$mpdf = new Mpdf();
$mpdf->WriteHTML('<h1>PDF mPDF BERHASIL!</h1><p>mPDF sudah aktif.</p>');
$mpdf->Output();

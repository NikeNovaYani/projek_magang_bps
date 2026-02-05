<?php
require_once __DIR__ . '/../vendor/autoload.php';
// FAILSAFE LOG
file_put_contents(__DIR__ . '/../failsafe_debug.txt', "Hit generate_notulensi.php at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
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
    'default_font'  => 'arial',
    'tempDir'       => __DIR__ . '/../tmp' // Important for image processing
]);

$mpdf->showImageErrors = true; // Debug images

/* ================= LOAD TEMPLATE ================= */
try {
    ob_start();
    include __DIR__ . '/template_notulensi.php';
    $html = ob_get_clean();

    /* ================= RENDER PDF ================= */
    $mpdf->WriteHTML($html);

    // 1. Generate Binary Data (String)
    $pdfContent = $mpdf->Output('', 'S');

    // 2. SAVE TO ARCHIVE IF REQUESTED
    if (isset($_GET['archive_folder']) && !empty($_GET['archive_folder'])) {
        $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['archive_folder']);
        $savePath = __DIR__ . '/../arsip/' . $folder . '/notulensi/Notulensi_Rapat.pdf';

        // Pastikan folder ada
        $dir = dirname($savePath);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        // Write content to file
        $bytes = file_put_contents($savePath, $pdfContent);
        if ($bytes === false) {
            throw new Exception("Failed to write PDF to $savePath");
        }
    }

    // 3. OUTPUT TO BROWSER (Download/Inline)
    $dest = isset($_GET['download']) && $_GET['download'] === 'true' ? 'D' : 'I';
    $filename = 'Notulensi_Rapat.pdf';

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
} catch (\Throwable $e) {
    // Log Fatal Errors
    file_put_contents(
        __DIR__ . '/../tmp/error_pdf_notulensi.log',
        date('Y-m-d H:i:s') . " ERROR in generate_notulensi.php:\n" .
            "Message: " . $e->getMessage() . "\n" .
            "File: " . $e->getFile() . " on line " . $e->getLine() . "\n" .
            "Trace:\n" . $e->getTraceAsString() . "\n\n",
        FILE_APPEND
    );

    // Output error to browser so we can see it in valid response
    echo "<h1>Terjadi Kesalahan Sistem</h1>";
    echo "<p>Gagal membuat PDF. Pesan error telah dicatat.</p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

exit;

<?php
require_once __DIR__ . '/../vendor/autoload.php';
// FAILSAFE LOG
file_put_contents(__DIR__ . '/../failsafe_debug.txt', "Hit generate_notulensi.php at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
session_start();

/* ================= HELPER ================= */
if (!function_exists('formatTanggalIndo')) {
    function formatTanggalIndo($date)
    {
        if (!$date) return '';
        $bulan = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        $ts = strtotime($date);
        return date('d', $ts) . ' ' . $bulan[date('F', $ts)] . ' ' . date('Y', $ts);
    }
}

/* ================= AMBIL DATA ================= */
// 1. Cek apakah ada ID di URL
$id_notulensi = $_GET['id'] ?? 0;

if ($id_notulensi > 0) {
    // Ambil dari Database
    require_once __DIR__ . '/../koneksi.php';
    $q = mysqli_query($koneksi, "SELECT * FROM notulensi WHERE id_n = '$id_notulensi'");
    if ($row = mysqli_fetch_assoc($q)) {
        // 1. Separate Images and PDFs
        $all_dokumentasi = json_decode($row['foto_dokumentasi'] ?? '[]', true) ?? [];
        $all_absensi     = json_decode($row['foto_absensi'] ?? '[]', true) ?? [];

        $img_dokumentasi = [];
        $img_absensi     = [];
        $pdf_attachments = [];

        // Helper to separate
        foreach ($all_dokumentasi as $f) {
            $path = (strpos($f, '/') === false) ? 'uploads/dokumentasi/' . $f : $f;
            if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
                $pdf_attachments[] = $path;
            } else {
                $img_dokumentasi[] = $path;
            }
        }

        foreach ($all_absensi as $f) {
            $path = (strpos($f, '/') === false) ? 'uploads/absensi/' . $f : $f;
            if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
                $pdf_attachments[] = $path;
            } else {
                $img_absensi[] = $path;
            }
        }

        // Map DB columns to Template Variables
        $data = [
            'unit_kerja'    => $row['unit_kerja'],
            'tanggal'       => $_GET['tgl'] ?? $row['tanggal_rapat'],
            'tanggal_raw'   => $_GET['tgl'] ?? $row['tanggal_rapat'],
            'tanggal_fmt'   => formatTanggalIndo($_GET['tgl'] ?? $row['tanggal_rapat']),
            'pimpinan'      => $row['pimpinan_rapat'],
            'pukul_mulai'   => $row['waktu_mulai'],
            'pukul_selesai' => $row['waktu_selesai'],
            'topik'         => $row['topik'],
            'tempat'        => $row['tempat'],
            'lampiran'      => $row['lampiran_ket'],
            'peserta'       => $row['peserta'],
            'agenda'        => $row['agenda'],
            'pembukaan'     => html_entity_decode($row['isi_pembukaan']),
            'pembahasan'    => html_entity_decode($row['isi_pembahasan']),
            'kesimpulan'    => html_entity_decode($row['isi_kesimpulan']),
            // TTD
            'p_tempat'      => $row['tempat_pembuatan'],
            'p_tanggal'     => $row['tanggal_pembuatan'],
            'p_notulis'     => $row['nama_notulis'],
            // Data Image (No PDF)
            'dokumentasi'   => $img_dokumentasi,
            'absensi'       => $img_absensi,
        ];
    } else {
        die("Data Notulensi tidak ditemukan.");
    }
} else {
    // 2. Fallback ke Session (Preview sebelum simpan)
    // NOTE: Preview session handling for PDFs might need more complex logic effectively
    // For now, simpler to just assume session data structure matches or just skip PDF merging for session preview if not complex
    $data = $_SESSION['notulensi'] ?? [];
    $pdf_attachments = []; // Reset for session mode (complex to handle temp files without DB)
}

/* ================= MPDF ================= */
$mpdf = new \Mpdf\Mpdf([
    'format'        => 'A4',
    'margin_top'    => 25,
    'margin_bottom' => 25,
    'margin_left'   => 25,
    'margin_right'  => 25,
    'default_font'  => 'arial',
    'tempDir'       => __DIR__ . '/../tmp',
    'shrink_tables_to_fit' => 0
]);

$mpdf->showImageErrors = true;

/* ================= LOAD TEMPLATE ================= */
try {
    ob_start();
    include __DIR__ . '/template_notulensi.php';
    $html = ob_get_clean();

    // [FIX] Konversi CSS list-style ke Atribut HTML type secara Robust (Regex)
    $html = preg_replace('/(<ol[^>]*?)style="[^"]*list-style-type:\s*lower-alpha;?[^"]*"([^>]*>)/i', '$1 type="a" $2', $html);
    $html = preg_replace('/(<ol[^>]*?)style="[^"]*list-style-type:\s*upper-alpha;?[^"]*"([^>]*>)/i', '$1 type="A" $2', $html);
    $html = preg_replace('/(<ol[^>]*?)style="[^"]*list-style-type:\s*lower-roman;?[^"]*"([^>]*>)/i', '$1 type="i" $2', $html);
    $html = preg_replace('/(<ol[^>]*?)style="[^"]*list-style-type:\s*upper-roman;?[^"]*"([^>]*>)/i', '$1 type="I" $2', $html);

    /* ================= RENDER PDF ================= */
    $mpdf->WriteHTML($html);

    /* ================= APPEND PDF ATTACHMENTS ================= */
    if (!empty($pdf_attachments)) {
        file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "Found PDFs: " . print_r($pdf_attachments, true) . "\n", FILE_APPEND);

        foreach ($pdf_attachments as $pdfFile) {
            $pdfPath = __DIR__ . '/../' . $pdfFile;

            // Debug path
            file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "Checking: $pdfPath\n", FILE_APPEND);

            if (file_exists($pdfPath)) {
                try {
                    $pageCount = $mpdf->SetSourceFile($pdfPath);
                    file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "Merging $pdfFile ($pageCount pages)\n", FILE_APPEND);

                    for ($i = 1; $i <= $pageCount; $i++) {
                        $mpdf->AddPage();
                        $tplId = $mpdf->ImportPage($i);
                        $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]);
                    }
                } catch (\Throwable $e) {
                    // FALLBACK: TRY GHOSTSCRIPT CONVERSION (Use QGIS's GS if available)
                    $gsPath = 'C:\\Program Files\\QGIS 3.40.7\\bin\\gswin64c.exe';
                    $tempPdf = __DIR__ . '/../tmp/temp_gs_' . uniqid() . '.pdf';

                    if (file_exists($gsPath)) {
                        file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "Trying Ghostscript fallback for $pdfFile...\n", FILE_APPEND);

                        // GS Command: optimize to version 1.4 which FPDI supports
                        $cmd = sprintf('"%s" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile="%s" "%s"', $gsPath, $tempPdf, $pdfPath);
                        exec($cmd, $output, $returnVar);

                        if ($returnVar === 0 && file_exists($tempPdf)) {
                            try {
                                $pageCount = $mpdf->SetSourceFile($tempPdf);
                                file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "GS Fallback success. Merging ($pageCount pages)\n", FILE_APPEND);
                                for ($i = 1; $i <= $pageCount; $i++) {
                                    $mpdf->AddPage();
                                    $tplId = $mpdf->ImportPage($i);
                                    $mpdf->UseTemplate($tplId, ['adjustPageSize' => true]);
                                }
                            } catch (\Throwable $e2) {
                                file_put_contents(__DIR__ . '/../tmp/pdf_merge_error.log', "GS Fallback failed for $pdfFile: " . $e2->getMessage() . "\n", FILE_APPEND);
                            }
                            // Cleanup temp file
                            unlink($tempPdf);
                        } else {
                            file_put_contents(__DIR__ . '/../tmp/pdf_merge_error.log', "Ghostscript execution failed for $pdfFile. Return: $returnVar\n", FILE_APPEND);
                        }
                    } else {
                        file_put_contents(__DIR__ . '/../tmp/pdf_merge_error.log', "Failed to merge $pdfFile (FPDI error) and GS not found at $gsPath: " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            } else {
                file_put_contents(__DIR__ . '/../tmp/pdf_debug.log', "File Not Found: $pdfPath\n", FILE_APPEND);
            }
        }
    }

    // 1. Generate Binary Data (String)
    $pdfContent = $mpdf->Output('', 'S');

    // 2. SAVE TO ARCHIVE AUTOMATICALLY (The Undangan Way)
    if ($id_notulensi > 0) {
        $clean_topik = isset($data['topik']) ? preg_replace('/[^A-Za-z0-9]/', '_', $data['topik']) : 'Rapat';
        // Nama file: Notulensi_ID_NamaKegiatan.pdf
        $nama_file_pdf = "Notulensi_" . $id_notulensi . "_" . $clean_topik . ".pdf";

        // Ambil Data dari tabel UNDANGAN agar nama folder 100% sama dengan generate_undangan.php
        $q_folder = mysqli_query($koneksi, "SELECT hari_tanggal_acara, nama_kegiatan FROM undangan WHERE id_u = '$id_notulensi'");
        $row_folder = mysqli_fetch_assoc($q_folder);

        $tgl_folder_db = $row_folder['hari_tanggal_acara'] ?? date('Y-m-d');
        $nama_kegiatan_db = $row_folder['nama_kegiatan'] ?? 'Rapat';

        // Nama Folder: YYYY-MM-DD_NamaKegiatan (Sesuai Undangan)
        $clean_nama_folder = preg_replace('/[^A-Za-z0-9]/', '_', $nama_kegiatan_db);
        $folder_name = $tgl_folder_db . '_' . $clean_nama_folder;

        $path_folder = __DIR__ . '/../arsip_pdf/' . $folder_name;
        $path_arsip_otomatis = $path_folder . '/' . $nama_file_pdf;

        // Pastikan folder arsip_pdf ada
        if (!is_dir($path_folder)) {
            mkdir($path_folder, 0777, true);
        }

        // Simpan File
        file_put_contents($path_arsip_otomatis, $pdfContent);

        // Update Database dengan RELATIVE PATH
        if (isset($koneksi)) {
            $db_path = $folder_name . '/' . $nama_file_pdf;
            $update_query = "UPDATE notulensi SET notulensi_pdf = '$db_path' WHERE id_n = '$id_notulensi'";
            mysqli_query($koneksi, $update_query);
        }
    }

    // 3. LOGIKA LAMA (Untuk Kompatibilitas Arsip Manual)
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

    // 4. OUTPUT TO BROWSER (Download/Inline)
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

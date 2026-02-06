<?php
session_start();

/* =========================
   UTIL FORMAT TANGGAL
========================= */
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

function formatTanggalIndo($date)
{
    global $bulan;
    if (!$date) return '';
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan[date('F', $ts)] . ' ' . date('Y', $ts);
}

/* =========================
   AMBIL DATA DARI FORM (atau SESSION if Archive Action without POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unit_kerja'])) {
    $data = [
        'unit_kerja'    => $_POST['unit_kerja'] ?? '',
        'tanggal'       => $_POST['tanggal'] ?? '',
        'tanggal_fmt'   => formatTanggalIndo($_POST['tanggal'] ?? ''),
        'pimpinan'      => $_POST['pimpinan'] ?? '',
        'pukul_mulai'   => $_POST['pukul_mulai'] ?? '',
        'pukul_selesai' => $_POST['pukul_selesai'] ?? '',
        'topik'         => $_POST['topik'] ?? '',
        'tempat'        => $_POST['tempat'] ?? '',
        'lampiran'      => $_POST['lampiran'] ?? '',
        'peserta'       => $_POST['peserta'] ?? '',
        'agenda'        => $_POST['agenda'] ?? '',

        // TinyMCE (HTML – jangan di htmlspecialchars!)
        'pembukaan'     => $_POST['pembukaan'] ?? '',
        'pembahasan'    => $_POST['pembahasan'] ?? '',
        'kesimpulan'    => $_POST['kesimpulan'] ?? '',
    ];
    // Update Session Immediately
    $_SESSION['notulensi'] = $data; // We'll merge files later
} else {
    // If just calling action=archive, use existing session
    $data = $_SESSION['notulensi'] ?? [];
}

/* =========================
   HANDLE UPLOAD FILE
========================= */
if (!isset($_SESSION['notulensi']['dokumentasi'])) {
    $_SESSION['notulensi']['dokumentasi'] = [];
}

if (!empty($_FILES['dokumentasi']['name'][0])) {
    $uploadDir = '../uploads/dokumentasi/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadedFiles = [];
    foreach ($_FILES['dokumentasi']['name'] as $key => $name) {
        if ($_FILES['dokumentasi']['error'][$key] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['dokumentasi']['tmp_name'][$key];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = uniqid('doc_') . '.' . $ext;
            $destination = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFiles[] = 'uploads/dokumentasi/' . $newName;
            }
        }
    }

    // Merge with existing or overwrite? 
    // Requirement: Merge new uploads with existing files validation
    $finalDocs = $_POST['existing_dokumentasi'] ?? [];

    if (!empty($uploadedFiles)) {
        // Merge arrays
        $finalDocs = array_merge($finalDocs, $uploadedFiles);
    }

    // Ensure uniqueness if needed
    $data['dokumentasi'] = array_unique($finalDocs);
} else {
    // If no new file uploaded, check if we have existing files sent
    if (isset($_POST['existing_dokumentasi'])) {
        $data['dokumentasi'] = $_POST['existing_dokumentasi'];
    } else {
        // Fallback to session (only if not POSTing form, or safety net)
        $data['dokumentasi'] = $_SESSION['notulensi']['dokumentasi'] ?? [];
    }
}

/* =========================
   HANDLE UPLOAD ABSENSI
========================= */
if (!isset($_SESSION['notulensi']['absensi'])) {
    $_SESSION['notulensi']['absensi'] = [];
}

if (!empty($_FILES['absensi']['name'][0])) {
    $uploadDir = '../uploads/absensi/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadedFilesAbsensi = [];
    foreach ($_FILES['absensi']['name'] as $key => $name) {
        if ($_FILES['absensi']['error'][$key] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['absensi']['tmp_name'][$key];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $newName = uniqid('abs_') . '.' . $ext;
            $destination = $uploadDir . $newName;

            if (move_uploaded_file($tmpName, $destination)) {
                $uploadedFilesAbsensi[] = 'uploads/absensi/' . $newName;
            }
        }
    }

    $finalAbs = $_POST['existing_absensi'] ?? [];

    if (!empty($uploadedFilesAbsensi)) {
        $finalAbs = array_merge($finalAbs, $uploadedFilesAbsensi);
    }

    $data['absensi'] = array_unique($finalAbs);
} else {
    if (isset($_POST['existing_absensi'])) {
        $data['absensi'] = $_POST['existing_absensi'];
    } else {
        $data['absensi'] = $_SESSION['notulensi']['absensi'] ?? [];
    }
}

/* =========================
   SIMPAN KE DATABASE
========================= */
require_once __DIR__ . '/../koneksi.php';

if ($koneksi) {
    // Escape variables
    $nama_kegiatan = mysqli_real_escape_string($koneksi, $_POST['nama_kegiatan'] ?? '');
    $unit_kerja    = mysqli_real_escape_string($koneksi, $data['unit_kerja'] ?? '');
    $tanggal_rapat = mysqli_real_escape_string($koneksi, $data['tanggal'] ?? '');
    $waktu_mulai   = mysqli_real_escape_string($koneksi, $data['pukul_mulai'] ?? '');
    $waktu_selesai = mysqli_real_escape_string($koneksi, $data['pukul_selesai'] ?? '');
    $tempat        = mysqli_real_escape_string($koneksi, $data['tempat'] ?? '');
    $pimpinan      = mysqli_real_escape_string($koneksi, $data['pimpinan'] ?? '');
    $topik         = mysqli_real_escape_string($koneksi, $data['topik'] ?? '');
    $lampiran      = mysqli_real_escape_string($koneksi, $data['lampiran'] ?? ''); // Maps to lampiran_ket
    $peserta       = mysqli_real_escape_string($koneksi, $data['peserta'] ?? '');
    $agenda        = mysqli_real_escape_string($koneksi, $data['agenda'] ?? '');
    $pembukaan     = mysqli_real_escape_string($koneksi, $data['pembukaan'] ?? '');
    $pembahasan    = mysqli_real_escape_string($koneksi, $data['pembahasan'] ?? '');
    $kesimpulan    = mysqli_real_escape_string($koneksi, $data['kesimpulan'] ?? '');

    $p_tempat      = mysqli_real_escape_string($koneksi, $_POST['p_tempat'] ?? '');
    $p_tanggal     = mysqli_real_escape_string($koneksi, $_POST['p_tanggal'] ?? '');
    $p_notulis     = mysqli_real_escape_string($koneksi, $_POST['p_notulis'] ?? '');

    // JSON Files
    $json_dok      = mysqli_real_escape_string($koneksi, json_encode($data['dokumentasi'] ?? []));
    $json_abs      = mysqli_real_escape_string($koneksi, json_encode($data['absensi'] ?? []));

    // ID Undangan (Optional / NULL)
    // Jika ada input hidden id_n (dari load undangan), gunakan. Jika tidak, NULL.
    $id_n          = isset($_POST['id_n']) ? (int)$_POST['id_n'] : 'NULL';

    $sql = "INSERT INTO notulensi (
        id_n, nama_kegiatan, unit_kerja, tanggal_rapat, waktu_mulai, waktu_selesai, tempat, 
        pimpinan_rapat, topik, lampiran_ket, peserta, agenda, 
        isi_pembukaan, isi_pembahasan, isi_kesimpulan, 
        tempat_pembuatan, tanggal_pembuatan, nama_notulis, 
        foto_dokumentasi, foto_absensi
    ) VALUES (
        $id_n, '$nama_kegiatan', '$unit_kerja', '$tanggal_rapat', '$waktu_mulai', '$waktu_selesai', '$tempat',
        '$pimpinan', '$topik', '$lampiran', '$peserta', '$agenda',
        '$pembukaan', '$pembahasan', '$kesimpulan',
        '$p_tempat', '$p_tanggal', '$p_notulis',
        '$json_dok', '$json_abs'
    )";

    // Insert but don't stop execution if fails (maybe log error?)
    if (!mysqli_query($koneksi, $sql)) {
        // Option: Log error or just ignore for now since user wants "save to database"
        error_log("DB Insert Error: " . mysqli_error($koneksi));
    }
}

/* =========================
   SIMPAN KE SESSION
========================= */
$_SESSION['notulensi'] = $data;

// 5. Handle Archiving
if (isset($_GET['action']) && $_GET['action'] === 'archive') {
    $arsipDir = '../arsip/';
    if (!is_dir($arsipDir)) mkdir($arsipDir, 0777, true);

    // Format Folder: YYYY-MM-DD_NamaKegiatan
    $namaKegiatan = trim($_POST['nama_kegiatan'] ?? '');
    if (empty($namaKegiatan)) {
        // Fallback
        $namaKegiatan = trim($data['topik'] ?: 'Rapat_Tanpa_Judul');
    }

    $safeName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $namaKegiatan);

    // Checks for existing folder by suffix
    $existingFolder = null;
    $folders = scandir($arsipDir);
    foreach ($folders as $f) {
        if ($f === '.' || $f === '..') continue;
        if (strpos($f, '_' . $safeName) !== false) {
            $existingFolder = $f;
            break;
        }
    }

    if ($existingFolder) {
        $folderName = $existingFolder;
    } else {
        $folderName = date('Y-m-d') . '_' . $safeName;
    }

    $targetDir = $arsipDir . $folderName . '/';

    // Buat Struktur Folder
    // Buat Struktur Folder
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Ensure all subfolders exist (even if main folder existed previously)
    $subfolders = ['undangan', 'notulensi', 'absensi', 'dokumentasi'];
    foreach ($subfolders as $sub) {
        if (!is_dir($targetDir . $sub . '/')) {
            mkdir($targetDir . $sub . '/', 0777, true);
        }
    }

    // Simpan JSON Data
    $jsonPath = $targetDir . 'notulensi.json';
    file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

    // COPY FILES KE ARSIP (Agar jadi Gudang Penyimpanan mandiri)
    // 1. Absensi
    if (!empty($data['absensi'])) {
        foreach ($data['absensi'] as $fileRelPath) {
            $src = '../' . $fileRelPath;
            if (file_exists($src)) {
                $dest = $targetDir . 'absensi/' . basename($src);
                copy($src, $dest);
            }
        }
    }
    // 2. Dokumentasi -> Masukkan ke folder notulensi saja sementara (atau buat dokumentasi khusus jika view mendukung)
    // view_folder.php saat ini scan 'notulensi'. Kita taruh situ.
    if (!empty($data['dokumentasi'])) {
        foreach ($data['dokumentasi'] as $fileRelPath) {
            $src = '../' . $fileRelPath;
            if (file_exists($src)) {
                $dest = $targetDir . 'dokumentasi/' . basename($src);
                copy($src, $dest);
            }
        }
    }

    // Log the response
    file_put_contents('../tmp/save_response_log.txt', "Returning folder: " . $folderName . "\n", FILE_APPEND);

    echo $folderName;
    exit;
}

echo 'OK';

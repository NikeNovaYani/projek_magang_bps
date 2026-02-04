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
    // Requirement implies "new upload", let's overwrite for simplicity unless user adds more.
    // For now, let's just replace the session data with new files if uploaded.
    if (!empty($uploadedFiles)) {
        $data['dokumentasi'] = $uploadedFiles;
    } else {
        // keep old if exists
        $data['dokumentasi'] = $_SESSION['notulensi']['dokumentasi'];
    }
} else {
    // If no new file uploaded, keep existing session data
    $data['dokumentasi'] = $_SESSION['notulensi']['dokumentasi'] ?? [];
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

    if (!empty($uploadedFilesAbsensi)) {
        $data['absensi'] = $uploadedFilesAbsensi;
    } else {
        $data['absensi'] = $_SESSION['notulensi']['absensi'];
    }
} else {
    $data['absensi'] = $_SESSION['notulensi']['absensi'] ?? [];
}

/* =========================
   SIMPAN KE SESSION
========================= */
$_SESSION['notulensi'] = $data;

// 5. Handle Archiving
if (isset($_GET['action']) && $_GET['action'] === 'archive') {
    $arsipDir = '../arsip/';
    if (!is_dir($arsipDir)) mkdir($arsipDir, 0777, true);

    // Format Folder: YYYY-MM-DD_Topik
    $topik = trim($data['topik'] ?: 'Rapat_Tanpa_Judul');
    $safeTopik = preg_replace('/[^A-Za-z0-9\-_]/', '_', $topik);
    $folderName = date('Y-m-d') . '_' . $safeTopik;
    $targetDir = $arsipDir . $folderName . '/';

    // Buat Struktur Folder
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
        $subfolders = ['undangan', 'notulensi', 'absensi']; // Standard structure
        foreach ($subfolders as $sub) {
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
                $dest = $targetDir . 'notulensi/' . basename($src);
                copy($src, $dest);
            }
        }
    }

    echo $folderName;
    exit;
}

echo 'OK';
?>

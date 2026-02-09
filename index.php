<?php
// ===============================
// EARLY EXIT FOR PDF (WAJIB)
// ===============================
// Ini menangani permintaan cetak PDF agar tidak tercampur dengan HTML
if (isset($_GET['page'])) {
    if ($_GET['page'] === 'generate_notulensi') {
        require __DIR__ . '/pdf/generate_notulensi.php'; // Pastikan path ini benar
        exit;
    }
    // Tambahan untuk Undangan (jika nanti dipanggil via router)
    if ($_GET['page'] === 'generate_undangan') {
        // Asumsi file ini ada di folder pdf atau root. Sesuaikan jika perlu.
        require __DIR__ . '/generate_undangan.php';
        exit;
    }
}

// ===============================
// ROUTING NORMAL
// ===============================
$page = $_GET['page'] ?? 'beranda';

$allowed_pages = [
    'beranda',
    'undangan',
    'notulensi',
    'absensi',


    'arsip',
    'logout'
];


if (!in_array($page, $allowed_pages, true)) {
    $page = 'beranda';
}

if ($page === 'logout') {
    require __DIR__ . '/pages/logout.php';
    exit;
}

// ===============================
// PAGE TITLE
// ===============================
$page_titles = [
    'beranda'       => 'Beranda',
    'undangan'      => 'Undangan Rapat',
    'notulensi'     => 'Notulensi Rapat',
    'absensi'       => 'Absensi Peserta',
    'arsip'         => 'Arsip Rapat',
    'logout'        => 'Logout'
];

$page_title = $page_titles[$page] ?? 'Sistem Rapat';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?> - Sistem Rapat BPS Kota Depok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <h2>UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i> Undangan</a></li>
                <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i> Notulensi</a></li>
                <li><a href="index.php?page=absensi"><i class="fas fa-user-check"></i> Absensi</a></li>
                <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i> Arsip</a></li>
                <li><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <?php
            // ===============================
            // LOADER HALAMAN (Perbaikan Path)
            // ===============================
            switch ($page) {
                case 'undangan':
                    // PERBAIKAN: Menambahkan folder '/pages/'
                    include __DIR__ . '/pages/undangan.php';
                    break;

                case 'notulensi':
                    include __DIR__ . '/pages/notulensi.php';
                    break;

                case 'absensi':
                    // Saya asumsikan ini juga di folder pages
                    include __DIR__ . '/pages/absensi.php';
                    break;


                case 'arsip':
                    // Saya asumsikan ini juga di folder pages
                    include __DIR__ . '/pages/arsip.php';
                    break;


                case 'logout':
                    include __DIR__ . '/pages/logout.php';
                    break;
                default:
                    // Beranda biasanya juga di folder pages? Sesuaikan jika di root.
                    if (file_exists(__DIR__ . '/pages/beranda.php')) {
                        include __DIR__ . '/pages/beranda.php';
                    } else {
                        include __DIR__ . '/beranda.php';
                    }
            }
            ?>
        </div>
    </div>

</body>

</html>
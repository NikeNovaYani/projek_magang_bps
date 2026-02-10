<?php
session_start();

// Jika session status_login tidak ada atau tidak true, tendang balik ke login.php
if (!isset($_SESSION['status_login']) || $_SESSION['status_login'] !== true) {
    header("Location: login.php");
    exit();
}

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

$page_title = $page_titles[$page] ?? 'UANG BPS Kota Depok';

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $page_title ?> - UANG BPS Kota Depok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== GLOBAL LAYOUT STYLES ===== */
        * {
            box-sizing: border-box;
            font-family: "Arial", serif;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
            color: #0d47a1;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: 5px 0 15px rgba(27, 110, 235, 0.1);
            padding: 20px 0;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar h2 {
            text-align: center;
            color: #1976d2;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 700;
            position: relative;
        }

        .sidebar h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #1976d2;
            border-radius: 3px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar li {
            margin: 5px 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #1e70ebff;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 16px;
            position: relative;
            overflow: hidden;
        }

        .sidebar a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #e3f2fd;
            color: #0d47a1;
            transform: translateX(5px);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            margin-left: 280px;
            /* Width of sidebar */
            /* Ensure content fits */
            width: calc(100% - 280px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <h2>UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda" class="<?= $page === 'beranda' ? 'active' : '' ?>"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="index.php?page=undangan" class="<?= $page === 'undangan' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Undangan</a></li>
                <li><a href="index.php?page=notulensi" class="<?= $page === 'notulensi' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Notulensi</a></li>
                <li><a href="index.php?page=absensi" class="<?= $page === 'absensi' ? 'active' : '' ?>"><i class="fas fa-user-check"></i> Absensi</a></li>
                <li><a href="index.php?page=arsip" class="<?= $page === 'arsip' ? 'active' : '' ?>"><i class="fas fa-archive"></i> Arsip</a></li>
                <li style="position: absolute; bottom: 0; width: 100%;"><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
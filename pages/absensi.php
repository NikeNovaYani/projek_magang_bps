<?php
// Mencegah akses langsung ke file ini
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header("Location: ../index.php?page=absensi");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absensi - Desa Cantik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --shadow: 0 10px 30px rgba(15, 23, 42, .08);
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: var(--bg);
            font-family: Arial, sans-serif;
            color: var(--text);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR (Copied from notulensi.php) */
        .sidebar {
            width: 250px;
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

        .sidebar a.active {
            border-left: 4px solid #1976d2;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 140px;
            /* Adjusted to match sidebar width */
            overflow-y: auto;
        }

        /* CARDS & UTILS */
        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-head {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid var(--line);
        }

        .card-head h3 {
            margin: 0;
            color: #0d47a1;
            font-size: 18pt;
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: .15s;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            background: #f8fafc;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        /* Landscape Preview Table */
        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .preview-table td {
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
            text-align: center;
        }

        .landscape-img {
            max-width: 100%;
            height: auto;
            max-height: 80vh;
            /* Prevent it from being too tall */
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

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
            }
        }

        /* PRINT STYLE */
        @media print {

            .sidebar,
            .btn,
            .upload-area,
            .card-head {
                display: none !important;
            }

            .container {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 0;
            }

            .card {
                border: none;
                box-shadow: none;
                margin: 0;
            }

            .card-body {
                padding: 0;
            }

            #previewContainer {
                display: block !important;
                margin-top: 0 !important;
            }

            .landscape-img {
                max-width: 100%;
                max-height: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>SI UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i> Undangan</a></li>
                <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i> Notulensi</a></li>
                <li><a href="index.php?page=absensi" class="active"><i class="fas fa-user-check"></i> Absensi</a></li>
                <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i> Arsip</a></li>
                <li style="position: absolute; bottom: 0px; right: 0px; left: 0px;"><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">

            <!-- SECTION 1: LINK -->
            <div class="card">
                <div class="card-head">
                    <h3>Daftar Hadir</h3>
                </div>
                <div class="card-body" style="text-align: center;">
                    <p style="color: var(--muted); margin-bottom: 20px;">
                        Silakan buka link berikut untuk mengisi atau mengunduh daftar hadir, kemudian unggah hasilnya di bawah.
                    </p>
                    <a href="https://daftarhadir.web.bps.go.id/#/login" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Buka Website Daftar Hadir
                    </a>
                </div>
            </div>

            <!-- SECTION: TERAKHIR DIUPLOAD -->
            <?php
            // Fetch Latest Absensi from Database
            require_once __DIR__ . '/../koneksi.php';
            $query = "SELECT nama_kegiatan, tanggal_rapat, foto_absensi 
                      FROM notulensi 
                      WHERE foto_absensi IS NOT NULL AND foto_absensi != '[]' 
                      ORDER BY created_at DESC 
                      LIMIT 1";
            $result = mysqli_query($koneksi, $query);
            $row = mysqli_fetch_assoc($result);
            $absensi_photos = [];
            if ($row && !empty($row['foto_absensi'])) {
                $absensi_photos = json_decode($row['foto_absensi'], true);
            }
            ?>

            <div class="card">
                <div class="card-head">
                    <h3>Daftar Hadir Terakhir</h3>
                    <?php if ($row): ?>
                        <p style="margin:5px 0 0; font-size:14px; color:#64748b;">
                            <?= htmlspecialchars($row['nama_kegiatan']) ?>
                            (<?= date('d-m-Y', strtotime($row['tanggal_rapat'])) ?>)
                        </p>
                    <?php endif; ?>
                </div>
                <div class="card-body" style="text-align: center;">
                    <?php if (!empty($absensi_photos)): ?>
                        <div style="display: flex; flex-direction: column; gap: 20px; align-items: center;">
                            <?php foreach ($absensi_photos as $img): ?>
                                <div style="background: #fff; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <?php
                                    $imgPath = (strpos($img, '/') === false) ? 'uploads/absensi/' . $img : $img;
                                    ?>
                                    <img src="<?= htmlspecialchars($imgPath) ?>" style="max-width: 100%; height: auto; max-height: 400px; border-radius: 4px;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--muted); font-style: italic;">
                            Belum ada data absensi yang tersimpan di database.
                        </p>
                    <?php endif; ?>
                </div>
            </div>



        </div>
    </div>

    <script>
        function previewAbsensi() {
            const input = document.getElementById('absensiInput');
            const previewContainer = document.getElementById('previewContainer');
            const img = document.getElementById('imgPreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>
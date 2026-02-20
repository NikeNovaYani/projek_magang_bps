<?php
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



        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 120px;
            overflow-y: auto;
        }

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
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }


        }

        @media print {

            .btn,
            .upload-area,
            .card-head {
                display: none !important;
            }

            .container {
                display: block;
            }

            .main-content {
                margin-left: 120px;
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
        <div class="main-content">
            <div class="card">
                <div class="card-head">
                    <h3>Daftar Hadir</h3> <!-- TAMPILAN JUDUL ATAS -->
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

            <?php
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
                    <h3>Daftar Hadir Terakhir</h3> <!-- TAMPILAN JUDUL BAWAH -->
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
                            <?php foreach ($absensi_photos as $img):
                                $imgPath = (strpos($img, '/') === false) ? 'uploads/absensi/' . $img : $img;
                                $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
                            ?>
                                <div style="background: #fff; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center;">
                                    <?php if ($ext === 'pdf'): ?>
                                        <div style="width: 100%; height: 500px; background: #f8fafc; border-radius: 4px; overflow: hidden; position: relative;">
                                            <iframe src="<?= htmlspecialchars($imgPath) ?>#toolbar=0&navpanes=0&view=FitH" style="width: 100%; height: 100%; border: none;">
                                                <p>Browser Anda tidak mendukung preview PDF. <a href="<?= htmlspecialchars($imgPath) ?>">Download PDF</a>.</p>
                                            </iframe>
                                            <a href="<?= htmlspecialchars($imgPath) ?>" target="_blank" style="position: absolute; bottom: 10px; right: 10px; background: rgba(239, 68, 68, 0.9); color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;">
                                                <i class="fas fa-expand"></i> Fullscreen
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <img src="<?= htmlspecialchars($imgPath) ?>" style="max-width: 100%; height: auto; max-height: 400px; border-radius: 4px;" alt="Absensi">
                                    <?php endif; ?>
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
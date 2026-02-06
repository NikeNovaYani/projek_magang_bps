<?php
/* ================= LOAD FROM ARCHIVE ================= */
if (isset($_GET['load'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['load']);
    $jsonPath = 'arsip/' . $folder . '/undangan.json';

    // Extract Nama Kegiatan from Folder Name (Fallback)
    $parts = explode('_', $folder, 2);
    $nama_kegiatan = isset($parts[1]) ? str_replace('_', ' ', $parts[1]) : '';

    if (file_exists($jsonPath)) {
        $loaded = json_decode(file_get_contents($jsonPath), true);
        if ($loaded) {
            if ($loaded) {
                // If JSON has specific name, override (optional, currently JSON might not have it)
                if (!empty($loaded['nama_kegiatan'])) {
                    $nama_kegiatan = $loaded['nama_kegiatan'];
                }
                $nomor    = $loaded['nomor'] ?? null;
                $sifat    = $loaded['sifat'] ?? null;
                $lampiran = $loaded['lampiran'] ?? null;
                $hal      = $loaded['hal'] ?? null;
                $tglsurat = $loaded['tanggal'] ?? null; // 'tanggal' in JSON maps to 'tglsurat' var
                $kepada   = $loaded['kepada'] ?? null;
                // Note: 'isi' might not be saved in current save_undangan.php logic, check later if needed.
                $hari     = $loaded['hari_tanggal'] ?? null;
                $waktu    = $loaded['pukul_mulai'] ?? null;
                $tempat   = $loaded['tempat'] ?? null;
                $agenda   = $loaded['agenda'] ?? null;
            }
        }
    }
}

/* ================= DATABASE CONNECTION ================= */
require_once __DIR__ . '/../koneksi.php';

// Ambil data pejabat (ambil 1 saja)
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);

/* ================= SAVE TO DATABASE ================= */
$msg_success = '';
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['f_nomor'])) { // Simple check to see if form is submitted
    $nama_kegiatan = mysqli_real_escape_string($koneksi, $_POST['f_nama_kegiatan'] ?? '');
    $nomor_surat   = mysqli_real_escape_string($koneksi, $_POST['f_nomor'] ?? '');
    $sifat         = mysqli_real_escape_string($koneksi, $_POST['f_sifat'] ?? '');
    $lampiran      = mysqli_real_escape_string($koneksi, $_POST['f_lampiran'] ?? '');
    $perihal       = mysqli_real_escape_string($koneksi, $_POST['f_hal'] ?? '');
    $tanggal_surat = mysqli_real_escape_string($koneksi, $_POST['f_tglsurat'] ?? '');
    $kepada        = mysqli_real_escape_string($koneksi, $_POST['f_kepada'] ?? '');
    $isi_undangan  = mysqli_real_escape_string($koneksi, $_POST['f_isi'] ?? '');
    $hari_tanggal  = mysqli_real_escape_string($koneksi, $_POST['f_hari'] ?? '');
    $waktu_acara   = mysqli_real_escape_string($koneksi, $_POST['f_waktu'] ?? '');
    $tempat_acara  = mysqli_real_escape_string($koneksi, $_POST['f_tempat'] ?? '');
    $agenda        = mysqli_real_escape_string($koneksi, $_POST['f_agenda'] ?? '');
    $id_pejabat    = isset($pejabat['id']) ? $pejabat['id'] : 'NULL';

    $sql_insert = "INSERT INTO undangan 
    (nama_kegiatan, nomor_surat, sifat, lampiran, perihal, tanggal_surat, kepada, isi_undangan, hari_tanggal_acara, waktu_acara, tempat_acara, agenda, id_pejabat) 
    VALUES 
    ('$nama_kegiatan', '$nomor_surat', '$sifat', '$lampiran', '$perihal', '$tanggal_surat', '$kepada', '$isi_undangan', '$hari_tanggal', '$waktu_acara', '$tempat_acara', '$agenda', $id_pejabat)";

    if (mysqli_query($koneksi, $sql_insert)) {
        $msg_success = "Data undangan berhasil disimpan ke database!";
    } else {
        $msg_error = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}

/* ================= DATA DEFAULT ================= */
$nomor    = $_POST['f_nomor']    ?? ($nomor ?? 'B-32766/32766/BPS/2024');
$sifat    = $_POST['f_sifat']    ?? ($sifat ?? 'Biasa');
$lampiran = $_POST['f_lampiran'] ?? ($lampiran ?? '-');
$hal      = $_POST['f_hal']      ?? ($hal ?? 'Undangan Pembahasan Optimalisasi Anggaran Perjadin 2025');
$tglsurat = $_POST['f_tglsurat'] ?? ($tglsurat ?? date('Y-m-d'));
$kepada   = $_POST['f_kepada']   ?? ($kepada ?? "1. Seluruh Ketua Tim BPS Kota Depok\n2. PPK BPS Kota Depok");
$isi      = $_POST['f_isi']      ?? ($isi ?? 'Sehubungan dengan menjelang akan berakhirnya tahun anggaran 2025, Kepala BPS Kota Depok mengundang seluruh Ketua Tim dan PPK BPS Kota Depok untuk hadir dalam rapat yang akan diselenggarakan pada');
$hari     = $_POST['f_hari']     ?? ($hari ?? '2024-11-11');
$waktu    = $_POST['f_waktu']    ?? ($waktu ?? '13:30');
$tempat   = $_POST['f_tempat']   ?? ($tempat ?? 'Ruang Rapat BPS Kota Depok');
$agenda   = $_POST['f_agenda']   ?? ($agenda ?? 'Pembahasan Optimalisasi Anggaran');

/* ================= FUNGSI FORMATTING ================= */
function formatTanggal($date)
{
    $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        [$y, $m, $d] = explode('-', $date);
        return "Depok, $d {$bulan[$m]} $y";
    }
    return $date;
}

function formatHariTanggal($date)
{
    $hari_list = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan_list = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
    $ts = strtotime($date);
    return $hari_list[date('w', $ts)] . ', ' . date('d', $ts) . ' ' . $bulan_list[date('m', $ts)] . ' ' . date('Y', $ts);
}

function formatWaktu($w)
{
    return 'pukul ' . str_replace(':', '.', $w) . ' WIB - Selesai';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Undangan Rapat - Sistem Rapat BPS Kota Depok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== RESET ===== */
        * {
            box-sizing: border-box;
            font-family: "Arial", serif;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
            color: #0d47a1;
        }

        /* ===== CONTAINER ===== */
        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            /* kotak navigasi */
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
            /*judul navigasi */
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
            /* kotak isi */
            list-style: none;
            padding: 0;
            /* kotak teks */
            margin: 0;
        }

        .sidebar li {
            /* jarak per item */
            margin: 5px 0;
        }

        .sidebar a {
            /* teks sama choice */
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
            overflow-y: auto;
            margin-left: 140px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width:768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                order: -1;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media print {
            .sidebar {
                display: none;
            }

            .container {
                display: block;
            }

            .main-content {
                padding: 0;
                background: white;
            }

            body {
                background: white;
            }
        }

        /* Form Styling */
        .form-container {
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .form-container label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
            color: #1619ccff;
        }

        .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-container textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            height: 90px;
            /* Lebih tinggi dari input */
            resize: none;
            /* Tidak bisa di-resize (statis) */
            overflow-y: auto;
            /* Scrollable */
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-lihat {
            background: #0bbb1aff;
            color: white;
        }

        .btn-print {
            background: #ff7300ff;
            color: white;
        }

        /* Sheet Preview Styling */
        .sheet {
            width: 210mm;
            background: white;
            padding: 20mm;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            margin: 0 auto;
            min-height: 297mm;
            box-sizing: border-box;
        }

        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 100px;
            height: auto;
            margin-right: 10px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 11pt;
            color: black
        }

        .content {
            font-size: 11pt;
            line-height: 1.5;
            text-align: justify;
            color: black
        }

        .detail-table {
            margin: 15px 0 15px 30px;
        }

        .ttd {
            width: 250px;
            float: right;
            margin-top: 50px;
            text-align: center;
        }

        .ttd-image img {
            width: 150px;
            height: auto;
        }

        /* Style Khusus Kop Surat BPS */
        .kop-preview {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            /* Garis tebal bawah */
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
            color: #000;
        }


        .kop-logo img {
            width: 120px;
            height: auto;
        }

        .kop-text {
            text-align: left;
            line-height: 1.2;
        }

        .instansi-name {
            font-size: 16pt;
            font-weight: bold;
            font-style: italic;
            /* Tulisan Miring */
            letter-spacing: 0.5px;
        }

        .wilayah-name {
            font-size: 14pt;
            font-weight: bold;
            font-style: italic;
            /* Tulisan Miring */
            margin-top: 2px;
            margin-bottom: 8px;
        }

        .alamat-text {
            font-size: 8pt;
            font-weight: normal;
            font-style: normal;
            /* Tulisan Tegak */
            line-height: 1.4;
        }

        .card-head {
            padding: 0px;
            text-align: center;
        }

        .card-head h3 {
            position: relative;
            color: #0d47a1;
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0px;
            padding-bottom: 0px;
        }

        .card-head h3::after {
            content: "";
            display: block;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #1976d2, #0d47a1);
            margin-top: 8px;
            border-radius: 2px;
            animation: lineGrow 1s ease-in 0.5s forwards;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <h2>UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="index.php?page=undangan" class="active"><i class="fas fa-envelope"></i> Undangan</a></li>
                <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i> Notulensi</a></li>
                <li><a href="index.php?page=absensi"><i class="fas fa-user-check"></i> Absensi</a></li>
                <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i> Arsip</a></li>
                <li style="position: absolute; bottom: 0px; right: 0px; left: 0px;"><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <!-- FORM INPUT -->
            <div class="form-container">
                <form id="formUndangan" method="post">
                    <h3 class="card-head">Buat Undangan</h3>

                    <?php if (!empty($msg_success)): ?>
                        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                            <?= $msg_success ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($msg_error)): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                            <?= $msg_error ?>
                        </div>
                    <?php endif; ?>

                    <label>Nama Kegiatan Rapat</label>
                    <input name="f_nama_kegiatan" value="<?= htmlspecialchars($nama_kegiatan ?? '') ?>" placeholder="Contoh: Pembinaan Desa Cantik 2026" required>

                    <label>Nomor Surat</label>
                    <input name="f_nomor" value="<?= htmlspecialchars($nomor) ?>">
                    <a href="https://sites.google.com/view/permintaan-nomor-surat/no-surat-2025"
                        target="_blank" style="display: inline-block; margin-bottom: 20px; color: #1976d2; 
                text-decoration: none; font-size: 10pt;"><i class="fas fa-external-link-alt"></i> Buat Nomor Surat</a>

                    <label>Sifat</label><input name="f_sifat" value="<?php echo htmlspecialchars($sifat); ?>">

                    <label>Lampiran</label><input name="f_lampiran" value="<?php echo htmlspecialchars($lampiran); ?>">

                    <label>Hal / Perihal</label>
                    <textarea name="f_hal" rows="2"><?= htmlspecialchars($hal) ?></textarea>

                    <label>Tanggal Surat</label><input type="date" name="f_tglsurat" value="<?php echo htmlspecialchars($tglsurat); ?>">

                    <label>Kepada</label>
                    <textarea name="f_kepada" rows="3"><?= htmlspecialchars($kepada) ?></textarea>

                    <label>Isi</label><textarea name="f_isi"><?php echo htmlspecialchars($isi); ?></textarea>
                    <label>Hari, Tanggal</label><input type="date" name="f_hari" value="<?php echo htmlspecialchars($hari); ?>">
                    <label>Waktu</label><input type="time" name="f_waktu" value="<?php echo htmlspecialchars($waktu); ?>">
                    <label>Tempat</label><textarea name="f_tempat"><?php echo htmlspecialchars($tempat); ?></textarea>
                    <label>Agenda</label><input name="f_agenda" value="<?php echo htmlspecialchars($agenda); ?>">


                    <div class="btn-group">
                        <button type="submit" class="btn-lihat" onclick="submitNormal()">Simpan</button>
                        <button type="button" class="btn-print" onclick="submitCetak()">Cetak PDF</button>
                    </div>
                </form>
            </div>

            <!-- PREVIEW SURAT -->
            <div class="sheet">
                <div class="kop-preview">
                    <div class="kop-logo">
                        <img src="pdf/logo.png" alt="Logo BPS">
                    </div>

                    <div class="kop-text">
                        <div class="instansi-name">BADAN PUSAT STATISTIK</div>
                        <div class="wilayah-name">KOTA DEPOK</div>
                        <div class="alamat-text">
                            Jalan Boulevard Sektor Anggrek, Grand Depok City Kel. Kalimulya,
                            Kecamatan Cilodong Kota Depok<br>
                            Telepon (021) 7710370, Fax (021) 77825913 E-mail: bps3276@bps.go.id
                        </div>
                    </div>
                </div>

                <div class="meta">
                    <table>
                        <tr>
                            <td width="70">Nomor</td>
                            <td>:</td>
                            <td><?= $nomor ?></td>
                        </tr>
                        <tr>
                            <td>Sifat</td>
                            <td>:</td>
                            <td><?= $sifat ?></td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($lampiran) ?></td>
                        </tr>
                        <tr>
                            <td>Hal</td>
                            <td>:</td>
                            <td><strong><?= $hal ?></strong></td>
                        </tr>
                    </table>
                    <div><?= formatTanggal($tglsurat) ?></div>
                </div>

                <div class="content" style="line-height: 1.2;">
                    <div style="display: flex; align-items: flex-start;">
                        <span style="white-space: nowrap; margin-right: 10px;">Yth.</span>

                        <div style="display: flex; flex-direction: column; width: 100%;">
                            <?php
                            $daftar_penerima = explode("\n", $kepada);
                            $total_baris = count($daftar_penerima);

                            foreach ($daftar_penerima as $index => $penerima) {
                                $posisi_titik = strpos($penerima, ".");

                                if ($posisi_titik !== false) {
                                    $nomor = substr($penerima, 0, $posisi_titik + 1);
                                    $nama = substr($penerima, $posisi_titik + 1);

                                    echo '<div style="display: flex; align-items: flex-start; margin-bottom: 2px;">';
                                    echo '<span style="min-width: 25px;">' . htmlspecialchars(trim($nomor)) . '</span>';
                                    echo '<span>' . htmlspecialchars(trim($nama)) . '</span>';
                                    echo '</div>';
                                } else {
                                    echo '<div style="margin-bottom: 2px;">' . htmlspecialchars(trim($penerima)) . '</div>';
                                }
                            }
                            ?>
                            <div style="margin-top: 4px;">Di Tempat</div>
                        </div>
                    </div>

                    <p style="text-indent: 40px; text-align: justify; line-height: 1.5; margin-bottom: 15px;">
                        <?= htmlspecialchars($isi) ?>
                    </p>

                    <table class="detail-table">
                        <tr>
                            <td width="120">Hari / Tanggal</td>
                            <td>:</td>
                            <td><?= formatHariTanggal($hari) ?></td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>:</td>
                            <td><?= formatWaktu($waktu) ?></td>
                        </tr>
                        <tr>
                            <td>Tempat</td>
                            <td>:</td>
                            <td><?= $tempat ?></td>
                        </tr>
                        <tr>
                            <td>Agenda</td>
                            <td>:</td>
                            <td><?= $agenda ?></td>
                        </tr>
                    </table>

                    <p style="text-indent: 50px; text-align: justify; margin-top: 20px;">
                        Demikian undangan ini disampaikan, atas kehadiran dan perhatiannya diucapkan terima kasih.
                    </p>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 90px;">
                        <tr>
                            <td style="width: 55%;"></td>
                            <td style="width: 45%; text-align: left; vertical-align: top; position: relative;">

                                <div style="position: absolute; top: -90px; left: -130px; z-index: 1;">
                                    <?php if (!empty($pejabat['file_stempel_ttd'])): ?>
                                        <img src="pdf/<?= htmlspecialchars($pejabat['file_stempel_ttd']) ?>" style="width: 300px; opacity: 0.8;">
                                    <?php endif; ?>
                                </div>

                                <div style="position: relative; z-index: 2;">
                                    <p style="margin: 0; padding: 0;">Kepala Badan Pusat Statistik</p>
                                    <p style="margin: 0; padding: 0;">Kota Depok,</p>
                                </div>

                                <div style="height: 75px;"></div>

                                <div style="position: relative; z-index: 2;">
                                    <p style="margin: 0; padding: 0;"><strong><?= htmlspecialchars($pejabat['nama_kepala'] ?? 'Agus Marzuki Prihantoro') ?></strong></p>
                                </div>

                            </td>
                        </tr>
                    </table>

                    </td>
                    </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <script>
        const form = document.getElementById('formUndangan');

        function submitNormal() {
            form.target = '_self';
            form.action = ''; // Kembali ke halaman ini sendiri
            form.submit();
        }

        function submitCetak() {
            // Validasi Field Nama Kegiatan
            const namaKegiatan = form.querySelector('[name="f_nama_kegiatan"]').value.trim();
            if (!namaKegiatan) {
                alert('PERINGATAN: Harap isi "Nama Kegiatan Rapat" terlebih dahulu sebelum mencetak PDF agar arsip dapat dikelompokkan dengan benar.');
                return;
            }

            const data = new FormData(form);

            // Change button text to indicate loading
            const btn = document.querySelector('.btn-print');
            const originalText = btn.innerText;
            btn.innerText = 'Menyimpan & Mengunduh...';
            btn.disabled = true;

            // 1. Kirim data ke save_undangan.php?action=archive via AJAX
            fetch('pages/save_undangan.php?action=archive', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(result => {
                    // Jika sukses, result akan berisi Nama Folder (misal: 2024-02-04_Kegiatan)
                    // Kita anggap sukses jika tidak kosong dan tidak ada error PHP fatal (bisa divalidasi lebih lanjut)
                    const folderName = result.trim();

                    if (folderName && !folderName.includes('<br') && !folderName.includes('Error')) {
                        // 2. Sukses Simpan -> Trigger Download Silent via Iframe + Auto Save to Archive folder
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        // Pass folder name to generator
                        iframe.src = 'pdf/generate_undangan.php?download=true&archive_folder=' + encodeURIComponent(folderName);
                        document.body.appendChild(iframe);

                        // Show notification
                        setTimeout(() => {
                            alert('Undangan berhasil diarsipkan dan PDF diunduh!');
                            btn.innerText = originalText;
                            btn.disabled = false;
                        }, 1000);

                    } else {
                        alert('Gagal menyimpan data arsip. Response: ' + result);
                        console.log(result);
                        btn.innerText = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem.');
                    btn.innerText = originalText;
                    btn.disabled = false;
                });
        }
    </script>

</body>

</html>
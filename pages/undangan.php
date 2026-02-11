<?php
// 1. Cek Session agar tidak error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. [PENTING] Reset variabel agar tidak "bocor" dari halaman lain
// Kita set nilai default Agenda di sini.
$agenda = "Pembahasan Optimalisasi Anggaran";

/* ================= LOAD FROM ARCHIVE ================= */
// ... kode selanjutnya ...
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
                // Kita hanya timpa variabel $agenda JIKA di dalam JSON benar-benar ada isinya.
                // Ini mencegah variabel menjadi null/kosong jika JSON-nya tidak lengkap.
                if (!empty($loaded['agenda'])) {
                    $agenda = $loaded['agenda'];
                }
            }
        }
    }
}

/* ================= DATABASE CONNECTION ================= */
require_once __DIR__ . '/../koneksi.php';

// Ambil data pejabat (ambil 1 saja)
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);

// ========== EDIT MODE: LOAD DATA FROM DB ==========
$id_undangan_edit = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = false;

// Pre-fill nama_kegiatan from URL parameter (dari arsip manual "Buat" button)
if (isset($_GET['nama']) && !empty($_GET['nama'])) {
    $nama_kegiatan = $_GET['nama'];
}

if ($id_undangan_edit > 0) {
    $q_edit = mysqli_query($koneksi, "SELECT * FROM undangan WHERE id_u = '$id_undangan_edit'");
    if ($row_edit = mysqli_fetch_assoc($q_edit)) {
        $is_edit_mode = true;
        // Override variables with DB data
        $nama_kegiatan = $row_edit['nama_kegiatan'];
        $nomor         = $row_edit['nomor_surat'];
        $sifat         = $row_edit['sifat'];
        $lampiran      = $row_edit['lampiran'];
        $hal           = $row_edit['perihal'];
        $tglsurat      = $row_edit['tanggal_surat'];
        $kepada        = $row_edit['kepada'];
        $isi        = $row_edit['isi_undangan']; // [BARU] Ambil isi dari DB

        $hari     = $row_edit['hari_tanggal_acara'];

        // Parse Waktu (09:00 s.d 12:00 WIB)
        $waktu_raw = str_replace(' WIB', '', $row_edit['waktu_acara']);
        $waktu_parts = explode(' s.d ', $waktu_raw);
        $waktu_mulai_db = $waktu_parts[0] ?? '';
        $waktu_selesai_db = $waktu_parts[1] ?? '';

        // Note: Form Undangan pakai 1 field 'waktu', tapi save_undangan pakai mulai/selesai?
        // Cek form: <input type="time" name="f_waktu"> -> Hanya satu (Jam Mulai).
        $waktu    = $waktu_mulai_db;

        $tempat   = $row_edit['tempat_acara'];
        $agenda   = $row_edit['agenda'];
    }
}

/* ================= SAVE TO DATABASE ================= */
$msg_success = '';
$msg_error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['f_nomor'])) { // Simple check to see if form is submitted
    $id_u          = isset($_POST['id_u']) ? (int)$_POST['id_u'] : 0;
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

    if ($id_u > 0) {
        // === UPDATE ===
        $query = "UPDATE undangan SET 
                  nama_kegiatan = '$nama_kegiatan',
                  nomor_surat = '$nomor_surat',
                  sifat = '$sifat',
                  lampiran = '$lampiran',
                  perihal = '$perihal',
                  tanggal_surat = '$tanggal_surat',
                  kepada = '$kepada',
                  isi_undangan = '$isi_undangan',
                  hari_tanggal_acara = '$hari_tanggal',
                  waktu_acara = '$waktu_acara',
                  tempat_acara = '$tempat_acara',
                  agenda = '$agenda'
                  WHERE id_u = '$id_u'";

        if (mysqli_query($koneksi, $query)) {
            $msg_success = "Data undangan berhasil diperbarui!";
            // Refresh to ensure variables are updated
            echo "<script>alert('$msg_success'); window.location.href='index.php?page=undangan&id=$id_u';</script>";
            exit;
        } else {
            $msg_error = "Gagal memperbarui: " . mysqli_error($koneksi);
        }
    } else {
        // === INSERT ===
        $sql_insert = "INSERT INTO undangan 
        (nama_kegiatan, nomor_surat, sifat, lampiran, perihal, tanggal_surat, kepada, isi_undangan, hari_tanggal_acara, waktu_acara, tempat_acara, agenda, id_pejabat) 
        VALUES 
        ('$nama_kegiatan', '$nomor_surat', '$sifat', '$lampiran', '$perihal', '$tanggal_surat', '$kepada', '$isi_undangan', '$hari_tanggal', '$waktu_acara', '$tempat_acara', '$agenda', $id_pejabat)";

        if (mysqli_query($koneksi, $sql_insert)) {
            $msg_success = "Data undangan berhasil disimpan ke database!";
            $new_id = mysqli_insert_id($koneksi);
            // Redirect to Edit Mode
            echo "<script>alert('$msg_success'); window.location.href='index.php?page=undangan&id=$new_id';</script>";
            exit;
        } else {
            $msg_error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
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
$tempat   = $_POST['f_tempat']   ?? ($tempat ?? 'Ruang Rapat BPS Kota Depok');
// Kita gunakan variabel $agenda yang sudah kita atur di atas (entah itu default, dari JSON, atau dari DB).
// Jika ada POST (saat tombol simpan ditekan), pakai data POST.
$agenda = $_POST['f_agenda'] ?? $agenda; // Default explicit value

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
    <script src="/projek_magang/tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
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

        /* TinyMCE Fix */
        .tox-promotion {
            display: none !important;
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
                    <input type="hidden" name="id_u" value="<?= $id_undangan_edit ?>"> <!-- ID untuk Edit Mode -->
                    <h3 class="card-head"><?= $is_edit_mode ? 'Edit Undangan' : 'Buat Undangan' ?></h3>

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
                    <label>Agenda</label><textarea name="f_agenda"><?php echo htmlspecialchars($agenda); ?></textarea>


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

                    <?php
                    // [BARU] Pastikan diakhiri dengan titik dua ( : ) sesuai request
                    $isi_preview = trim($isi);
                    if (substr($isi_preview, -1) === '.') {
                        $isi_preview = substr($isi_preview, 0, -1); // Hapus titik di akhir jika ada
                    }
                    if (substr($isi_preview, -1) !== ':') {
                        $isi_preview .= ' :';
                    }
                    ?>
                    <p style="text-indent: 40px; text-align: justify; line-height: 1.5; margin-bottom: 15px;">
                        <?= htmlspecialchars($isi_preview) ?>
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
            // Validasi Field Nama Kegiatan
            const namaKegiatan = form.querySelector('[name="f_nama_kegiatan"]').value.trim();
            if (!namaKegiatan) {
                // Tampilkan notifikasi di atas form
                let notif = document.querySelector('.custom-alert');
                if (!notif) {
                    notif = document.createElement('div');
                    notif.className = 'custom-alert';
                    notif.style.cssText = 'background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ffeeba;';
                    const container = document.querySelector('.form-container form');
                    container.insertBefore(notif, container.children[2]); // Insert after h3 or messages
                }
                notif.innerHTML = '<strong>PERINGATAN:</strong> Harap isi "Nama Kegiatan Rapat" terlebih dahulu.';

                // Scroll ke atas
                document.querySelector('.form-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return;
            }

            form.target = '_self';
            form.action = ''; // Kembali ke halaman ini sendiri
            form.submit();
        }

        function submitCetak() {
            const form = document.querySelector('form'); // Pastikan selektor form benar

            // Validasi Field Nama Kegiatan
            const namaKegiatan = form.querySelector('[name="f_nama_kegiatan"]').value.trim();
            if (!namaKegiatan) {
                alert('PERINGATAN: Harap isi "Nama Kegiatan Rapat" terlebih dahulu.');
                return;
            }

            const data = new FormData(form);

            // Ubah tombol jadi Loading
            const btn = document.querySelector('.btn-print');
            const originalText = btn.innerText;
            btn.innerText = 'Menyimpan & Mengunduh...';
            btn.disabled = true;

            // TinyMCE Trigger Save if needed
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            // 1. Kirim data ke save_undangan.php
            fetch('pages/save_undangan.php', { // Hapus ?action=archive jika tidak perlu logika khusus
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(result => {
                    // result sekarang berisi ID Undangan (misal: "15")
                    let idUndangan = result.trim();

                    // Cek apakah result benar-benar angka (ID Valid)
                    if (!isNaN(idUndangan) && idUndangan > 0) {

                        // 2. Sukses Simpan -> Trigger Download via Iframe menggunakan ID
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';

                        // PERUBAHAN UTAMA DI SINI:
                        // Kita kirim parameter ?id=... bukan archive_folder
                        iframe.src = 'pdf/generate_undangan.php?download=true&id=' + idUndangan;

                        document.body.appendChild(iframe);

                        // Notifikasi Sukses
                        setTimeout(() => {
                            // alert('Undangan berhasil disimpan dan PDF sedang diunduh.');
                            btn.innerText = originalText;
                            btn.disabled = false;

                            // Opsional: Redirect ke halaman list undangan setelah cetak
                            // window.location.href = 'index.php?page=undangan'; 
                        }, 2000);

                    } else {
                        // Jika return bukan angka (berarti error PHP)
                        alert('Gagal menyimpan data. Error: ' + result);
                        console.log(result);
                        btn.innerText = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem saat menghubungi server.');
                    btn.innerText = originalText;
                    btn.disabled = false;
                });
        }
    </script>

</body>

</html>
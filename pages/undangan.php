<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$agenda = "Pembahasan Optimalisasi Anggaran";
if (isset($_GET['load'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['load']);
    $jsonPath = 'arsip/' . $folder . '/undangan.json';

    $parts = explode('_', $folder, 2);
    $nama_kegiatan = isset($parts[1]) ? str_replace('_', ' ', $parts[1]) : '';

    if (file_exists($jsonPath)) {
        $loaded = json_decode(file_get_contents($jsonPath), true);
        if ($loaded) {
            if (!empty($loaded['nama_kegiatan'])) {
                $nama_kegiatan = $loaded['nama_kegiatan'];
            } else {
                $nomor    = $loaded['nomor'] ?? null;
                $sifat    = $loaded['sifat'] ?? null;
                $lampiran = $loaded['lampiran'] ?? null;
                $hal      = $loaded['hal'] ?? null;
                $tglsurat = $loaded['tanggal'] ?? null;
                $kepada   = $loaded['kepada'] ?? null;
                $hari     = $loaded['hari_tanggal'] ?? null;
                $waktu    = $loaded['pukul_mulai'] ?? null;
                $tempat   = $loaded['tempat'] ?? null;
                if (!empty($loaded['agenda'])) {
                    $agenda = $loaded['agenda'];
                }
            }
        }
    }
}

/* ================= DATABASE CONNECTION ================= */
require_once __DIR__ . '/../koneksi.php';
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);

// ========== NAMA TANDA TANGAN ==========
$nama_pimpinan = "Agus Marzuki Prihantoro";

// ========== EDIT MODE: LOAD DATA DARI DATABASE ==========
$id_undangan_edit = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit_mode = false;
if (isset($_GET['nama']) && !empty($_GET['nama'])) {
    $nama_kegiatan = $_GET['nama'];
}

if ($id_undangan_edit > 0) {
    $q_edit = mysqli_query($koneksi, "SELECT * FROM undangan WHERE id_u = '$id_undangan_edit'");
    if ($row_edit = mysqli_fetch_assoc($q_edit)) {
        $is_edit_mode = true;
        $nama_kegiatan = $row_edit['nama_kegiatan'];
        $nomor         = $row_edit['nomor_surat'];
        $sifat         = $row_edit['sifat'];
        $lampiran      = $row_edit['lampiran'];
        $hal           = $row_edit['perihal'];
        $tglsurat      = $row_edit['tanggal_surat'];
        $kepada        = $row_edit['kepada'];
        $isi        = $row_edit['isi_undangan'];
        $hari     = $row_edit['hari_tanggal_acara'];
        $waktu_raw = str_replace(' WIB', '', $row_edit['waktu_acara']);
        $waktu_parts = explode(' s.d ', $waktu_raw);
        $waktu_mulai_db = $waktu_parts[0] ?? '';
        $waktu_selesai_db = $waktu_parts[1] ?? '';
        $waktu    = $waktu_mulai_db;
        $tempat   = $row_edit['tempat_acara'];
        $agenda   = $row_edit['agenda'];
    }
}

/* ================= SAVE KE DATABASE ================= */
$msg_success = '';
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['f_nomor'])) {
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
            $msg_success = "Perubahan berhasil disimpan!";
            echo "<!DOCTYPE html><body>";
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '$msg_success',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href='index.php?page=undangan&id=$id_u';
                });
            </script>";
            echo "</body></html>";
            exit;
        } else {
            $msg_error = "Gagal memperbarui: " . mysqli_error($koneksi);
        }
    } else {

        $check_duplicate = mysqli_query($koneksi, "SELECT id_u FROM undangan WHERE nama_kegiatan = '$nama_kegiatan'");
        if (mysqli_num_rows($check_duplicate) > 0) {
            echo "<!DOCTYPE html><body>";
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Nama kegiatan \"$nama_kegiatan\" sudah ada! Silakan gunakan nama lain.',
                    icon: 'error'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            echo "</body></html>";
            exit;
        }

        $sql_insert = "INSERT INTO undangan 
        (nama_kegiatan, nomor_surat, sifat, lampiran, perihal, tanggal_surat, kepada, isi_undangan, hari_tanggal_acara, waktu_acara, tempat_acara, agenda, id_pejabat) 
        VALUES 
        ('$nama_kegiatan', '$nomor_surat', '$sifat', '$lampiran', '$perihal', '$tanggal_surat', '$kepada', '$isi_undangan', '$hari_tanggal', '$waktu_acara', '$tempat_acara', '$agenda', $id_pejabat)";

        if (mysqli_query($koneksi, $sql_insert)) {
            $msg_success = "Data undangan berhasil disimpan ke arsip!";
            $new_id = mysqli_insert_id($koneksi);
            echo "<!DOCTYPE html><body>";
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: '$msg_success',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href='index.php?page=undangan&id=$new_id';
                });
            </script>";
            echo "</body></html>";
            exit;
        } else {
            $msg_error = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}

/* ================= DATA DEFAULT DI FORM INPUT ================= */
$nomor    = $_POST['f_nomor']    ?? ($nomor ?? 'B-32766/32766/BPS/2024');
$sifat    = $_POST['f_sifat']    ?? ($sifat ?? 'Biasa');
$lampiran = $_POST['f_lampiran'] ?? ($lampiran ?? '-');
$hal      = $_POST['f_hal']      ?? ($hal ?? 'Undangan Pembahasan Optimalisasi Anggaran Perjadin 2025');
$tglsurat = $_POST['f_tglsurat'] ?? ($tglsurat ?? date('Y-m-d'));
$kepada   = $_POST['f_kepada']   ?? ($kepada ?? "1. Seluruh Ketua Tim BPS Kota Depok\n2. PPK BPS Kota Depok");
$isi      = $_POST['f_isi']      ?? ($isi ?? 'Sehubungan dengan menjelang akan berakhirnya tahun anggaran 2025, Kepala BPS Kota Depok mengundang seluruh Ketua Tim dan PPK BPS Kota Depok untuk hadir dalam rapat yang akan diselenggarakan pada');
$hari     = $_POST['f_hari']     ?? ($hari ?? 'Y-m-d');
$waktu    = $_POST['f_waktu']    ?? ($waktu ?? '13:30');
$tempat   = $_POST['f_tempat']   ?? ($tempat ?? 'Ruang Rapat BPS Kota Depok');
$tempat   = $_POST['f_tempat']   ?? ($tempat ?? 'Ruang Rapat BPS Kota Depok');
$agenda = $_POST['f_agenda'] ?? $agenda;

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
    <title>Undangan Rapat - SI UANG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/projek_magang/tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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



        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            margin-left: 130px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 1366px) {
            .main-content .container {
                display: block !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .form-container {
                width: 500px !important;
                max-width: 100% !important;
                margin-bottom: 10px;
            }

            .sheet {
                width: 210mm !important;
                max-width: 100% !important;
                order: 2;
                position: static !important;
                margin-left: 0px !important;
            }

            .main-content .sheet .kop-text .instansi-name {
                font-size: 14pt !important;
            }

            .main-content .sheet .kop-text .wilayah-name {
                font-size: 12pt !important;
            }

            .main-content .sheet .content {
                font-size: 11pt !important;
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

        .form-container {
            width: 500px;
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
            margin-top: 20px;
            font-size: 12pt;
            color: #1619ccff;
        }

        .form-container input {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-container textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 0px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            height: 90px;
            resize: none;
            overflow-y: auto;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn {
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(0, 0, 0, .10);
            transition: .15s;
            font-size: 10px;
            white-space: nowrap;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn.save {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .btn.print {
            background: #ff7300ff;
            color: white;
        }

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
            font-size: 12pt;
            color: black
        }

        .content {
            font-size: 12pt;
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

        .kop-preview {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
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
            letter-spacing: 0.5px;
        }

        .wilayah-name {
            font-size: 14pt;
            font-weight: bold;
            font-style: italic;
            margin-top: 2px;
            margin-bottom: 8px;
        }

        .alamat-text {
            font-size: 8pt;
            font-weight: normal;
            font-style: normal;
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

        @keyframes lineGrow {
            from {
                width: 0;
                left: 50%;
            }

            to {
                width: 100%;
                left: 0;
            }
        }

        .tox-promotion {
            display: none !important;
        }
    </style>
</head>

<body>

    <div class="container">


        <div class="main-content">
            <!-- TAMPILAN FORM BUAT UNDANGAN KIRI-->
            <div class="form-container">
                <form id="formUndangan" method="post">
                    <input type="hidden" name="id_u" value="<?= $id_undangan_edit ?>">
                    <div class="card-head">
                        <h3><?= $is_edit_mode ? 'Edit Undangan' : 'Buat Undangan' ?></h3>
                    </div>

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
                    <input name="f_nama_kegiatan" value="<?= htmlspecialchars($nama_kegiatan ?? '') ?>" placeholder="Samakan dengan di Notulensi" required style="font-size: small ">

                    <label>Nomor Surat</label>
                    <input name="f_nomor" value="<?= htmlspecialchars($nomor) ?>">

                    <!-- EMBEDED LINK KEHALAMAN SI MANSUR -->
                    <a href="https://s.bps.go.id/mansur_depok"
                        target="_blank" style="text-decoration: none; color: #3b82f6; font-weight: bold; 
                font-size: 13px; display: inline-flex; align-items: center; gap: 5px; padding: 6px 10px; 
                background: #eff6ff; border-radius: 6px; border: 1px solid #bfdbfe;">
                        <i class="fas fa-external-link-alt"></i> Link Buat Nomor Surat
                    </a>

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


                    <div class="actions" style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                        <button class="btn save" type="button" onclick="saveNotulensi()" style="flex: 1; justify-content: center; font-size: 16px; padding: 8px;"><i class="fas fa-save"></i> Simpan Undangan</button>
                        <button class="btn print" type="button" onclick="cetakPDF(this)" style="flex: 1; justify-content: center; font-size: 16px; padding: 8px;"><i class="fas fa-print"></i> Cetak PDF</button>
                    </div>
                </form>
            </div>

            <!-- TAMPILAN PREVIEW SURAT UNDANGAN KANAN-->
            <div class="sheet">
                <div class="kop-preview">
                    <!-- GAMBAR LOGO BPS -->
                    <div class="kop-logo">
                        <img src="pdf/logo.png" alt="Logo BPS">
                    </div>
                    <!-- KOP SURAT UNDANGAN -->
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
                    $isi_preview = trim($isi);
                    if (substr($isi_preview, -1) === '.') {
                        $isi_preview = substr($isi_preview, 0, -1);
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

                    <!-- PARAGRAF PENUTUP UNDANGAN -->
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

                                <!-- TANDA TANGAN -->
                                <div style="position: relative; z-index: 2;">
                                    <p style="margin: 0; padding: 0;">Kepala Badan Pusat Statistik</p>
                                    <p style="margin: 0; padding: 0;">Kota Depok,</p>
                                </div>

                                <div style="height: 75px;"></div>

                                <div style="position: relative; z-index: 2;">
                                    <p style="margin: 0; padding: 0;"><strong><?= htmlspecialchars($nama_pimpinan ?? $pejabat['nama_kepala']) ?></strong></p>
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

        function saveNotulensi() {
            const namaKegiatan = form.querySelector('[name="f_nama_kegiatan"]').value.trim();
            if (!namaKegiatan) {
                let notif = document.querySelector('.custom-alert');
                if (!notif) {
                    notif = document.createElement('div');
                    notif.className = 'custom-alert';
                    notif.style.cssText = 'background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ffeeba;';
                    const container = document.querySelector('.form-container form');
                    container.insertBefore(notif, container.children[2]);
                }
                notif.innerHTML = '<strong>PERINGATAN:</strong> Harap isi "Nama Kegiatan Rapat" terlebih dahulu.';

                document.querySelector('.form-container').scrollIntoView({
                    behavior: 'smooth'
                });
                return;
            }

            form.target = '_self';
            form.action = '';
            form.submit();
        }

        function cetakPDF(btn) {
            const form = document.getElementById('formUndangan');

            const namaKegiatan = form.querySelector('[name="f_nama_kegiatan"]').value.trim();
            if (!namaKegiatan) {
                alert('PERINGATAN: Harap isi "Nama Kegiatan Rapat" terlebih dahulu.');
                return;
            }

            const data = new FormData(form);

            const originalText = btn.innerText;
            btn.innerText = 'Menyimpan & Mengunduh...';
            btn.disabled = true;

            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }

            fetch('pages/save_undangan.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.text())
                .then(result => {
                    let idUndangan = result.trim();

                    if (!isNaN(idUndangan) && idUndangan > 0) {

                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';

                        iframe.src = 'pdf/generate_undangan.php?download=true&id=' + idUndangan;

                        document.body.appendChild(iframe);

                        setTimeout(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;

                            window.location.href = 'index.php?page=undangan';
                        }, 2000);

                    } else {
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
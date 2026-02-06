<?php
session_start();

$data = $_SESSION['undangan'] ?? [];

if (empty($data)) {
    echo "<p>Data undangan belum tersedia. Silakan simpan undangan terlebih dahulu.</p>";
    exit;
}

/* helper escape */
function e($v)
{
    return nl2br(htmlspecialchars($v ?? ''));
}

require_once __DIR__ . '/../koneksi.php';
$query_pejabat = mysqli_query($koneksi, "SELECT * FROM pejabat LIMIT 1");
$pejabat = mysqli_fetch_assoc($query_pejabat);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Preview Undangan</title>

    <!-- CSS khusus preview undangan -->
    <link rel="stylesheet" href="assets/css/undangan-preview.css">
</head>

<body>

    <div class="sheet">

        <!-- KOP -->
        <div class="kop">
            <img src="logo.png" alt="Logo BPS">
            <div class="kop-text">
                <h1>BADAN PUSAT STATISTIK</h1>
                <h2>KOTA DEPOK</h2>
                <p>Jalan Boulevard Sektor Anggrek, Grand Depok City Kel. Kalimulya, Kecamatan Cilodong Kota Depok</p>
                <p>Telp. (021)7713037 Fax. (021)7728519 Email: bps3276@bps.go.id</p>
            </div>
        </div>

        <!-- META -->
        <div class="meta">
            <table>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td><?= e($data['nomor']) ?></td>
                </tr>
                <tr>
                    <td>Sifat</td>
                    <td>:</td>
                    <td><?= e($data['sifat']) ?></td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td><?= e($data['lampiran']) ?></td>
                </tr>
                <tr>
                    <td style="max-width:380px;">Hal</td>
                    <td>:</td>
                    <td><strong><?= e($data['hal']) ?></strong></td>
                </tr>
            </table>

            <div class="tanggal">
                <?= e($data['tglsurat_fmt'] ?? $data['tglsurat']) ?>
            </div>
        </div>

        <!-- ISI SURAT -->
        <div class="content">

            <p>
                Yth.<br>
                <?= e($data['kepada']) ?><br>
                di Tempat
            </p>

            <p class="indent">
                Dalam rangka <?= e($data['isi']) ?>,
                maka kami mengundang Bapak/Ibu Saudara seluruh pegawai organik
                BPS Kota Depok hadir pada rapat sbb:
            </p>

            <table class="agenda-table">
                <tr>
                    <td>Hari, Tanggal</td>
                    <td>:</td>
                    <td><?= e($data['hari_fmt'] ?? $data['hari']) ?></td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td><?= e($data['waktu_fmt'] ?? $data['waktu']) ?></td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td><?= e($data['tempat']) ?></td>
                </tr>
                <tr>
                    <td>Agenda</td>
                    <td>:</td>
                    <td><?= e($data['agenda']) ?></td>
                </tr>
            </table>

            <p class="indent">
                Demikian undangan ini disampaikan, atas kehadiran dan perhatiannya
                diucapkan terima kasih.
            </p>

            <div class="ttd">
                <p>
                    Kepala Badan Pusat Statistik<br>
                    Kota Depok,
                </p>
                <br><br><br>
                <p><strong><?= e($pejabat['nama_kepala'] ?? 'Agus Marzuki Prihantoro') ?></strong></p>
            </div>

            <div class="clear"></div>
        </div>

    </div>

    <div style="text-align:right; margin:20px;">
        <a href="index.php?page=generate_undangan" target="_blank">
            <button>Cetak PDF</button>
        </a>
    </div>

</body>

</html>
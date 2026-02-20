<?php
$d = $_SESSION['undangan'] ?? [];

$nama_pimpinan = "Agus Marzuki Prihantoro"; // ===== NAMA TANDA TANGAN =====
$nomor      = $d['nomor'] ?? '[Nomor Surat]';
$sifat      = $d['sifat'] ?? 'Biasa';
$lampiran   = $_POST['f_lampiran'] ?? '-';
$hal        = $d['hal'] ?? 'Undangan Rapat';
$tglsurat   = $d['tanggal'] ?? date('Y-m-d');
$kepada     = $d['kepada'];
$isi        = $d['isi'] ?? ($_POST['f_isi']);
$isi        = trim($isi);
if (substr($isi, -1) === '.') {
    $isi = substr($isi, 0, -1);
}
if (substr($isi, -1) !== ':') {
    $isi .= ' :';
}
$hari       = $d['tanggal_acara'] ?? date('Y-m-d');
$waktu      = ($d['pukul_mulai'] ?? '09:00') . ' - ' . ($d['pukul_selesai'] ?? 'Selesai');
$tempat     = $d['tempat'] ?? '-';
$agenda     = $d['agenda'] ?? '-';
$nama_ttd   = $d['pimpinan'] ?? 'Agus Marzuki Prihantoro';

if (!function_exists('formatTanggal')) {
    function formatTanggal($date)
    {
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $ts = strtotime($date);
        return date('d', $ts) . ' ' . $bulan[date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
}

if (!function_exists('formatHariTanggal')) {
    function formatHariTanggal($date)
    {
        $hari_arr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $ts = strtotime($date);
        $hari_en = date('l', $ts);
        return $hari_arr[$hari_en] . ', ' . formatTanggal($date);
    }
}

if (!function_exists('formatWaktu')) {
    function formatWaktu($waktu)
    {
        return $waktu . ' WIB';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cetak Undangan</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
        }

        .sheet {
            background: white;
            box-sizing: border-box;
            font-size: 11pt;
        }

        table.kop-layout {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 0px;
            margin-bottom: 15px;
        }

        td.kop-logo {
            width: 50px;
            height: auto;
            margin-right: 0px;
        }

        td.kop-text {
            vertical-align: middle;

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

        table.meta-layout {
            width: 100%;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        td.meta-left {
            width: 60%;
            vertical-align: top;
        }

        td.meta-right {
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        /* Tabel kecil untuk Nomor/Sifat/Hal */
        table.meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .content {
            font-size: 11pt;
            line-height: 1.5;
            text-align: justify;
        }

        .detail-table {
            margin: 15px 0 15px 30px;
            width: 90%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .ttd-container {
            width: 100%;
            text-align: right;
            margin-top: 50px;
        }

        .ttd-box {
            display: block;
            width: 250px;
            text-align: center;
            float: right;
        }
    </style>
</head>

<body>
    <!----- TAMPILAN TEMPLATE CETAK UNDANGAN ----->
    <div class="sheet">
        <table class="kop-layout" style="padding-bottom: 0px;">
            <tr>
                <!-- GAMBAR LOGO BPS -->
                <td class="kop-logo">
                    <img src="logo.png" alt="Logo BPS" style="width: 120px; height: auto; margin-right: 0px;">
                </td>
                <!-- KOP SURAT UNDANGAN -->
                <td class="kop-text">
                    <div class="instansi-name">BADAN PUSAT STATISTIK</div>
                    <div class="wilayah-name">KOTA DEPOK</div>
                    <div class="alamat-text">
                        Jalan Boulevard Sektor Anggrek, Grand Depok City Kel. Kalimulya,
                        Kecamatan Cilodong Kota Depok<br>
                        Telepon (021) 7710370, Fax (021) 77825913 E-mail: bps3276@bps.go.id
                    </div>
                </td>
            </tr>
        </table>

        <table class="meta-layout">
            <tr>
                <td class="meta-left">
                    <table class="meta-table">
                        <tr>
                            <td width="70">Nomor</td>
                            <td width="15">:</td>
                            <td><?= htmlspecialchars($nomor) ?></td>
                        </tr>

                        <tr>
                            <td>Sifat</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($sifat) ?></td>
                        </tr>

                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td><?= htmlspecialchars($lampiran) ?></td>
                        </tr>

                        <tr>
                            <td>Hal</td>
                            <td>:</td>
                            <td><strong><?= htmlspecialchars($hal) ?></strong></td>
                        </tr>
                    </table>
                </td>

                <td class="meta-right">
                    Depok, <?= formatTanggal($tglsurat) ?>
                </td>
            </tr>
        </table>

        <div class="content" style="line-height: 1.2;">
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="vertical-align: top; width: 40px; white-space: nowrap;">Yth.</td>

                    <td style="vertical-align: top;">
                        <table style="width: 100%; border-collapse: collapse; border: none;">
                            <?php
                            $kepada_clean = preg_replace('/\\\\r\\\\n|\\\\n|\\\\r/', "\n", $kepada);
                            $kepada_clean = str_replace(array("\r\n", "\r"), "\n", $kepada_clean);
                            $daftar_penerima = explode("\n", $kepada_clean);
                            foreach ($daftar_penerima as $penerima) {
                                $posisi_titik = strpos($penerima, ".");

                                if ($posisi_titik !== false) {
                                    $nomor = substr($penerima, 0, $posisi_titik + 1);
                                    $nama = substr($penerima, $posisi_titik + 1);

                                    echo '<tr>';
                                    echo '<td style="vertical-align: top; width: 25px;">' . htmlspecialchars(trim($nomor)) . '</td>';
                                    echo '<td style="vertical-align: top;">' . htmlspecialchars(trim($nama)) . '</td>';
                                    echo '</tr>';
                                } else {
                                    echo '<tr><td colspan="2" style="vertical-align: top;">' . htmlspecialchars(trim($penerima)) . '</td></tr>';
                                }
                            }
                            ?>
                            <tr>
                                <td colspan="2" style="padding-top: 4px;">Di Tempat</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <p style="text-indent: 40px; text-align: justify; line-height: 1.5; margin-bottom: 15px;">
            <?= htmlspecialchars($isi) ?>
        </p>

        <table class="detail-table">
            <tr>
                <td width="120">Hari / Tanggal</td>
                <td width="15">:</td>
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
                <td><?= nl2br(htmlspecialchars($tempat)) ?></td>
            </tr>

            <tr>
                <td>Agenda</td>
                <td>:</td>
                <td><?= nl2br(htmlspecialchars($agenda)) ?></td>
            </tr>

        </table>

        <!-- PARAGRAF PENUTUP UNDANGAN-->
        <p style="text-indent: 50px; text-align: justify; margin-top: 20px;">
            demikian undangan ini disampaikan, atas kehadiran dan perhatiannya diucapkan terima kasih.
        </p>

        <?php
        $ttd_file = $pejabat['file_stempel_ttd'] ?? 'ttd1.png';
        $path = __DIR__ . '/' . $ttd_file;

        $base64 = '';
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data_img = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
        }
        ?>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: center; vertical-align: top;">

                    <!-- TANDA TANGAN -->
                    <p style="margin: 0; padding: 0; line-height: 1.2;">Kepala Badan Pusat Statistik</p>
                    <p style="margin: 0; padding: 0; line-height: 1.2;">Kota Depok,</p>

                    <div style="height: 0px; overflow: visible; position: relative; z-index: -1;">
                        <?php if ($base64): ?>
                            <img src="<?= $base64 ?>"
                                style="width: 270px; height: auto; opacity: 0.8; margin-top: -110px; 
                                        margin-left: -120px; margin-bottom: -70px;">
                        <?php endif; ?>
                    </div>

                    <div style="height: 20px;"></div>

                    <p style="margin: 0; padding: 0; position: relative; z-index: 2;">
                        <strong><?= htmlspecialchars($nama_pimpinan ?? $pejabat['nama_kepala']) ?></strong>
                    </p>
                </td>
            </tr>
        </table>
    </div>
    </div>
</body>

</html>
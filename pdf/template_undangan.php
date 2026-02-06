<?php
// 1. AMBIL DATA DARI SESSION
// Kita ambil data yang dikirim dari form input (lewat generate_undangan.php)
$d = $_SESSION['undangan'] ?? [];

// Mapping variabel agar sesuai dengan HTML kamu
$nomor      = $d['nomor'] ?? '[Nomor Surat]';
$sifat      = $d['sifat'] ?? 'Biasa';
$lampiran   = $_POST['f_lampiran'] ?? '-';
$hal        = $d['hal'] ?? 'Undangan Rapat';
$tglsurat   = $d['tanggal'] ?? date('Y-m-d'); // Tanggal pembuatan surat
$kepada     = $d['kepada'] ?? 'Bapak/Ibu Terlampir';
$isi      = $_POST['f_isi']      ?? 'Sehubungan dengan menjelang akan berakhirnya tahun anggaran 2025, Kepala BPS Kota Depok mengundang seluruh Ketua Tim dan PPK BPS Kota Depok untuk hadir dalam rapat yang akan diselenggarakan pada';
// Data Acara
$hari       = $d['tanggal_acara'] ?? date('Y-m-d');
$waktu      = ($d['pukul_mulai'] ?? '09:00') . ' - ' . ($d['pukul_selesai'] ?? 'Selesai');
$tempat     = $d['tempat'] ?? '-';
$agenda     = $d['agenda'] ?? '-';

// Tanda Tangan
$nama_ttd   = $d['pimpinan'] ?? 'Agus Marzuki Prihantoro'; // Default jika kosong

// 2. HELPER FUNCTIONS (Format Tanggal Indonesia)
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

// Helper sederhana untuk waktu (jika diperlukan format khusus)
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
        /* Reset default browser margin agar PDF rapi */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial;
        }

        /* Style Utama (Diadaptasi dari kode Preview kamu) */
        .sheet {
            /* Kita hapus width/shadow karena ini PDF, bukan layar */
            background: white;
            box-sizing: border-box;
            font-size: 11pt;
        }

        /* KOP SURAT (Menggunakan Tabel untuk pengganti Flexbox agar aman di PDF) */
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

        /* META DATA (Nomor, Sifat, Tanggal) */
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

        /* CONTENT */
        .content {
            font-size: 11pt;
            line-height: 1.5;
            text-align: justify;
        }

        /* DETAIL TABLE (Jadwal) */
        .detail-table {
            margin: 15px 0 15px 30px;
            width: 90%;
            border-collapse: collapse;
        }

        .detail-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* TANDA TANGAN */
        /* Menggunakan float di mPDF kadang tricky, kita gunakan container khusus */
        .ttd-container {
            width: 100%;
            text-align: right;
            /* Geser konten ke kanan */
            margin-top: 50px;
        }

        .ttd-box {
            display: inline-block;
            width: 250px;
            text-align: center;
            float: right;
        }
    </style>
</head>

<body>

    <div class="sheet">
        <table class="kop-layout" style="padding-bottom: 0px;">
            <tr>
                <td class="kop-logo">
                    <img src="logo.png" alt="Logo BPS" style="width: 120px; height: auto; margin-right: 0px;">
                </td>
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
                            $daftar_penerima = explode("\n", $kepada);
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

        <p style="text-indent: 50px; text-align: justify; margin-top: 20px;">
            Demikian undangan ini disampaikan, atas kehadiran dan perhatiannya diucapkan terima kasih.
        </p>

        <?php
        // 1. Ambil gambar dan ubah ke Base64 (Langkah ini wajib agar gambar tidak hilang)
        $ttd_file = $pejabat['file_stempel_ttd'] ?? 'ttd1.png';
        $path = __DIR__ . '/' . $ttd_file; // Pastikan file gambar ada di folder yang sama (folder pdf)

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
                        <strong><?= htmlspecialchars($pejabat['nama_kepala'] ?? 'Agus Marzuki Prihantoro') ?></strong>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    </div>

</body>

</html>
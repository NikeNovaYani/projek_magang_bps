<?php
/**
 * File: printundangan.php
 * Deskripsi: Menghasilkan PDF berdasarkan data yang dikirim dari form undangan.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// 1. Tangkap data dari POST (jika dipanggil via form) atau GET (jika default)
// Catatan: Agar data dari form terkirim, form di undangan.php harus diarahkan ke sini
$nomor    = $_POST['f_nomor']    ?? 'B-32766/32766/BPS/2024';
$sifat    = $_POST['f_sifat']    ?? 'Biasa';
$lampiran = $_POST['f_lampiran'] ?? '-';
$hal      = $_POST['f_hal']      ?? 'Undangan Pembahasan Optimalisasi Anggaran Perjadin 2025';
$tglsurat = $_POST['f_tglsurat'] ?? date('Y-m-d');
$kepada   = $_POST['f_kepada']   ?? "1. Seluruh Ketua Tim BPS Kota Depok\n2. PPK BPS Kota Depok";
$hari     = $_POST['f_hari']     ?? '2024-11-11';
$waktu    = $_POST['f_waktu']    ?? '13:30';
$tempat   = $_POST['f_tempat']   ?? 'Ruang Rapat BPS Kota Depok';
$agenda   = $_POST['f_agenda']   ?? 'Pembahasan Optimalisasi Anggaran';

/* ================= FUNGSI FORMATTING (Sama dengan di undangan.php) ================= */
function formatTanggal($date) {
    $bulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        [$y,$m,$d] = explode('-', $date);
        return "Depok, $d {$bulan[$m]} $y";
    }
    return $date;
}

function formatHariTanggal($date) {
    $hari_list = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan_list = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    $ts = strtotime($date);
    return $hari_list[date('w',$ts)].', '.date('d',$ts).' '.$bulan_list[date('m',$ts)].' '.date('Y',$ts);
}

function formatWaktu($w) {
    return 'pukul ' . str_replace(':', '.', $w) . ' WIB - Selesai';
}

// 2. Inisialisasi mPDF
$mpdf = new \Mpdf\Mpdf([
    'format' => 'A4',
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 25,
    'margin_bottom' => 25,
]);

// 3. Template HTML (Sesuaikan CSS agar kompatibel dengan mPDF)
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; line-height: 1.5; }
        .kop { border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop table { width: 100%; }
        .logo { width: 70px; }
        .kop-text { text-align: left; padding-left: 10px; }
        .kop-text strong { font-size: 14pt; }
        
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-left { width: 60%; vertical-align: top; }
        .meta-right { width: 40%; text-align: right; vertical-align: top; }
        
        .content { margin-top: 10px; }
        .detail-table { margin: 15px 0 15px 30px; }
        .detail-table td { vertical-align: top; padding: 3px 0; }
        
        .ttd { width: 250px; float: right; margin-top: 40px; text-align: center; }
        .ttd-space { height: 70px; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop">
        <table>
            <tr>
                <td class="logo"><img src="logo.png"></td>
                <td class="kop-text">
                    <strong>BADAN PUSAT STATISTIK</strong><br>
                    KOTA DEPOK
                </td>
            </tr>
        </table>
    </div>

    <!-- META DATA -->
    <table class="meta-table">
        <tr>
            <td class="meta-left">
                <table>
                    <tr><td width="70">Nomor</td><td width="10">:</td><td>'. $nomor .'</td></tr>
                    <tr><td>Sifat</td><td>:</td><td>'. $sifat .'</td></tr>
                    <tr><td>Lampiran</td><td>:</td><td>'. $lampiran .'</td></tr>
                    <tr><td>Hal</td><td>:</td><td><strong>'. $hal .'</strong></td></tr>
                </table>
            </td>
            <td class="meta-right">
                '. formatTanggal($tglsurat) .'
            </td>
        </tr>
    </table>

    <!-- ISI SURAT -->
    <div class="content">
        <p>Yth. '. nl2br(htmlspecialchars($kepada)) .'</p>
        
        <p>Sehubungan dengan pelaksanaan kegiatan koordinasi, kami mengharapkan kehadiran Bapak/Ibu pada:</p>

        <table class="detail-table">
            <tr><td width="100">Hari/Tanggal</td><td width="15">:</td><td>'. formatHariTanggal($hari) .'</td></tr>
            <tr><td>Waktu</td><td>:</td><td>'. formatWaktu($waktu) .'</td></tr>
            <tr><td>Tempat</td><td>:</td><td>'. $tempat .'</td></tr>
            <tr><td>Agenda</td><td>:</td><td>'. $agenda .'</td></tr>
        </table>

        <p>Demikian kami sampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd">
        <p>Kepala BPS Kota Depok,</p>
        <div class="ttd-space"></div>
        <p><strong>Agus Marzuki Prihantoro</strong></p>
    </div>

</body>
</html>
';

// 4. Render PDF
$mpdf->WriteHTML($html);
$mpdf->Output('Undangan_BPS_Depok.pdf', \Mpdf\Output\Destination::INLINE);
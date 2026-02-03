<?php
$data = $data ?? [];

/* ================= UTIL ================= */
$bulan_indonesia = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

function formatTanggalIndo($date)
{
    global $bulan_indonesia;
    $ts = strtotime($date);
    return date('d', $ts) . ' ' . $bulan_indonesia[date('F', $ts)] . ' ' . date('Y', $ts);
}

/* ================= AGENDA ================= */
$data['agenda'] = '
<span class="checkmark">&#10003;</span> Pembukaan<br>
<span class="checkmark">&#10003;</span> Pembahasan dan Diskusi<br>
<span class="checkmark">&#10003;</span> Kesimpulan / Tindak Lanjut
';

/* ================= ISI RESUME (WAJIB MASUK SINI) ================= */
$isi = '
<p class="section-title"><strong>Pembukaan</strong></p>
' . ($data['pembukaan'] ?? '') . '

<p class="section-title"><strong>Pembahasan dan Diskusi</strong></p>
' . ($data['pembahasan'] ?? '') . '
';

/* ================= ISI KESIMPULAN (TERPISAH DARI RESUME) ================= */
$kesimpulan_label = '<strong>Kesimpulan / Tindak Lanjut</strong>';
$kesimpulan = $data['kesimpulan'] ?? '';
?>

<style>
    @page {
        margin: 25mm;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
    }

    /* CHECKMARK */
    .checkmark {
        font-family: Arial, sans-serif;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    td {
        border: 1px solid #000;
        padding: 6px 10px;
        vertical-align: top;
        line-height: 1.35;
        word-wrap: break-word;
    }

    /* VALUE */
    .label-a {
        width: 130px;
    }

    /* VALUE  */
    .label-b {
        width: 225px;
    }

    /* VALUE  */
    .label-c {
        width: 30px;
    }

    /* VALUE  */
    .label-d {
        width: 140px;
    }

    /* SPACER */
    .spacer td {
        border: none;
        height: 10mm;
    }

    /* PAGE BREAK */
    .page-break {
        page-break-before: always;
    }

    /* RESUME STYLE */
    .resume-table {
        page-break-inside: avoid;
    }

    .resume-table td {
        border: 1px solid #000;
    }

    /* PARAGRAF */
    td p {
        margin: 0 0 0 0;
        page-break-inside: avoid;
    }

    /* JUDUL SECTION */
    .section-title {
        margin-top: 12px;
        page-break-after: avoid;
        font-weight: bold;
    }

    .no-border {
        border: none !important;
        padding-top: 4px;
        padding-bottom: 6px;
    }

    /* IDENTITAS TABLE */
    .identitas-table td {
        text-align: left;
        vertical-align: middle;
    }
</style>

<!-- ================= TABEL IDENTITAS ================= -->
<table class="identitas-table">
    <tr>
        <td class="label-a" rowspan="2">Unit Kerja</td>
        <td class="label-b" rowspan="2"><?= $data['unit_kerja'] ?? '' ?></td>
        
        <td class="label-c">Tanggal</td>
        <td class="label-d"><?= formatTanggalIndo($data['tanggal'] ?? '') ?></td>
    </tr>
    <tr>
        <td class="label-c">Pukul</td>
        <td class="label-d"><?= $data['pukul_mulai'] ?? '' ?> – <?= $data['pukul_selesai'] ?? '' ?> WIB</td>
    </tr>
    <tr>
        <td class="label-a">Pimpinan Rapat</td>
        <td class="label-b"><?= $data['pimpinan'] ?? '' ?></td>
        <td class="label-c">Tempat</td>
        <td class="label-d"><?= $data['tempat'] ?? '' ?></td>
    </tr>
    <tr>
        <td>Topik</td>
        <td colspan="3"><?= $data['topik'] ?? '' ?></td>
    </tr>
    <tr>
        <td>Lampiran</td>
        <td colspan="3">1. Dokumentasi<br>2. Daftar Hadir</td>
    </tr>
    <tr class="spacer">
        <td colspan="4"></td>
    </tr>
</table>

<?php
/* ================= PEMECAHAN HALAMAN ================= */
$limitFirst  = 900;
$limitNext   = 2500;

$pages = [];
$current = '';
$currentLimit = $limitFirst;

/* pecah per paragraf */
$paragraphs = preg_split('/<\/p>/i', $isi);

foreach ($paragraphs as $p) {
    $p = trim($p);
    if ($p === '') continue;

    $p .= '</p>';
    $length = mb_strlen(strip_tags($current . $p));

    if ($length > $currentLimit) {
        $pages[] = $current;
        $current = $p;
        $currentLimit = $limitNext;
    } else {
        $current .= $p;
    }
}

if (trim(strip_tags($current)) !== '') {
    $pages[] = $current;
}
?>

<!-- ================= PESERTA & AGENDA ================= -->
<table>
    <tr>
        <td class="label" colspan="4">Peserta :</td>
    </tr>
    <tr>
        <td colspan="4"><?= $data['peserta'] ?? 'Sebagaimana Terlampir' ?></td>
    </tr>

    <tr>
        <td class="label" colspan="4">Agenda :</td>
    </tr>
    <tr>
        <td colspan="4"><?= $data['agenda'] ?></td>
    </tr>

    <tr>
        <td colspan="4"><em>Resume:</em></td>
    </tr>
</table>

<!-- ================= HALAMAN RESUME ================= -->
<?php foreach ($pages as $i => $page): ?>
    <?php if ($i > 0): ?>
        <div class="page-break"></div>
    <?php endif; ?>

    <table class="resume-table">
        <tr>
            <td><?= $page ?></td>
        </tr>
    </table>
<?php endforeach; ?>

<!-- ================= KESIMPULAN / TINDAK LANJUT (TERPISAH) ================= -->
<table class="resume-table">
    <tr class="spacer">
        <td></td>
    </tr>
    <tr>
        <td><?= $kesimpulan_label ?></td>
    </tr>
    <tr>
        <td><?= $kesimpulan ?></td>
    </tr>
</table>

<!-- ================= TANDA TANGAN ================= -->
<?php
// TTD Data
$p_tempat = $data['p_tempat'] ?? 'Depok';
$p_tanggal = $data['p_tanggal'] ?? date('Y-m-d');
$p_notulis = $data['p_notulis'] ?? 'Nurine Kristy';
?>
<table style="border: none; margin-top: 20px; page-break-inside: avoid; font-size: 12pt;">
    <tr style="border: none;">
        <td style="border: none; width: 60%;"></td>
        <td style="border: none; width: 40%; text-align: center; vertical-align: top;">
            <p style="margin-bottom: 20px;"><?= $p_tempat ?>, <?= formatTanggalIndo($p_tanggal) ?></p>
            <p style="margin-top: 20px;">Notulis</p>
            
            <!-- Space Tanda Tangan Manual -->
            <br><br><br>

            <p style="font-weight: bold; margin-top: 20px;"><?= $p_notulis ?></p>
        </td>
    </tr>
</table>
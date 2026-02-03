<?php
session_start();

$data = $_SESSION['notulensi'] ?? [];

if (empty($data)) {
    echo "<p>Data notulensi belum tersedia. Silakan simpan notulensi terlebih dahulu.</p>";
    exit;
}

// helper
function e($v) {
    return nl2br(htmlspecialchars($v ?? ''));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Preview Notulensi</title>

    <!-- CSS khusus preview -->
    <link rel="stylesheet" href="assets/css/notulensi-preview.css">
</head>
<body>

<h2 style="text-align:center;">NOTULENSI RAPAT</h2>

<div class="card">
    <div class="card-body paper">

        <table>
            <tr>
                <td class="label-cell" rowspan="2">Unit kerja</td>
                <td rowspan="2"><?= e($data['unit_kerja']) ?></td>
                <td width="10%">Tanggal</td>
                <td><?= e($data['tanggal_fmt'] ?? $data['tanggal']) ?></td>
            </tr>
            <tr>
                <td>Pukul</td>
                <td><?= e($data['pukul_mulai']) ?> – <?= e($data['pukul_selesai']) ?> WIB</td>
            </tr>
            <tr>
                <td>Pimpinan Rapat</td>
                <td><?= e($data['pimpinan']) ?></td>
                <td>Tempat</td>
                <td><?= e($data['tempat']) ?></td>
            </tr>
            <tr>
                <td>Topik</td>
                <td colspan="3"><?= e($data['topik']) ?></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td colspan="3"><?= e($data['lampiran']) ?></td>
            </tr>
        </table>

        <div class="spacer"></div>

        <table>
            <tr>
                <td class="label-cell">Peserta :</td>
            </tr>
            <tr>
                <td><?= e($data['peserta']) ?></td>
            </tr>

            <tr>
                <td class="label-cell">Agenda :</td>
            </tr>
            <tr>
                <td><?= e($data['agenda']) ?></td>
            </tr>

            <tr>
                <td><em>Resume:</em></td>
            </tr>
        </table>

        <div class="preview-box">
            <strong>Pembukaan</strong>
            <div><?= $data['pembukaan'] ?></div>

            <br>
            <strong>Pembahasan dan Diskusi</strong>
            <div><?= $data['pembahasan'] ?></div>
        </div>

        <div style="margin-top:20px; text-align:right;">
            <a href="index.php?page=generate_notulensi" target="_blank">
                <button>Cetak PDF</button>
            </a>
        </div>

    </div>
</div>

</body>
</html>

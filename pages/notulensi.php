<?php
session_start();
?>

<?php
// ========== UTIL ==========
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

// ========== DATA ==========
$unit_kerja    = $_POST['unit_kerja'] ?? 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok';
$tanggal_raw   = $_POST['tanggal'] ?? date('Y-m-d');
$pimpinan      = $_POST['pimpinan'] ?? 'Satriana Yasmuarto, S.Si, MM';
$pukul_mulai   = $_POST['pukul_mulai'] ?? '09:00';
$pukul_selesai = $_POST['pukul_selesai'] ?? '11:00';
$topik         = $_POST['topik'] ?? ('Rapat Tim Kegiatan Pembinaan Desa Cantik ' . date('Y'));
$tempat        = $_POST['tempat'] ?? 'Ruang Rapat BPS Kota Depok';
$lampiran      = $_POST['lampiran'] ?? "1. Dokumentasi\n2. Daftar Hadir";
$peserta       = $_POST['peserta'] ?? "Sebagaimana Terlampir";
$agenda        = $_POST['agenda'] ?? "✓ Pembukaan\n✓ Pembahasan dan Diskusi\n✓ Kesimpulan dan Tindak Lanjut";
$pembukaan     = $_POST['pembukaan'] ?? "<p>Silakan isi pembukaan rapat di sini.</p>";
$pembahasan    = $_POST['pembahasan'] ?? "<p>Silakan isi pembahasan dan diskusi di sini.</p>";
$kesimpulan    = $_POST['kesimpulan'] ?? '';

// TTD Notulis
$p_tempat  = $_POST['p_tempat'] ?? 'Depok';
$p_tanggal = $_POST['p_tanggal'] ?? date('Y-m-d');
$p_notulis = $_POST['p_notulis'] ?? 'Nurine Kristy';

// ========== PRINT MODE ==========
$is_print = (isset($_GET['print']) && $_GET['print'] === '1');

if ($is_print) {
?>
    <!doctype html>
    <html lang="id">

    <head>
        <meta charset="utf-8">
        <title>Print Notulensi</title>
        <style>
            /* 1. Reset Dasar */
            html,
            body {
                margin: 0;
                padding: 0;
                background: #fff;
                font-family: Arial, sans-serif;
                color: #000;
            }

            /* 2. Pengaturan Halaman Fisik */
            @page {
                size: letter;
                /* Memberikan margin fisik pada kertas (Atas, Kanan, Bawah, Kiri) */
                margin: 2cm;
            }

            /* 3. Kontainer Konten */
            .print-area {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            /* 4. Styling Tabel (Sama dengan format PDF Anda) */
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
                table-layout: fixed;
                /* Mencegah tabel meluber keluar margin */
            }

            td {
                border: 1px solid #000;
                padding: 6px 10px;
                font-size: 11pt;
                vertical-align: top;
                line-height: 1.35;
                word-wrap: break-word;
            }

            .label {
                width: 130px;
            }

            /* 5. Resume Box */
            .resume {
                border: 1px solid #000;
                border-top: none;
                padding: 15px;
                font-size: 11pt;
                line-height: 1.35;
                text-align: justify;
            }

            .resume p,
            .resume div {
                margin: 0;
            }

            .spacer {
                height: 5mm;
                /* Jarak antar tabel */
            }

            /* 6. Kontrol Pemotongan Halaman (PENTING) */
            @media print {

                /* Mencegah satu baris tabel terpotong di antara dua halaman */
                tr {
                    page-break-inside: avoid;
                }

                /* Menghilangkan sisa-sisa padding/margin browser */
                body {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }


            ul.checklist {
                list-style: none;
                padding-left: 0;
            }

            ul.checklist li {
                margin-bottom: 6px;
            }

            ul.checklist li::before {
                content: "✓ ";
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="print-area">
            <table>
                <tr>
                    <td class="label" rowspan="2">Unit kerja</td>
                    <td rowspan="2"><?= nl2br(htmlspecialchars($unit_kerja)) ?></td>
                    <td style="width:90px;">Tanggal</td>
                    <td><?= htmlspecialchars(formatTanggalIndo($tanggal_raw)) ?></td>
                </tr>
                <tr>
                    <td>Pukul</td>
                    <td><?= htmlspecialchars($pukul_mulai) ?> – <?= htmlspecialchars($pukul_selesai) ?> WIB</td>
                </tr>
                <tr>
                    <td>Pimpinan Rapat</td>
                    <td><?= nl2br(htmlspecialchars($pimpinan)) ?></td>
                    <td>Tempat</td>
                    <td><?= nl2br(htmlspecialchars($tempat)) ?></td>
                </tr>
                <tr>
                    <td>Topik</td>
                    <td colspan="3"><?= nl2br(htmlspecialchars($topik)) ?></td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td colspan="3"><?= nl2br(htmlspecialchars($lampiran)) ?></td>
                </tr>
            </table>

            <div class="spacer"></div>

            <table>
                <tr>
                    <td class="label">Peserta :</td>
                </tr>
                <tr>
                    <td><?= nl2br(htmlspecialchars($peserta)) ?></td>
                </tr>
                <tr>
                    <td class="label">Agenda :</td>
                </tr>
                <tr>
                    <td><?= nl2br(htmlspecialchars($agenda)) ?></td>
                </tr>
                <tr>
                    <td><em>Resume:</em></td>
                </tr>
            </table>

            <div class="resume">
                <div style="margin-bottom: 10px;">
                    <strong>Pembukaan</strong>
                    <div><?= $pembukaan ?></div>
                </div>
                <div>
                    <strong>Pembahasan dan Diskusi</strong>
                    <div><?= $pembahasan ?></div>
                </div>

                <div>
                    <strong>Kesimpulan / Tindak Lanjut</strong>
                    <div><?= $kesimpulan ?></div>
                </div>
            </div>
            
            <div class="spacer"></div>

            <!-- TANDA TANGAN -->
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="border: none; width: 70%;"></td>
                    <td style="border: none; text-align: center; vertical-align: top;">
                        <p style="margin-bottom: 5px;"><?= htmlspecialchars($p_tempat) ?>, <?= formatTanggalIndo($p_tanggal) ?></p>
                        <p style="margin-bottom: 5px;">Notulis</p>
                        
                        <br><br><br><br>

                        <p style="font-weight: bold; margin-top: 5px;"><?= htmlspecialchars($p_notulis) ?></p>
                    </td>
                </tr>
            </table>
        </div>
    </body>

    </html>
<?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notulensi - Desa Cantik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

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

        .page-wrap {
            max-width: 1200px;
            margin: 10px;
        }


        .title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: .2px;
        }

        .hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
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
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 14px rgba(0, 0, 0, .10);
            transition: .15s;
            font-size: 13px;
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
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
        }

        /* layout 2 panel */
        .grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 16px;
            align-items: start;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
            overflow: hidden;
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

        .card-body {
            padding: 14px;
        }

        /* table form */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 11pt;
            vertical-align: top;
            line-height: 1.35;
        }

        .label-cell {
            width: 130px;
        }

        input,
        textarea {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font: inherit;
            line-height: 1.35;
            padding: 0;
        }

        textarea {
            resize: none;
            min-height: 22px;
            overflow: hidden;
        }

        .spacer {
            height: 12px;
        }

        /* preview */
        .paper {
            background: #fff;
            padding: 14px;
        }

        .preview-box {
            border: 1px solid #000;
            padding: 10px;
            border-top: none;
            font-size: 11pt;
            line-height: 1.35;
        }

        .preview-box p,
        #paperPreview p {
            margin: 0;
        }

        .tox-tinymce {
            border: 1px dashed #cbd5e1 !important;
            border-radius: 10px !important;
        }

        .tox-toolbar {
            background: #f8fafc !important;
        }

        /* notif */
        .notification {
            position: fixed;
            top: 18px;
            left: 50%;
            transform: translateX(-50%) translateY(-12px);
            opacity: 0;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 800;
            box-shadow: 0 12px 24px rgba(0, 0, 0, .18);
            transition: .2s;
            z-index: 9999;
        }

        .notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* responsive */
        @media (max-width: 1100px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .actions {
                justify-content: flex-start;
            }
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

        /* print from page (ctrl+p) => hanya preview */
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
                background: #fff !important;
                padding: 0 !important;
            }

            .page-wrap {
                max-width: none;
                margin: 0;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .card-head {
                display: none;
            }

            .card-body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2>UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i> Undangan</a></li>
                <li><a href="index.php?page=notulensi" class="active"><i class="fas fa-file-alt"></i> Notulensi</a></li>
                <li><a href="index.php?page=absensi"><i class="fas fa-user-check"></i> Absensi</a></li>
                <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i> Arsip</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="notification" id="notification">Tersimpan ✅</div>

            <div class="page-wrap">
                <div class="actions">
                    <button class="btn save" type="button" onclick="saveNotulensi()">Simpan</button>
                    <a href="index.php?page=generate_notulensi" target="_blank" class="btn print">
                        Cetak PDF
                    </a>
                </div>


                <div class="grid">
                    <!-- FORM -->
                    <div class="card">
                        <div class="card-head">
                            <h3>Buat Notulensi</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="notulenForm">
                                <!-- DATA RAPAT (Collapsible) -->
                                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                                    <div style="padding: 12px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleDataRapat(this)">
                                        <span style="font-weight: bold; color: #475569;"><i class="fas fa-sliders-h"></i> Identitas Notulensi</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>

                                    <div id="data-rapat-content" style="display: none; padding: 15px; border-top: 1px dashed #cbd5e1; background: #fff; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                            <div>
                                                <label class="hint">Unit Kerja</label>
                                                <textarea name="unit_kerja" rows="1"><?= htmlspecialchars($unit_kerja) ?></textarea>
                                            </div>
                                            <div>
                                                <label class="hint">Tanggal Rapat</label>
                                                <input type="date" name="tanggal" value="<?= $tanggal_raw ?>">
                                            </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                            <div>
                                                <label class="hint">Waktu Mulai - Selesai (WIB)</label>
                                                <div style="display: flex; gap: 5px; align-items: center;">
                                                    <input type="text" name="pukul_mulai" value="<?= $pukul_mulai ?>" style="text-align: center;">
                                                    <span>-</span>
                                                    <input type="text" name="pukul_selesai" value="<?= $pukul_selesai ?>" style="text-align: center;">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="hint">Tempat</label>
                                                <textarea name="tempat" rows="1"><?= htmlspecialchars($tempat) ?></textarea>
                                            </div>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                            <label class="hint">Pimpinan Rapat</label>
                                            <textarea name="pimpinan" rows="1"><?= htmlspecialchars($pimpinan) ?></textarea>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                            <label class="hint">Topik Rapat</label>
                                            <textarea name="topik" rows="1"><?= htmlspecialchars($topik) ?></textarea>
                                        </div>

                                        <div style="margin-bottom: 15px;">
                                            <label class="hint">Lampiran</label>
                                            <textarea name="lampiran" rows="2"><?= htmlspecialchars($lampiran) ?></textarea>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                            <div>
                                                <label class="hint">Peserta</label>
                                                <textarea name="peserta" rows="1" readonly style="background: #f1f5f9; color: #64748b;"><?= htmlspecialchars($peserta) ?></textarea>
                                            </div>
                                            <div>
                                                <label class="hint">Agenda</label>
                                                <textarea name="agenda" rows="1" readonly style="background: #f1f5f9; color: #64748b;"><?= htmlspecialchars($agenda) ?></textarea>
                                            </div>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; padding-top:15px; border-top: 1px dashed #eee;">
                                            <div>
                                                <label class="hint">Tempat Pembuatan Notulensi</label>
                                                <input type="text" name="p_tempat" value="<?= htmlspecialchars($p_tempat) ?>">
                                            </div>
                                            <div>
                                                <label class="hint">Tanggal Pembuatan</label>
                                                <input type="date" name="p_tanggal" value="<?= $p_tanggal ?>">
                                            </div>
                                            <div style="grid-column: span 2;">
                                                <label class="hint">Nama Notulis</label>
                                                <input type="text" name="p_notulis" value="<?= htmlspecialchars($p_notulis) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <script>
                                    function toggleDataRapat(header) {
                                        const content = document.getElementById('data-rapat-content');
                                        const icon = header.querySelector('.fa-chevron-down');
                                        if (content.style.display === 'none') {
                                            content.style.display = 'block';
                                            icon.style.transform = 'rotate(180deg)';
                                        } else {
                                            content.style.display = 'none';
                                            icon.style.transform = 'rotate(0deg)';
                                        }
                                        icon.style.transition = 'transform 0.3s';
                                    }

                                    function toggleSection(header, contentId) {
                                        const content = document.getElementById(contentId);
                                        const icon = header.querySelector('.fa-chevron-down');
                                        if (content.style.display === 'none') {
                                            content.style.display = 'block';
                                            icon.style.transform = 'rotate(180deg)';
                                        } else {
                                            content.style.display = 'none';
                                            icon.style.transform = 'rotate(0deg)';
                                        }
                                        icon.style.transition = 'transform 0.3s';
                                    }
                                </script>

                                <!-- Pembukaan -->
                                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                                    <div style="padding: 12px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleSection(this, 'pembukaan-content')">
                                        <span style="font-weight: bold; color: #475569;">Pembukaan</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="pembukaan-content" style="display: none; padding: 15px; border-top: 1px dashed #cbd5e1; background: #fff; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                                        <textarea id="editor-pembukaan" name="pembukaan" class="editor"><?= htmlspecialchars($pembukaan ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Pembahasan -->
                                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                                    <div style="padding: 12px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleSection(this, 'pembahasan-content')">
                                        <span style="font-weight: bold; color: #475569;">Pembahasan dan Diskusi</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="pembahasan-content" style="display: none; padding: 15px; border-top: 1px dashed #cbd5e1; background: #fff; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                                        <textarea id="editor-pembahasan" name="pembahasan" class="editor"><?= htmlspecialchars($pembahasan ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Kesimpulan -->
                                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; margin-bottom: 20px;">
                                    <div style="padding: 12px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleSection(this, 'kesimpulan-content')">
                                        <span style="font-weight: bold; color: #475569;">Kesimpulan / Tindak Lanjut</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="kesimpulan-content" style="display: none; padding: 15px; border-top: 1px dashed #cbd5e1; background: #fff; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                                        <textarea id="editor-kesimpulan" name="kesimpulan" class="editor"><?= htmlspecialchars($kesimpulan ?? '') ?></textarea>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- PREVIEW -->
                    <div class="card">
                        <div class="card-body paper" id="paperPreview">
                            <table>
                                <tr>
                                    <td class="label-cell" rowspan="2" style="vertical-align:middle;">Unit kerja</td>
                                    <td rowspan="2" id="pv_unit"><?= nl2br(htmlspecialchars($unit_kerja)) ?></td>
                                    <td width="10%">Tanggal</td>
                                    <td id="pv_tgl"><?= htmlspecialchars(formatTanggalIndo($tanggal_raw)) ?></td>
                                </tr>
                                <tr>
                                    <td>Pukul</td>
                                    <td id="pv_pukul"><?= htmlspecialchars($pukul_mulai) ?> – <?= htmlspecialchars($pukul_selesai) ?> WIB</td>
                                </tr>
                                <tr>
                                    <td>Pimpinan Rapat</td>
                                    <td id="pv_pimpinan"><?= nl2br(htmlspecialchars($pimpinan)) ?></td>
                                    <td>Tempat</td>
                                    <td id="pv_tempat"><?= nl2br(htmlspecialchars($tempat)) ?></td>
                                </tr>
                                <tr>
                                    <td>Topik</td>
                                    <td colspan="3" id="pv_topik"><?= nl2br(htmlspecialchars($topik)) ?></td>
                                </tr>
                                <tr>
                                    <td>Lampiran</td>
                                    <td colspan="3" id="pv_lampiran"><?= nl2br(htmlspecialchars($lampiran)) ?></td>
                                </tr>
                            </table>

                            <div class="spacer"></div>

                            <table>
                                <tr>
                                    <td class="label-cell">Peserta :</td>
                                </tr>
                                <tr>
                                    <td id="pv_peserta"><?= nl2br(htmlspecialchars($peserta)) ?></td>
                                </tr>

                                <tr>
                                    <td class="label-cell">Agenda :</td>
                                </tr>
                                <tr>
                                    <td id="pv_agenda"><?= nl2br(htmlspecialchars($agenda)) ?></td>
                                </tr>

                                <tr>
                                    <td><em>Resume:</em></td>
                                </tr>
                            </table>

                            <div class="preview-box">
                                <strong>Pembukaan</strong>
                                <div id="pv_pembukaan"><?= $pembukaan ?></div>
                                <br>
                                <strong>Pembahasan dan Diskusi</strong>
                                <div id="pv_pembahasan"><?= $pembahasan ?></div>
                                <strong>Kesimpulan / Tindak Lanjut</strong>
                                <div id="pv_kesimpulan"></div>
                            </div>
                            
                            <!-- SIGNATURE PREVIEW -->
                            <div style="margin-top: 40px; display: flex; justify-content: flex-end;">
                                <div style="text-align: center; width: 300px;">
                                    <p id="pv_tempat_tgl" style="margin-bottom: 5px;"><?= htmlspecialchars($p_tempat) ?>, <?= formatTanggalIndo($p_tanggal) ?></p>
                                    <p style="margin-bottom: 5px;">Notulis</p>
                                    
                                    <br><br><br><br>

                                    <p id="pv_notulis" style="font-weight: bold; margin-top: 5px;"><?= htmlspecialchars($p_notulis) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <script>
                /* =========================
   HELPER
========================= */
                function escapeHtml(s) {
                    return (s || '').replace(/[&<>"']/g, c => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    } [c]));
                }

                function nl2br(s) {
                    return escapeHtml(s).replace(/\n/g, '<br>');
                }

                function autoResize(t) {
                    t.style.height = 'auto';
                    t.style.height = t.scrollHeight + 'px';
                }

                /* =========================
                   UPDATE PREVIEW
                ========================= */
                function updatePreview() {
                    const get = n => document.querySelector(`[name="${n}"]`)?.value || '';

                    document.getElementById('pv_unit').innerHTML = nl2br(get('unit_kerja'));
                    document.getElementById('pv_pimpinan').innerHTML = nl2br(get('pimpinan'));
                    document.getElementById('pv_tempat').innerHTML = nl2br(get('tempat'));
                    document.getElementById('pv_topik').innerHTML = nl2br(get('topik'));
                    document.getElementById('pv_lampiran').innerHTML = nl2br(get('lampiran'));
                    document.getElementById('pv_peserta').innerHTML = nl2br(get('peserta'));
                    document.getElementById('pv_agenda').innerHTML = nl2br(get('agenda'));

                    const mul = get('pukul_mulai');
                    const sel = get('pukul_selesai');
                    document.getElementById('pv_pukul').textContent =
                        mul && sel ? `${mul} – ${sel} WIB` : '';

                    const tgl = get('tanggal');
                    if (tgl) {
                        const d = new Date(tgl);
                        const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        document.getElementById('pv_tgl').textContent =
                            `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
                    }

                    if (tinymce.get('editor-pembukaan')) {
                        document.getElementById('pv_pembukaan').innerHTML =
                            tinymce.get('editor-pembukaan').getContent();
                    }

                    if (tinymce.get('editor-pembahasan')) {
                        document.getElementById('pv_pembahasan').innerHTML =
                            tinymce.get('editor-pembahasan').getContent();
                    }

                    if (tinymce.get('editor-kesimpulan')) {
                        document.getElementById('pv_kesimpulan').innerHTML =
                            tinymce.get('editor-kesimpulan').getContent();
                    }

                    // UPDATE SIGNATURE PREVIEW
                    const p_tempat = get('p_tempat');
                    const p_tanggal = get('p_tanggal');
                    const p_notulis = get('p_notulis');
                    
                    let dateStr = '';
                    if (p_tanggal) {
                         const d = new Date(p_tanggal);
                         const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                         dateStr = `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
                    }

                    const signatureText = (p_tempat && dateStr) ? `${p_tempat}, ${dateStr}` : 
                                          (p_tempat ? p_tempat : dateStr);
                    
                    const elTempatTgl = document.getElementById('pv_tempat_tgl');
                    if(elTempatTgl) elTempatTgl.textContent = signatureText;

                    const elNotulis = document.getElementById('pv_notulis');
                    if(elNotulis) elNotulis.textContent = p_notulis;
                }

                /* =========================
                   SIMPAN KE SESSION (AJAX)
                ========================= */
                function saveNotulensi() {
                    tinymce.triggerSave(); // WAJIB

                    const form = document.getElementById('notulenForm');
                    const data = new FormData(form);

                    fetch('pages/save_notulensi.php', {
                            method: 'POST',
                            body: data
                        })
                        .then(r => r.text())
                        .then(res => {
                            if (res.trim() === 'OK') showNotif();
                        });
                }

                /* =========================
                   PREVIEW
                ========================= */
                function previewNotulensi() {
                    saveNotulensi();
                    setTimeout(() => {
                        window.location.href = 'index.php?page=preview_notulensi';
                    }, 300);
                }

                /* =========================
                   LOCAL STORAGE
                ========================= */
                function loadFromLocalStorage() {
                    const saved = localStorage.getItem('notulensiData');
                    if (!saved) return;

                    const d = JSON.parse(saved);

                    // Check if saved data matches old defaults
                    const oldPembukaan = "<p>Rapat dimulai dengan sambutan pembukaan dari Bapak Satriana Yasmuarto..</p>";
                    const oldPembahasan = "<p><strong>a. Rancangan Kegiatan dan Jadwal Program Desa Cantik</strong></p><p>..</p>";
                    if (d.pembukaan === oldPembukaan && d.pembahasan === oldPembahasan) {
                        // Remove old data and set new defaults
                        localStorage.removeItem('notulensiData');
                        setTimeout(() => {
                            tinymce.get('editor-pembukaan')?.setContent("<p>Silakan isi pembukaan rapat di sini.</p>");
                            tinymce.get('editor-pembahasan')?.setContent("<p>Silakan isi pembahasan dan diskusi di sini.</p>");
                            updatePreview();
                        }, 300);
                        return;
                    }

                    Object.keys(d).forEach(k => {
                        const el = document.querySelector(`[name="${k}"]`);
                        if (el) el.value = d[k];
                    });

                    setTimeout(() => {
                        tinymce.get('editor-pembukaan')?.setContent(d.pembukaan || '');
                        tinymce.get('editor-pembahasan')?.setContent(d.pembahasan || '');
                        tinymce.get('editor-kesimpulan')?.setContent(d.kesimpulan || '');
                        updatePreview();
                    }, 300);
                }

                /* =========================
                   NOTIF
                ========================= */
                function showNotif() {
                    const n = document.getElementById('notification');
                    if (!n) return;
                    n.classList.add('show');
                    setTimeout(() => n.classList.remove('show'), 1200);
                }

                /* =========================
                   TINYMCE (FIXED)
                ========================= */
                const tinyConfig = {
                    license_key: 'gpl', // ⬅️ INI YANG HILANG
                    menubar: false,
                    branding: false,
                    statusbar: false,
                    plugins: 'lists link table',
                    toolbar: 'bold italic underline | bullist numlist',
                    setup: ed => {
                        ed.on('change keyup', updatePreview);
                    }
                };

                tinymce.init({
                    selector: '#editor-pembukaan',
                    height: 300,
                    ...tinyConfig
                });

                tinymce.init({
                    selector: '#editor-pembahasan',
                    height: 1000,
                    ...tinyConfig
                });

                tinymce.init({
                    selector: '#editor-kesimpulan',
                    height: 250,
                    license_key: 'gpl',
                    menubar: false,
                    branding: false,
                    statusbar: false,
                    plugins: 'lists',
                    toolbar: false,
                    content_style: `
        ul.checklist {
            list-style: none;
            padding-left: 0;
        }
        ul.checklist li::before {
            content: "✓ ";
            font-weight: bold;
        }
    `,
                    setup: editor => {
                        editor.on('keydown', e => {
                            if (e.key === 'Enter') {
                                const content = editor.getContent();
                                if (!content.includes('<ul')) {
                                    editor.setContent('<ul class="checklist"><li></li></ul>');
                                    e.preventDefault();
                                }
                            }
                        });

                        editor.on('change keyup', updatePreview);
                    }
                });

                /* =========================
                   EVENT
                ========================= */
                window.addEventListener('load', () => {
                    loadFromLocalStorage();

                    // textarea NON TinyMCE saja
                    document.querySelectorAll('textarea:not(#editor-pembukaan):not(#editor-pembahasan)')
                        .forEach(t => {
                            autoResize(t);
                            t.addEventListener('input', () => {
                                autoResize(t);
                                updatePreview();
                            });
                        });

                    document.querySelectorAll('input').forEach(i => {
                        i.addEventListener('input', updatePreview);
                    });

                    updatePreview();
                });
            </script>
        </div>
    </div>
</body>

</html>
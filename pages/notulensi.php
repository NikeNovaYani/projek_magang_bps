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

// ========== LOAD FROM ARCHIVE ==========
$is_loaded_from_archive = false;
if (isset($_GET['load'])) {
    $folder = preg_replace('/[^A-Za-z0-9\-_]/', '_', $_GET['load']);
    $jsonPath = 'arsip/' . $folder . '/notulensi.json';

    if (file_exists($jsonPath)) {
        $loaded = json_decode(file_get_contents($jsonPath), true);
        if ($loaded) {
            $unit_kerja    = $loaded['unit_kerja'] ?? $unit_kerja;
            $tanggal_raw   = $loaded['tanggal'] ?? $tanggal_raw;
            $pimpinan      = $loaded['pimpinan'] ?? $pimpinan;
            $pukul_mulai   = $loaded['pukul_mulai'] ?? $pukul_mulai;
            $pukul_selesai = $loaded['pukul_selesai'] ?? $pukul_selesai;
            $topik         = $loaded['topik'] ?? $topik;
            $tempat        = $loaded['tempat'] ?? $tempat;
            $lampiran      = $loaded['lampiran'] ?? $lampiran;
            $peserta       = $loaded['peserta'] ?? $peserta;
            $agenda        = $loaded['agenda'] ?? $agenda;
            $pembukaan     = $loaded['pembukaan'] ?? $pembukaan;
            $pembahasan    = $loaded['pembahasan'] ?? $pembahasan;
            $pembahasan    = $loaded['pembahasan'] ?? $pembahasan;
            $kesimpulan    = $loaded['kesimpulan'] ?? $kesimpulan;
            // Load Images Arrays
            $dokumentasi_files = $loaded['dokumentasi'] ?? [];
            $absensi_files     = $loaded['absensi'] ?? [];
            $is_loaded_from_archive = true;
        }
    }
}

// ========== DATA DEFAULT (Using fallback if not loaded) ==========
if (!isset($unit_kerja)) { // Only set defaults if variables not already set
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
    $pembukaan     = $_POST['pembukaan'] ?? "Silakan isi pembukaan rapat di sini.";
    $pembahasan    = $_POST['pembahasan'] ?? "Silakan isi pembahasan dan diskusi di sini.";
    $kesimpulan    = $_POST['kesimpulan'] ?? '';
}

// TTD Notulis (Load or Default)
$p_tempat  = $_POST['p_tempat'] ?? ($p_tempat ?? 'Depok');
$p_tanggal = $_POST['p_tanggal'] ?? ($p_tanggal ?? date('Y-m-d'));
$p_notulis = $_POST['p_notulis'] ?? ($p_notulis ?? 'Nurine Kristy');

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
                <div style="margin-bottom: 50px;">
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
    <script src="/projek_magang/tinymce/js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

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
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--line);
            overflow: hidden;
            margin-bottom: 30px;
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

        .first-line-indent {
            text-indent: 40px;
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
            width: 100%;
        }

        /* Form Sections */
        .form-section {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .form-section-header {
            padding: 12px 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-section-content {
            display: none;
            padding: 15px;
            border-top: 1px dashed #cbd5e1;
            background: #fff;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
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

        /* PREVIEW A4 STYLE */
        .paper {
            background: #fff;
            padding: 0;
        }

        #paperPreview {
            width: 100%;
            aspect-ratio: 210/297;
            position: relative;
            overflow: hidden;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .page-view,
        .doc-page {
            width: 100%;
            height: 100%;
            padding: 15mm;
            box-sizing: border-box;
            background: white;
            position: absolute;
            top: 0;
            left: 0;
            overflow-y: hidden;
        }

        .preview-box {
            border: 1px solid #000;
            padding: 10px;
            border-top: none;
            font-size: 11pt;
            line-height: 1.35;
            margin-bottom: 20px;
        }

        .preview-box p,
        #paperPreview p {
            margin: 10;
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
                    <button class="btn print" type="button" onclick="cetakPDF(this)">Cetak PDF</button>
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
                                    function toggleSection(header, contentId) {
                                        const content = document.getElementById(contentId);
                                        const icon = header.querySelector('.fa-chevron-down');
                                        // Check computed style because class sets display:none
                                        const isHidden = window.getComputedStyle(content).display === 'none';

                                        if (isHidden) {
                                            content.style.display = 'block';
                                            icon.style.transform = 'rotate(180deg)';
                                        } else {
                                            content.style.display = 'none';
                                            icon.style.transform = 'rotate(0deg)';
                                        }
                                        icon.style.transition = 'transform 0.3s';
                                    }

                                    function toggleDataRapat(header) {
                                        toggleSection(header, 'data-rapat-content');
                                    }
                                </script>

                                <!-- Pembukaan -->
                                <div class="form-section">
                                    <div class="form-section-header" onclick="toggleSection(this, 'pembukaan-content')">
                                        <span style="font-weight: bold; color: #475569;">Pembukaan</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="pembukaan-content" class="form-section-content">
                                        <textarea id="editor-pembukaan" name="pembukaan" class="editor"><?= htmlspecialchars($pembukaan ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Pembahasan -->
                                <div class="form-section">
                                    <div class="form-section-header" onclick="toggleSection(this, 'pembahasan-content')">
                                        <span style="font-weight: bold; color: #475569;">Pembahasan dan Diskusi</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="pembahasan-content" class="form-section-content">
                                        <textarea id="editor-pembahasan" name="pembahasan" class="editor"><?= htmlspecialchars($pembahasan ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Kesimpulan -->
                                <div class="form-section">
                                    <div class="form-section-header" onclick="toggleSection(this, 'kesimpulan-content')">
                                        <span style="font-weight: bold; color: #475569;">Kesimpulan / Tindak Lanjut</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="kesimpulan-content" class="form-section-content">
                                        <textarea id="editor-kesimpulan" name="kesimpulan" class="editor"><?= htmlspecialchars($kesimpulan ?? '') ?></textarea>
                                    </div>
                                </div>

                                <!-- Dokumentasi -->
                                <div class="form-section">
                                    <div class="form-section-header" onclick="toggleSection(this, 'dokumentasi-content')">
                                        <span style="font-weight: bold; color: #475569;">Dokumentasi (Foto)</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="dokumentasi-content" class="form-section-content">
                                        <label class="hint" style="display:block; margin-bottom:8px;">Unggah Foto (Max 4)</label>

                                        <!-- Custom File Input -->
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <label for="inputDokumentasi" style="cursor: pointer; background: #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;">
                                                <i class="fas fa-camera"></i> Pilih Foto
                                            </label>
                                            <span id="fileCount" class="hint">Belum ada file dipilih</span>
                                        </div>
                                        <input type="file" name="dokumentasi[]" id="inputDokumentasi" multiple accept="image/png, image/jpeg, image/jpg" onchange="previewImages()" style="display: none;">

                                        <p class="hint" style="margin-top: 8px;">Format: JPG, PNG. Disarankan rasio landscape. Maksimal 4 foto.</p>
                                    </div>
                                </div>

                                <!-- absensi -->
                                <div class="form-section">
                                    <div class="form-section-header" onclick="toggleSection(this, 'absensi-content')">
                                        <span style="font-weight: bold; color: #475569;">Absensi</span>
                                        <i class="fas fa-chevron-down" style="color: #475569;"></i>
                                    </div>
                                    <div id="absensi-content" class="form-section-content">
                                        <label class="hint" style="display:block; margin-bottom:15px; font-weight: bold;">Unggah Bukti Absensi</label>

                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <!-- Left: Link -->
                                            <a href="https://daftarhadir.web.bps.go.id/#/login" target="_blank" style="text-decoration: none; color: #3b82f6; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 5px; padding: 6px 10px; background: #eff6ff; border-radius: 6px; border: 1px solid #bfdbfe;">
                                                <i class="fas fa-external-link-alt"></i> Link Daftar Hadir
                                            </a>

                                            <!-- Right: Upload Button -->
                                            <div style="display: flex; gap: 10px; align-items: center;">
                                                <label for="inputAbsensi" style="cursor: pointer; background: #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;">
                                                    <i class="fas fa-camera"></i> Pilih Foto
                                                </label>
                                            </div>
                                        </div>

                                        <div style="text-align: right; margin-top: 5px;">
                                            <span id="fileCountAbsensi" class="hint">Belum ada file dipilih</span>
                                        </div>

                                        <input type="file" name="absensi[]" id="inputAbsensi" multiple accept="image/png, image/jpeg, image/jpg" onchange="previewImages()" style="display: none;">

                                        <p class="hint" style="margin-top: 8px; text-align: right;">Format: JPG, PNG. Disarankan rasio landscape. Maksimal 2 foto.</p>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- PREVIEW -->
                    <div class="card" style="position: sticky; top: 20px;">
                        <!-- Navigation Controls -->
                        <div style="background: #e2e8f0; padding: 8px 12px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #cbd5e1;">
                            <span style="font-weight: bold; font-size: 13px; color: #475569;">Preview</span>
                            <div style="display: flex; gap: 5px; align-items: center;" id="navControls">
                                <!-- Dynamic Buttons injected here -->
                                <button type="button" class="nav-btn active" onclick="switchPage(1)">1</button>
                                <button type="button" class="nav-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>

                        <div class="card-body paper" id="paperPreview" style="min-height: 800px; position: relative; overflow: hidden;">

                            <!-- PAGE 1: TEXT -->
                            <div class="page-view" id="page1" style="width: 100%; transition: transform 0.3s ease;">
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

                            <!-- Dynamic Pages (2, 3...) will be injected here -->

                        </div>
                    </div>

                </div>
            </div>

            <!-- Styles for Navigation Buttons -->
            <style>
                .nav-btn {
                    border: none;
                    background: transparent;
                    width: 28px;
                    height: 28px;
                    border-radius: 50%;
                    cursor: pointer;
                    font-weight: bold;
                    color: #64748b;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .nav-btn.active {
                    background: #fff;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                    color: #0f172a;
                }

                .nav-btn:hover:not(.active) {
                    background: rgba(255, 255, 255, 0.5);
                }

                .doc-page {
                    position: absolute;
                    top: 0;
                    left: 100%;
                    /* Initially hidden to the right */
                    width: 50%;
                    height: auto;
                    background: #fff;
                    transition: left 0.3s ease;
                    padding: 14px;
                    overflow-y: auto;
                }

                /* Auto Height Logic */
                .paper.auto-height {
                    aspect-ratio: unset !important;
                    height: auto !important;
                    overflow: visible !important;
                    min-height: 297mm;
                    /* Ensure at least A4 height */
                }

                .page-view.relative-flow {
                    position: relative !important;
                    height: auto !important;
                    left: 0 !important;
                    transform: none !important;
                }
            </style>

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
                   NAVIGATION PREVIEW
                ========================= */
                let currentPage = 1;
                let totalPages = 1;
                const archiveFolder = '<?= $folder ?? '' ?>';

                function updateNavUI() {
                    const container = document.getElementById('navControls');
                    let html = '';

                    for (let i = 1; i <= totalPages; i++) {
                        const activeClass = (i === currentPage) ? 'active' : '';
                        html += `<button type="button" class="nav-btn ${activeClass}" onclick="switchPage(${i})">${i}</button>`;
                    }

                    html += `<button type="button" class="nav-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>`;
                    container.innerHTML = html;
                }

                function switchPage(page) {
                    if (page < 1 || page > totalPages) return;
                    currentPage = page;

                    const paper = document.getElementById('paperPreview');
                    const p1 = document.getElementById('page1');

                    // Handle Page 1 Logic
                    if (page === 1) {
                        paper.classList.add('auto-height');
                        p1.classList.add('relative-flow');
                        p1.style.transform = 'none'; // Clear transform
                    } else {
                        paper.classList.remove('auto-height');
                        p1.classList.remove('relative-flow');
                        p1.style.transform = 'translateX(-100%)';
                        p1.style.position = 'absolute'; // Ensure it goes back to absolute
                    }

                    // Handle Dynamic Pages (2, 3...)
                    const docPages = document.querySelectorAll('.doc-page');
                    docPages.forEach((p, idx) => {
                        const pageNum = idx + 2;
                        if (pageNum === page) {
                            p.style.left = '0';
                        } else if (pageNum < page) {
                            p.style.left = '-100%';
                        } else {
                            p.style.left = '100%';
                        }
                    });

                    updateNavUI();
                }

                function nextPage() {
                    let next = currentPage + 1;
                    if (next > totalPages) next = 1;
                    switchPage(next);
                }

                /* =========================
                   IMAGE PREVIEW
                ========================= */
                function previewImages() {
                    const inputDoc = document.getElementById('inputDokumentasi');
                    const inputAbs = document.getElementById('inputAbsensi');
                    const paper = document.getElementById('paperPreview');

                    // Update label counts
                    const countSpanDoc = document.getElementById('fileCount');
                    const countSpanAbs = document.getElementById('fileCountAbsensi'); // Use specific ID

                    if (inputDoc.files.length > 0) {
                        countSpanDoc.textContent = inputDoc.files.length + ' file dipilih';
                    } else {
                        countSpanDoc.textContent = 'Belum ada file dipilih';
                    }

                    if (countSpanAbs) {
                        if (inputAbs.files.length > 0) {
                            countSpanAbs.textContent = inputAbs.files.length + ' file dipilih';
                        } else {
                            countSpanAbs.textContent = 'Belum ada file dipilih';
                        }
                    }

                    // Remove existing doc pages (and absensi pages if any share the class)
                    const existing = document.querySelectorAll('.doc-page');
                    existing.forEach(e => e.remove());

                    // Start Page Counting
                    totalPages = 1;

                    // --- PROCESS DOKUMENTASI ---
                    // Combine separate arrays: Server Files + New Input Files
                    // Note: This simple logic appends new files after existing ones.
                    const newDocs = Array.from(inputDoc.files);
                    const allDocs = [...(window.serverDocFiles || []), ...newDocs].slice(0, 4);

                    if (allDocs.length > 0) {
                        const chunkSize = 2;
                        for (let i = 0; i < allDocs.length; i += chunkSize) {
                            const chunk = allDocs.slice(i, i + chunkSize);
                            totalPages++;
                            createPage(totalPages, 'DOKUMENTASI', chunk, false);
                        }
                    }

                    // --- PROCESS ABSENSI ---
                    const newAbs = Array.from(inputAbs.files);
                    const allAbs = [...(window.serverAbsFiles || []), ...newAbs].slice(0, 4);

                    if (allAbs.length > 0) {
                        // 1 Image per page for Absensi (usually A4 landscape)
                        const chunkSize = 1;
                        for (let i = 0; i < allAbs.length; i += chunkSize) {
                            const chunk = allAbs.slice(i, i + chunkSize);
                            totalPages++;
                            createPage(totalPages, 'DAFTAR HADIR', chunk, true);
                        }
                    }

                    // Update UI
                    updateNavUI();

                    // Update Counts Logic to include Server files
                    if (allDocs.length > 0) countSpanDoc.textContent = allDocs.length + ' file (Total)';
                    if (countSpanAbs && allAbs.length > 0) countSpanAbs.textContent = allAbs.length + ' file (Total)';

                    // Navigate only if we added pages and are currently on page 1
                    // or if the user just uploaded something, switch to the new page?
                    // Let's just update UI. JS state might need to stay on current page unless it's invalid.
                    if (currentPage > totalPages) {
                        switchPage(totalPages);
                    } else if (totalPages > 1 && currentPage === 1 && (inputDoc.files.length > 0 || inputAbs.files.length > 0)) {
                        // Only auto-switch if we are on page 1 and content was added
                        // But avoid annoying switching if user is editing text.
                        // For now, let's auto switch to 2 if pages created.
                        switchPage(2);
                    }
                }

                function createPage(pageNum, title, files, isLandscape) {
                    const paper = document.getElementById('paperPreview');
                    const div = document.createElement('div');
                    div.className = 'doc-page';
                    div.id = 'page' + pageNum;
                    div.style.left = '100%';

                    div.innerHTML = `<h3 style="text-align: center; margin-bottom: 20px; font-weight: bold; color: #0d47a1;">${title}</h3>`;

                    const imgContainer = document.createElement('div');
                    imgContainer.style.cssText = 'display: flex; flex-direction: column; gap: 40px; align-items: center; justify-content: flex-start; height: 90%;';

                    if (files && files.length > 0) {
                        files.forEach(file => {
                            let src = '';
                            if (file instanceof File) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    renderImg(e.target.result);
                                }
                                reader.readAsDataURL(file);
                            } else if (typeof file === 'string') {
                                src = file;
                                if (archiveFolder && !src.includes('uploads/') && !src.startsWith('data:')) {
                                    src = 'arsip/' + archiveFolder + '/' + src;
                                }
                                renderImg(src);
                            }

                            function renderImg(s) {
                                const img = document.createElement('img');
                                img.src = s;
                                if (isLandscape) {
                                    img.style.cssText = 'max-width: 100%; max-height: 90%; object-fit: contain;';
                                } else {
                                    img.style.cssText = 'max-width: 90%; max-height: 420px; object-fit: contain;';
                                }
                                imgContainer.appendChild(img);
                            }
                        });
                    }

                    div.appendChild(imgContainer);
                    paper.appendChild(div);
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
                    if (elTempatTgl) elTempatTgl.textContent = signatureText;

                    const elNotulis = document.getElementById('pv_notulis');
                    if (elNotulis) elNotulis.textContent = p_notulis;
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
                /* =========================
                   LOCAL STORAGE
                ========================= */
                function loadFromLocalStorage() {
                    // 1. Check legacy 'notulensiData' first
                    const legacy = localStorage.getItem('notulensiData');
                    if (legacy) {
                        const d = JSON.parse(legacy);
                        const oldPembukaan = "<p>Rapat dimulai dengan sambutan pembukaan dari Bapak Satriana Yasmuarto..</p>";
                        const oldPembahasan = "<p><strong>a. Rancangan Kegiatan dan Jadwal Program Desa Cantik</strong></p><p>..</p>";
                        if (d.pembukaan === oldPembukaan && d.pembahasan === oldPembahasan) {
                            localStorage.removeItem('notulensiData');
                            // Reset to new defaults if detected old junk
                            setTimeout(() => {
                                if (tinymce.get('editor-pembukaan')) tinymce.get('editor-pembukaan').setContent("Silakan isi pembukaan rapat di sini.");
                                if (tinymce.get('editor-pembahasan')) tinymce.get('editor-pembahasan').setContent("Silakan isi pembahasan dan diskusi di sini.");
                            }, 500);
                            return;
                        }
                    }

                    // 2. Load Real Draft 'notulensi_draft'
                    const saved = localStorage.getItem('notulensi_draft');
                    if (!saved) return;

                    try {
                        const d = JSON.parse(saved);
                        // Load Form Fields
                        Object.keys(d).forEach(k => {
                            if (k === 'dokumentasi' || k === 'absensi') return;
                            const el = document.querySelector(`[name="${k}"]`);
                            if (el) el.value = d[k];
                        });

                        setTimeout(() => {
                            if (tinymce.get('editor-pembukaan')) tinymce.get('editor-pembukaan').setContent(d.pembukaan || '');
                            if (tinymce.get('editor-pembahasan')) tinymce.get('editor-pembahasan').setContent(d.pembahasan || '');
                            if (tinymce.get('editor-kesimpulan')) tinymce.get('editor-kesimpulan').setContent(d.kesimpulan || '');
                            updatePreview();
                        }, 500);

                        updateNavUI();
                    } catch (e) {
                        console.error("Error loading draft", e);
                    }
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
                    license_key: 'gpl',
                    menubar: false,
                    branding: false,
                    statusbar: false,
                    plugins: 'lists link table advlist',
                    toolbar: 'bold italic underline | bullist numlist | indent_first',
                    content_style: `
                        body { font-family: Arial, sans-serif; font-size: 11pt; }
                        .first-line-indent { text-indent: 40px; }
                        p { margin-top: 0; margin-bottom: 10px; }
                    `,
                    setup: ed => {
                        ed.on('change keyup', updatePreview);

                        // Register Custom Format
                        ed.on('init', function() {
                            ed.formatter.register('myIndent', {
                                block: 'p',
                                classes: 'first-line-indent'
                            });
                        });

                        // Register Custom Button
                        ed.ui.registry.addButton('indent_first', {
                            icon: 'indent', // Using standard icon, or we could use 'paragraph'
                            tooltip: 'Indentasi Baris Pertama (Special)',
                            onAction: function(_) {
                                ed.formatter.toggle('myIndent');
                            }
                        });
                    }
                };

                if (typeof tinymce !== 'undefined') {
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
                }

                /* =========================
                   EVENT
                ========================= */
                window.addEventListener('load', () => {
                    const isLoadedFromArchive = <?= $is_loaded_from_archive ? 'true' : 'false' ?>;
                    if (!isLoadedFromArchive) {
                        // loadFromLocalStorage(); // Only load local storage if not editing archive
                    }

                    updateNavUI(); // Init Default

                    // Pass PHP Image Arrays to JS
                    const existingDocFiles = <?= json_encode($dokumentasi_files ?? []) ?>;
                    const existingAbsFiles = <?= json_encode($absensi_files ?? []) ?>;

                    // Store globally to use in updatePreview
                    window.serverDocFiles = existingDocFiles;
                    window.serverAbsFiles = existingAbsFiles;

                    // textarea NON TinyMCE saja
                    document.querySelectorAll('textarea:not(#editor-pembukaan):not(#editor-pembahasan):not(#editor-kesimpulan)')
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

                    // File inputs - Trigger Image Preview
                    const fDoc = document.getElementById('inputDokumentasi');
                    const fAbs = document.getElementById('inputAbsensi');
                    if (fDoc) fDoc.addEventListener('change', previewImages);
                    if (fAbs) fAbs.addEventListener('change', previewImages);

                    updatePreview();
                    // Render Initial Images (including Server Files)
                    previewImages();
                    switchPage(1);
                });
                /* =========================
                   CETAK PDF & ARCHIVE
                ========================= */
                function cetakPDF(btn) {
                    const form = document.getElementById('notulenForm');
                    const originalText = btn.innerText;

                    // Update UI
                    btn.innerText = 'Menyimpan...';
                    btn.disabled = true;

                    // Collect Data (Sync TinyMCE if available)
                    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
                    const data = new FormData(form);

                    // 1. Send to Archive
                    fetch('pages/save_notulensi.php?action=archive', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.text())
                        .then(result => {
                            const folderName = result.trim();

                            // Check valid folder name (simple check)
                            if (folderName && !folderName.includes('<br') && !folderName.includes('Error')) {
                                // 2. Trigger Silent Download
                                const iframe = document.createElement('iframe');
                                iframe.style.display = 'none';
                                iframe.src = 'pdf/generate_notulensi.php?download=true&archive_folder=' + encodeURIComponent(folderName);
                                document.body.appendChild(iframe);

                                setTimeout(() => {
                                    alert('Notulensi berhasil diarsipkan dan PDF diunduh!');
                                    btn.innerText = originalText;
                                    btn.disabled = false;
                                }, 1500);
                            } else {
                                alert('Gagal menyimpan arsip: ' + result);
                                console.error(result);
                                btn.innerText = originalText;
                                btn.disabled = false;
                            }
                        })
                        .catch(err => {
                            alert('Terjadi kesalahan koneksi.');
                            console.error(err);
                            btn.innerText = originalText;
                            btn.disabled = false;
                        });
                }

                function saveNotulensi() {
                    if (typeof tinymce !== 'undefined') tinymce.triggerSave();

                    const form = document.getElementById('notulenForm');
                    const data = new FormData(form);

                    fetch('pages/save_notulensi.php', {
                            method: 'POST',
                            body: data
                        })
                        .then(r => r.text())
                        .then(res => {
                            if (res.trim() === 'OK') showNotif();
                            else alert('Gagal simpan: ' + res);
                        })
                        .catch(e => alert('Error: ' + e));
                }
            </script>
        </div>
    </div>
</body>

</html>
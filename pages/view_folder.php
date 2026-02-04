<?php
// view_folder.php
if (!isset($_GET['folder'])) {
    header("Location: index.php?page=arsip");
    exit;
}

$folder = $_GET['folder'];
$folderName = str_replace('_', ' ', explode('_', $folder, 2)[1] ?? $folder);
$baseDir = 'arsip/' . $folder . '/';

if (!is_dir($baseDir)) {
    echo "<script>alert('Folder tidak ditemukan!'); window.location='index.php?page=arsip';</script>";
    exit;
}

// Fungsi Helper Scan File
function scan_files($dir) {
    if (!is_dir($dir)) return [];
    return array_diff(scandir($dir), ['.', '..']);
}

$files_undangan = scan_files($baseDir . 'undangan');
$files_notulensi = scan_files($baseDir . 'notulensi');
$files_absensi = scan_files($baseDir . 'absensi');

$json_undangan = file_exists($baseDir . 'undangan.json');
$json_notulensi = file_exists($baseDir . 'notulensi.json'); // Nanti kita buat ini

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Arsip - <?= htmlspecialchars($folderName) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         * { box-sizing: border-box; font-family: "Arial", sans-serif; }
         body { background: #f5f9ff; color: #333; margin: 0; padding: 20px; }
         .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
         
         .header { display: flex; align-items: center; border-bottom: 2px solid #e3f2fd; padding-bottom: 15px; margin-bottom: 25px; }
         .back-btn { text-decoration: none; color: #1976d2; font-weight: bold; display: flex; align-items: center; gap: 8px; margin-right: 20px; transition: 0.3s; }
         .back-btn:hover { transform: translateX(-5px); }
         .title { font-size: 24px; color: #0d47a1; margin: 0; flex: 1; }
         
         .section { margin-bottom: 30px; }
         .section h3 { color: #1565c0; font-size: 16px; border-left: 4px solid #1976d2; padding-left: 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between; }
         
         .file-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
         .file-card { background: #f8faff; border: 1px solid #e3f2fd; border-radius: 8px; padding: 15px; transition: 0.2s; position: relative; }
         .file-card:hover { border-color: #2196f3; box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1); }
         
         .fc-icon { font-size: 24px; color: #ff5722; margin-bottom: 10px; } /* Default PDF color */
         .fc-name { font-size: 14px; font-weight: bold; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
         .fc-size { font-size: 11px; color: #888; }
         
         .fc-actions { margin-top: 10px; display: flex; gap: 10px; }
         .btn-action { text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
         .btn-view { background: #e3f2fd; color: #1976d2; }
         .btn-dl { background: #e8f5e9; color: #2e7d32; }
         .btn-view:hover { background: #bbdefb; }
         .btn-dl:hover { background: #c8e6c9; }
         
         .action-btn { background: #fff3e0; color: #e65100; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; }
         .action-btn:hover { background: #ffe0b2; }

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="index.php?page=arsip" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
        <h1 class="title"><?= htmlspecialchars($folderName) ?></h1>
    </div>

    <!-- UNDANGAN SECTION -->
    <div class="section">
        <h3>
            <span><i class="fas fa-envelope"></i> Dokumen Undangan</span>
            <?php if($json_undangan): ?>
                <a href="index.php?page=undangan&load=<?= urlencode($folder) ?>" class="action-btn"><i class="fas fa-pen"></i> Edit Data</a>
            <?php endif; ?>
        </h3>
        <div class="file-list">
            <?php if (empty($files_undangan)): ?>
                <p style="color:#999; font-size:13px; font-style:italic;">Tidak ada file undangan.</p>
            <?php else: ?>
                <?php foreach($files_undangan as $file): 
                    $path = $baseDir . 'undangan/' . $file;
                    $size = round(filesize($path) / 1024, 1) . ' KB';
                ?>
                <div class="file-card">
                    <div class="fc-icon"><i class="fas fa-file-pdf"></i></div>
                    <div class="fc-name" title="<?= $file ?>"><?= $file ?></div>
                    <div class="fc-size"><?= $size ?></div>
                    <div class="fc-actions">
                        <a href="<?= $path ?>" target="_blank" class="btn-action btn-view">Lihat</a>
                        <a href="<?= $path ?>" download class="btn-action btn-dl">Unduh</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- NOTULENSI SECTION -->
    <div class="section">
        <h3>
            <span><i class="fas fa-file-alt"></i> Dokumen Notulensi</span>
            <?php if($json_notulensi): ?>
                <a href="index.php?page=notulensi&load=<?= urlencode($folder) ?>" class="action-btn"><i class="fas fa-pen"></i> Edit Data</a>
            <?php endif; ?>
        </h3>
        <div class="file-list">
            <?php if (empty($files_notulensi)): ?>
                <p style="color:#999; font-size:13px; font-style:italic;">Tidak ada file notulensi.</p>
            <?php else: ?>
                <?php foreach($files_notulensi as $file): 
                    $path = $baseDir . 'notulensi/' . $file;
                    $size = round(filesize($path) / 1024, 1) . ' KB';
                ?>
                <div class="file-card">
                    <div class="fc-icon" style="color:#4caf50;"><i class="fas fa-file-pdf"></i></div>
                    <div class="fc-name" title="<?= $file ?>"><?= $file ?></div>
                    <div class="fc-size"><?= $size ?></div>
                    <div class="fc-actions">
                        <a href="<?= $path ?>" target="_blank" class="btn-action btn-view">Lihat</a>
                        <a href="<?= $path ?>" download class="btn-action btn-dl">Unduh</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ABSENSI SECTION -->
    <div class="section">
        <h3><span><i class="fas fa-user-check"></i> Dokumen Absensi</span></h3>
        <div class="file-list">
            <?php if (empty($files_absensi)): ?>
                <p style="color:#999; font-size:13px; font-style:italic;">Tidak ada file absensi.</p>
            <?php else: ?>
                <?php foreach($files_absensi as $file): 
                    $path = $baseDir . 'absensi/' . $file;
                    $size = round(filesize($path) / 1024, 1) . ' KB';
                    $isImg = preg_match('/\.(jpg|jpeg|png)$/i', $file);
                ?>
                <div class="file-card">
                    <div class="fc-icon" style="color:#9c27b0;">
                        <?php if($isImg): ?>
                            <img src="<?= $path ?>" style="width:100%; height:80px; object-fit:cover; border-radius:4px;">
                        <?php else: ?>
                            <i class="fas fa-file-image"></i>
                        <?php endif; ?>
                    </div>
                    <?php if(!$isImg): ?>
                        <div class="fc-name" title="<?= $file ?>"><?= $file ?></div>
                        <div class="fc-size"><?= $size ?></div>
                    <?php endif; ?>
                    <div class="fc-actions">
                        <a href="<?= $path ?>" target="_blank" class="btn-action btn-view">Lihat</a>
                        <a href="<?= $path ?>" download class="btn-action btn-dl">Unduh</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>

<?php
// view_folder.php - Halaman untuk melihat isi folder arsip
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$arsip_dir = 'arsip/';

if (empty($folder) || !is_dir($arsip_dir . $folder)) {
    header('Location: index.php?page=arsip');
    exit;
}

$folder_path = $arsip_dir . $folder . '/';

// Fungsi untuk mendapatkan file dalam subfolder
function get_files_in_subfolder($path) {
    if (!is_dir($path)) return [];
    $items = scandir($path);
    $files = [];
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_file($path . $item)) {
            $files[] = $item;
        }
    }
    return $files;
}

$undangan_files = get_files_in_subfolder($folder_path . 'undangan/');
$notulensi_files = get_files_in_subfolder($folder_path . 'notulensi/');
$absensi_files = get_files_in_subfolder($folder_path . 'absensi/');

$message = '';
$message_type = '';

// Handle upload file (Add or Replace)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $subfolder = $_POST['subfolder'];
    $target_dir = $folder_path . $subfolder . '/';
    
    // Pastikan folder ada
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $target_file = $target_dir . basename($_FILES['file']['name']);
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $message = 'File berhasil tersimpan!';
        $message_type = 'success';
        // Refresh file lists
        $undangan_files = get_files_in_subfolder($folder_path . 'undangan/');
        $notulensi_files = get_files_in_subfolder($folder_path . 'notulensi/');
        $absensi_files = get_files_in_subfolder($folder_path . 'absensi/');
    } else {
        $message = 'Gagal upload file!';
        $message_type = 'error';
    }
}

// Handle delete file
if (isset($_GET['delete']) && isset($_GET['subfolder'])) {
    $file = $_GET['delete'];
    $sub = $_GET['subfolder'];
    $file_path = $folder_path . $sub . '/' . $file;
    if (file_exists($file_path)) {
        unlink($file_path);
        header('Location: view_folder.php?folder=' . urlencode($folder));
        exit;
    }
}

$parts = explode('_', $folder, 2);
$date = $parts[0];
$name = isset($parts[1]) ? str_replace('_', ' ', $parts[1]) : 'Unnamed';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($name) ?> - Arsip Rapat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         /* ===== RESET ===== */
         * { box-sizing: border-box; font-family: "Arial", serif; }

        body {
            margin: 0;
            background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
            color: #0d47a1;
        }

        .container { display: flex; min-height: 100vh; }

        .sidebar {
            width: 280px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: 5px 0 15px rgba(27, 110, 235, 0.1);
            padding: 20px 0;
            position: fixed;
            left: 0; top: 0;
            z-index: 1000;
        }

        .sidebar h2 {
            text-align: center; color: #1976d2;
            margin-bottom: 30px; font-size: 28px; font-weight: 700;
            position: relative;
        }

        .sidebar h2:after {
            content: ''; position: absolute; bottom: -10px; left: 50%;
            transform: translateX(-50%); width: 60px; height: 3px;
            background: #1976d2; border-radius: 3px;
        }

        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar li { margin: 5px 0; }
        
        .sidebar a {
            display: flex; align-items: center; padding: 15px 25px;
            color: #1e70ebff; text-decoration: none;
            transition: all 0.3s ease; font-size: 16px;
        }

        .sidebar a i { margin-right: 15px; width: 20px; text-align: center; }
        
        .sidebar a:hover, .sidebar a.active {
            background-color: #e3f2fd; color: #0d47a1;
            transform: translateX(5px);
        }
        
        .sidebar a.active { border-left: 4px solid #1976d2; font-weight: 600; }

        .main-content {
            flex: 1; padding: 30px; margin-left: 280px;
        }

        /* ACTIONS BAR */
        .action-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }

        .btn-back {
            text-decoration: none; color: #1976d2; font-weight: bold;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-back:hover { text-decoration: underline; }

        .page-title {
            font-size: 24px; color: #0d47a1; margin: 10px 0;
            border-bottom: 3px solid #1976d2; display: inline-block; padding-bottom: 5px;
        }

        .meta-info { color: #666; font-size: 14px; margin-bottom: 30px; }

        /* UPLOAD CARD */
        .upload-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px;
        }
        .upload-card h3 {
            margin-top: 0; color: #1976d2; font-size: 16px;
            border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;
        }

        .form-inline {
            display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;
        }

        .form-group { flex: 1; min-width: 200px; }
        .form-group label { display: block; font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #444; }
        
        .form-control {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
        }

        .btn-upload {
            padding: 9px 20px; background: #1976d2; color: white; border: none;
            border-radius: 4px; cursor: pointer; font-weight: bold;
        }
        .btn-upload:hover { background: #1565c0; }

        /* FILE SECTIONS */
        .files-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .file-section {
            background: white; padding: 0; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;
            display: flex; flex-direction: column; height: 100%;
        }

        .fs-header {
            padding: 15px 20px; background: #f1f8ff; border-bottom: 1px solid #e3f2fd;
            display: flex; align-items: center; gap: 10px;
        }
        .fs-header i { font-size: 18px; color: #1976d2; }
        .fs-title { font-weight: bold; color: #0d47a1; font-size: 16px; }
        .fs-count { 
            margin-left: auto; background: #1976d2; color: white; 
            font-size: 11px; padding: 2px 8px; border-radius: 10px; 
        }

        .file-list {
            list-style: none; padding: 0; margin: 0; flex: 1;
        }
        
        .file-item {
            padding: 15px 20px; border-bottom: 1px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .file-item:last-child { border-bottom: none; }
        .file-item:hover { background: #fafafa; }

        .f-name { font-size: 14px; color: #333; word-break: break-all; margin-right: 10px; }
        .f-name i { color: #888; margin-right: 8px; }

        .f-actions { display: flex; gap: 8px; }
        .f-btn {
            text-decoration: none; padding: 5px; border-radius: 4px;
            color: #555; transition: all 0.2s;
        }
        .f-btn:hover { background: #e3f2fd; color: #1976d2; }
        .f-btn.del:hover { background: #ffebee; color: #d32f2f; }

        .empty-msg {
            padding: 30px; text-align: center; color: #999; font-style: italic; font-size: 13px;
        }
        
        /* Modal Notification */
        .notif {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background: #333; color: white; padding: 10px 20px; border-radius: 5px;
            display: none; animation: fadeIn 0.3s; z-index: 2000;
        }
        @keyframes fadeIn { from{opacity: 0; top: 0;} to{opacity: 1; top: 20px;} }

    </style>
</head>
<body>

<div class="container">
    <div class="sidebar">
        <h2>UANG</h2>
        <ul>
            <li><a href="index.php?page=beranda"><i class="fas fa-home"></i>Beranda</a></li>
            <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i>Undangan</a></li>
            <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i>Notulensi</a></li>
            <li><a href="index.php?page=absensi"><i class="fas fa-user-check"></i>Absensi</a></li>
            <li><a href="index.php?page=arsip" class="active"><i class="fas fa-archive"></i>Arsip</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="action-bar">
            <a href="index.php?page=arsip" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>

        <div>
            <h1 class="page-title"><?= htmlspecialchars($name) ?></h1>
            <div class="meta-info"><i class="far fa-calendar"></i> Tanggal Rapat: <?= date('d F Y', strtotime($date)) ?></div>
        </div>

        <!-- UPLOAD SECTION (Managed as "Edit/Add") -->
        <div class="upload-card">
            <h3><i class="fas fa-cloud-upload-alt"></i> Tambah / Ganti File</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="form-inline">
                    <div class="form-group">
                        <label>Kategori Dokumen</label>
                        <select name="subfolder" class="form-control" required>
                            <option value="undangan">Undangan</option>
                            <option value="notulensi">Notulensi</option>
                            <option value="absensi">Absensi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-upload"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>

        <!-- FILES GRID -->
        <div class="files-grid">
            
            <!-- UNDANGAN -->
            <div class="file-section">
                <div class="fs-header">
                    <i class="fas fa-envelope"></i>
                    <span class="fs-title">Undangan</span>
                    <span class="fs-count"><?= count($undangan_files) ?></span>
                </div>
                <ul class="file-list">
                    <?php if(empty($undangan_files)): ?>
                        <div class="empty-msg">Belum ada file</div>
                    <?php else: ?>
                        <?php foreach($undangan_files as $f): ?>
                            <li class="file-item">
                                <span class="f-name"><i class="far fa-file-pdf"></i> <?= $f ?></span>
                                <div class="f-actions">
                                    <a href="<?= $folder_path ?>undangan/<?= $f ?>" target="_blank" class="f-btn" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="<?= $folder_path ?>undangan/<?= $f ?>" download class="f-btn" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="view_folder.php?folder=<?= urlencode($folder) ?>&delete=<?= urlencode($f) ?>&subfolder=undangan" class="f-btn del" onclick="return confirm('Hapus file ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- NOTULENSI -->
            <div class="file-section">
                <div class="fs-header">
                    <i class="fas fa-file-alt"></i>
                    <span class="fs-title">Notulensi</span>
                    <span class="fs-count"><?= count($notulensi_files) ?></span>
                </div>
                <ul class="file-list">
                    <?php if(empty($notulensi_files)): ?>
                        <div class="empty-msg">Belum ada file</div>
                    <?php else: ?>
                        <?php foreach($notulensi_files as $f): ?>
                            <li class="file-item">
                                <span class="f-name"><i class="far fa-file-word"></i> <?= $f ?></span>
                                <div class="f-actions">
                                    <a href="<?= $folder_path ?>notulensi/<?= $f ?>" target="_blank" class="f-btn" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="<?= $folder_path ?>notulensi/<?= $f ?>" download class="f-btn" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="view_folder.php?folder=<?= urlencode($folder) ?>&delete=<?= urlencode($f) ?>&subfolder=notulensi" class="f-btn del" onclick="return confirm('Hapus file ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- ABSENSI -->
            <div class="file-section">
                <div class="fs-header">
                    <i class="fas fa-user-check"></i>
                    <span class="fs-title">Absensi</span>
                    <span class="fs-count"><?= count($absensi_files) ?></span>
                </div>
                <ul class="file-list">
                    <?php if(empty($absensi_files)): ?>
                        <div class="empty-msg">Belum ada file</div>
                    <?php else: ?>
                        <?php foreach($absensi_files as $f): ?>
                            <li class="file-item">
                                <span class="f-name"><i class="far fa-image"></i> <?= $f ?></span>
                                <div class="f-actions">
                                    <a href="<?= $folder_path ?>absensi/<?= $f ?>" target="_blank" class="f-btn" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="<?= $folder_path ?>absensi/<?= $f ?>" download class="f-btn" title="Download"><i class="fas fa-download"></i></a>
                                    <a href="view_folder.php?folder=<?= urlencode($folder) ?>&delete=<?= urlencode($f) ?>&subfolder=absensi" class="f-btn del" onclick="return confirm('Hapus file ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>
</div>

<!-- Notification -->
<div id="notif-box" class="notif"></div>

<script>
    <?php if(!empty($message)): ?>
        const box = document.getElementById('notif-box');
        box.innerText = "<?= addslashes($message) ?>";
        box.style.display = 'block';
        box.style.background = "<?= $message_type == 'success' ? '#2e7d32' : '#c62828' ?>";
        setTimeout(() => { box.style.display = 'none'; }, 3000);
    <?php endif; ?>
</script>

</body>
</html>

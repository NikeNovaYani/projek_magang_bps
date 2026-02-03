<?php
// arsip.php - Halaman Arsip Rapat
$page = 'arsip';

// Direktori arsip
$arsip_dir = 'arsip/';

// Buat direktori jika belum ada
if (!is_dir($arsip_dir)) {
    mkdir($arsip_dir, 0755, true);
}

// Helper untuk format pesan alert
function set_alert($msg, $type) {
    echo "<script>window.onload = function() { showNotification('" . addslashes($msg) . "', '$type'); };</script>";
}

// Handle form submit untuk membuat folder baru & Upload File Sekaligus
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_folder'])) {
    $folder_name = trim($_POST['folder_name']);
    
    if (!empty($folder_name)) {
        $date = date('Y-m-d');
        // Sanitasi nama folder
        $safe_name = preg_replace('/[^A-Za-z0-9\-_]/', '_', $folder_name);
        $folder_path = $arsip_dir . $date . '_' . $safe_name . '/';

        if (!is_dir($folder_path)) {
            // 1. Buat Struktur Folder
            if (mkdir($folder_path, 0777, true)) {
                $subfolders = ['undangan', 'notulensi', 'absensi'];
                foreach ($subfolders as $sub) {
                    mkdir($folder_path . $sub . '/', 0777, true);
                }

                // 2. Handle File Uploads (Jika ada)
                $upload_errors = [];
                $uploaded_count = 0;

                $file_types = ['file_undangan' => 'undangan', 'file_notulensi' => 'notulensi', 'file_absensi' => 'absensi'];

                foreach ($file_types as $input_name => $target_sub) {
                    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
                        $tmp_name = $_FILES[$input_name]['tmp_name'];
                        $original_name = $_FILES[$input_name]['name'];
                        $target_file = $folder_path . $target_sub . '/' . basename($original_name);
                        
                        // Upload
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $uploaded_count++;
                        } else {
                            $upload_errors[] = "Gagal upload $target_sub";
                        }
                    }
                }

                $msg = 'Arsip "' . htmlspecialchars($folder_name) . '" berhasil dibuat!';
                if ($uploaded_count > 0) {
                    $msg .= " ($uploaded_count file tersimpan)";
                }
                if (!empty($upload_errors)) {
                    $msg .= ". Warning: " . implode(", ", $upload_errors);
                }
                
                set_alert($msg, 'success');

            } else {
                set_alert('Gagal membuat direktori sistem.', 'error');
            }
        } else {
            set_alert('Folder arsip dengan nama tersebut sudah ada!', 'error');
        }
    } else {
        set_alert('Nama kegiatan tidak boleh kosong!', 'error');
    }
}

// Handle edit folder name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_folder'])) {
    $old_folder = $_POST['old_folder'];
    $new_name = trim($_POST['new_name']);
    if (!empty($new_name) && is_dir($arsip_dir . $old_folder)) {
        $date_part = explode('_', $old_folder)[0];
        $new_folder = $date_part . '_' . preg_replace('/[^A-Za-z0-9\-_]/', '_', $new_name);
        if ($old_folder !== $new_folder) {
            if (!is_dir($arsip_dir . $new_folder)) {
                if(rename($arsip_dir . $old_folder, $arsip_dir . $new_folder)) {
                    set_alert('Nama arsip berhasil diubah!', 'success');
                } else {
                    set_alert('Gagal mengubah nama folder.', 'error');
                }
            } else {
                set_alert('Nama baru sudah digunakan!', 'error');
            }
        }
    }
}

// Handle delete folder
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_folder'])) {
    $folder_to_delete = $_POST['delete_folder'];
    if (is_dir($arsip_dir . $folder_to_delete)) {
        // Recursive Delete
        function delete_folder_recursive($dir) {
            if (!is_dir($dir)) return false;
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;
                is_dir($path) ? delete_folder_recursive($path) : unlink($path);
            }
            return rmdir($dir);
        }

        if (delete_folder_recursive($arsip_dir . $folder_to_delete)) {
            set_alert('Arsip berhasil dihapus permanen.', 'success');
        } else {
            set_alert('Gagal menghapus arsip.', 'error');
        }
    } else {
        set_alert('Arsip tidak ditemukan!', 'error');
    }
}

// Fungsi Helper untuk cek isi folder (untuk indikator UI)
function has_files($dir) {
    if(!is_dir($dir)) return false;
    $scan = array_diff(scandir($dir), ['.', '..']);
    return count($scan) > 0;
}

// Get Folders
function get_folders($dir) {
    $folders = [];
    if (is_dir($dir)) {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($dir . $item)) {
                $folders[] = $item;
            }
        }
    }
    return array_reverse($folders); // Terbaru diatas
}
$folders = get_folders($arsip_dir);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gudang Arsip - BPS Kota Depok</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
         /* ===== RESET ===== */
         * {
            box-sizing: border-box;
            font-family: "Arial", serif;
        }

        body {
            margin: 0;
            background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
            color: #0d47a1;
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
            margin-left: 140px; /* Lebar sidebar */
        }



        /* CARD STYLE (White Box with Shadow) */
        .card-panel {
            width: 50%;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .card-head h3 {
            margin-top: 0;
            color: #1976d2;
            font-size: 18px;
            border-bottom: 2px solid #e3f2fd;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* FORM CUSTOMIZATION */
        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1619ccff;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #1976d2;
            outline: none;
        }

        /* UPLOAD GRID */
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .upload-item {
            border: 2px dashed #bbdefb;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            background: #fdfdfd;
            transition: all 0.2s;
        }

        .upload-item:hover {
            border-color: #1976d2;
            background: #f0f7ff;
        }

        .upload-item label {
            cursor: pointer;
            display: block;
        }

        .upload-item i {
            font-size: 24px;
            color: #1976d2;
            margin-bottom: 10px;
            display: block;
        }

        .upload-item span {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            display: block;
        }

        .file-status {
            font-size: 11px;
            color: #10b981;
            margin-top: 5px;
            display: block;
        }

        /* BUTTONS */
        .btn-primary {
            background: #1976d2;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #1565c0;
        }

        /* ARSIP GRID */
        .archive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .archive-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-top: 4px solid #1976d2;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }

        .archive-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .ac-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .ac-icon {
            font-size: 32px;
            color: #ffa000;
        }

        .ac-actions button {
            background: none;
            border: none;
            cursor: pointer;
            color: #bbb;
            font-size: 16px;
            transition: color 0.2s;
        }

        .ac-actions button:hover {
            color: #d32f2f;
        }
        
        .ac-actions button.edit-btn:hover {
            color: #1976d2;
        }

        .ac-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .ac-date {
            font-size: 12px;
            color: #888;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* CHIPS */
        .file-chips {
            display: flex;
            gap: 5px;
            margin-top: auto;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .chip {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
            background: #f1f5f9;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .chip.active {
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
        }

        .btn-open {
            background: #f8f9fa;
            color: #1976d2;
            text-align: center;
            display: block;
            text-decoration: none;
            padding: 8px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            border: 1px solid #e3f2fd;
            transition: all 0.2s;
        }

        .btn-open:hover {
            background: #1976d2;
            color: white;
        }

        /* Notification */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 25px;
            background: white;
            border-left: 5px solid #1976d2;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border-radius: 4px;
            display: none;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .notification.success { border-color: #2e7d32; }
        .notification.error { border-color: #c62828; }

    </style>
</head>
<body>
    <div class="container">
        <!-- SIDEBAR -->
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

        <!-- MAIN CONTENT -->
        <div class="main-content">


            <!-- CARD BUAT ARSIP -->
            <div class="card-panel">
                <div class="card-head">
                    <h3><i class="fas fa-plus-circle"></i> Buat Arsip Baru</h3>
                </div>
                
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Nama Kegiatan / Rapat</label>
                        <input type="text" name="folder_name" class="form-control" placeholder="Contoh: Rapat Koordinasi Bulan Desember" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="margin-top:20px; border-bottom:1px dashed #ccc; padding-bottom:5px;">
                            Upload File (Opsional) - Bisa dilakukan nanti
                        </label>
                        <div class="upload-grid">
                            <div class="upload-item">
                                <label>
                                    <input type="file" name="file_undangan" style="display:none" onchange="updateFileName(this)">
                                    <i class="fas fa-envelope-open-text"></i>
                                    <span>Undangan</span>
                                    <small class="file-status"></small>
                                </label>
                            </div>
                            <div class="upload-item">
                                <label>
                                    <input type="file" name="file_notulensi" style="display:none" onchange="updateFileName(this)">
                                    <i class="fas fa-file-contract"></i>
                                    <span>Notulensi</span>
                                    <small class="file-status"></small>
                                </label>
                            </div>
                            <div class="upload-item">
                                <label>
                                    <input type="file" name="file_absensi" style="display:none" onchange="updateFileName(this)">
                                    <i class="fas fa-users"></i>
                                    <span>Absensi</span>
                                    <small class="file-status"></small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="create_folder" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Arsip
                    </button>
                </form>
            </div>

            <!-- LIST ARSIP -->
            <div class="archive-grid">
                <?php foreach ($folders as $folder): ?>
                    <?php
                    $parts = explode('_', $folder, 2);
                    $date = $parts[0];
                    $name = isset($parts[1]) ? str_replace('_', ' ', $parts[1]) : 'Unnamed';
                    $path = $arsip_dir . $folder . '/';
                    
                    $has_undangan = has_files($path . 'undangan');
                    $has_notulensi = has_files($path . 'notulensi');
                    $has_absensi = has_files($path . 'absensi');
                    ?>
                    
                    <div class="archive-card">
                        <div class="ac-header">
                            <i class="fas fa-folder ac-icon"></i>
                            <div class="ac-actions">
                                <button class="edit-btn" onclick="toggleEdit('<?= $folder ?>')"><i class="fas fa-pen"></i></button>
                                <button onclick="if(confirm('Hapus arsip ini?')) document.getElementById('del-<?= $folder ?>').submit()"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <div class="ac-title"><?= htmlspecialchars($name) ?></div>
                        <div class="ac-date"><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($date)) ?></div>

                        <!-- Edit Form -->
                        <div id="edit-<?= $folder ?>" style="display:none; margin-bottom:10px;">
                            <form method="post">
                                <input type="hidden" name="edit_folder" value="true">
                                <input type="hidden" name="old_folder" value="<?= $folder ?>">
                                <div style="display:flex; gap:5px;">
                                    <input type="text" name="new_name" value="<?= htmlspecialchars($name) ?>" class="form-control" style="font-size:12px; padding:5px;">
                                    <button type="submit" style="background:#2e7d32; color:white; border:none; border-radius:4px;"><i class="fas fa-check"></i></button>
                                </div>
                            </form>
                        </div>
                        <form id="del-<?= $folder ?>" method="post" style="display:none;"><input type="hidden" name="delete_folder" value="<?= $folder ?>"></form>

                        <div class="file-chips">
                            <span class="chip <?= $has_undangan ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Undangan</span>
                            <span class="chip <?= $has_notulensi ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Notulensi</span>
                            <span class="chip <?= $has_absensi ? 'active' : '' ?>"><i class="fas fa-user-check"></i> Absensi</span>
                        </div>

                        <a href="view_folder.php?folder=<?= urlencode($folder) ?>" class="btn-open">
                            <i class="fas fa-external-link-alt"></i> Buka Arsip
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- NOTIFICATION -->
    <div id="notif" class="notification">
        <span id="notif-msg"></span>
    </div>

    <script>
        function updateFileName(input) {
            if(input.files && input.files[0]) {
                const span = input.closest('label').querySelector('.file-status');
                span.innerText = "Terpilih: " + input.files[0].name;
                span.style.color = '#1976d2';
                // Ubah border boxnya biar keliatan aktif
                input.closest('.upload-item').style.borderColor = '#1976d2';
                input.closest('.upload-item').style.background = '#e3f2fd';
            }
        }

        function toggleEdit(id) {
            const el = document.getElementById('edit-' + id);
            el.style.display = el.style.display === 'block' ? 'none' : 'block';
        }

        function showNotification(msg, type) {
            const el = document.getElementById('notif');
            const txt = document.getElementById('notif-msg');
            el.className = 'notification ' + type;
            txt.innerText = msg;
            el.style.display = 'block';
            setTimeout(() => {
                el.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>

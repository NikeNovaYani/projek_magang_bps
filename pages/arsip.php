<?php
// arsip.php - Halaman Arsip Rapat
$page = 'arsip';

// Fix: Include database connection globally
require_once __DIR__ . '/../koneksi.php';

// Direktori arsip
$arsip_dir = 'arsip/';

// Buat direktori jika belum ada
if (!is_dir($arsip_dir)) {
    mkdir($arsip_dir, 0755, true);
}

// Helper untuk format pesan alert
function set_alert($msg, $type)
{
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

                // Track paths for Database
                $db_undangan = NULL;
                $db_notulensi = NULL;
                $db_absensi = NULL;

                $file_types = ['file_undangan' => 'undangan', 'file_notulensi' => 'notulensi', 'file_absensi' => 'absensi'];

                foreach ($file_types as $input_name => $target_sub) {
                    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0) {
                        $tmp_name = $_FILES[$input_name]['tmp_name'];
                        $original_name = $_FILES[$input_name]['name'];
                        $target_file = $folder_path . $target_sub . '/' . basename($original_name);

                        // Upload
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $uploaded_count++;

                            // Map to DB variables
                            if ($target_sub === 'undangan') $db_undangan = $target_file;
                            if ($target_sub === 'notulensi') $db_notulensi = $target_file;
                            if ($target_sub === 'absensi') $db_absensi = $target_file;
                        } else {
                            $upload_errors[] = "Gagal upload $target_sub";
                        }
                    }
                }

                // 3. Masukkan ke Database arsip_manual
                if ($koneksi) {
                    $nm = mysqli_real_escape_string($koneksi, $folder_name);
                    $qu = mysqli_real_escape_string($koneksi, $db_undangan ?? '');
                    $qn = mysqli_real_escape_string($koneksi, $db_notulensi ?? '');
                    $qa = mysqli_real_escape_string($koneksi, $db_absensi ?? '');

                    // Handle NULLs for SQL
                    $qu = $qu ? "'$qu'" : "NULL";
                    $qn = $qn ? "'$qn'" : "NULL";
                    $qa = $qa ? "'$qa'" : "NULL";

                    $sql = "INSERT INTO arsip_manual (nama_kegiatan, file_undangan, file_notulensi, file_absensi) 
                            VALUES ('$nm', $qu, $qn, $qa)";
                    mysqli_query($koneksi, $sql);
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
                if (rename($arsip_dir . $old_folder, $arsip_dir . $new_folder)) {
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
        function delete_folder_recursive($dir)
        {
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

// === HANDLE REPLACE / UPLOAD FILE (MANUAL & AUTOMATIC) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['replace_file'])) {
    $id_referensi = $_POST['id_referensi'];
    $file_type = $_POST['file_type']; // 'file_undangan', etc. or 'undangan'
    $source = $_POST['source'] ?? 'manual'; // 'manual' or 'otomatis'

    // Mapping normalized type
    $type_norm = str_replace('file_', '', $file_type); // undangan, notulensi, absensi

    if (!empty($id_referensi) && isset($_FILES['new_file']) && $_FILES['new_file']['error'] == 0) {
        $tmp_name = $_FILES['new_file']['tmp_name'];
        $original_name = $_FILES['new_file']['name'];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $clean_name = preg_replace('/[^A-Za-z0-9\-_]/', '', pathinfo($original_name, PATHINFO_FILENAME));
        $new_filename = time() . '_' . $clean_name . '.' . $ext;

        if ($source == 'manual') {
            // --- LOGIC MANUAL ---
            $col_name = 'file_' . $type_norm; // file_undangan, file_notulensi...

            // 1. Ambil path lama & folder info
            $query_old = "SELECT * FROM arsip_manual WHERE id_am = '$id_referensi'";
            $result_old = mysqli_query($koneksi, $query_old);
            $data_old = mysqli_fetch_assoc($result_old);

            if ($data_old) {
                // Determine Target Directory
                $target_dir = "";
                $old_path = $data_old[$col_name];

                // A. Use old path if exists
                if (!empty($old_path)) {
                    // Fix: Check if absolute or relative
                    if (file_exists($old_path)) {
                        $target_dir = dirname($old_path) . '/';
                    } elseif (file_exists("arsip/" . $old_path)) { // Handle legacy relative stored as relative
                        $target_dir = "arsip/" . dirname($old_path) . '/'; // Incorrect logic if path is just "file.pdf" inside folder. 
                        // Better: just dirname of the full path
                        $target_dir = dirname("arsip/" . $old_path) . '/'; // This is getting messy.
                        // Let's stick to the previous reliable logic: find ANY sibling file.
                    }
                }

                // B. Fallback: Find sibling files
                if (empty($target_dir) || !is_dir($target_dir)) {
                    $siblings = ['file_undangan', 'file_notulensi', 'file_absensi'];
                    foreach ($siblings as $sib) {
                        if (!empty($data_old[$sib]) && file_exists($data_old[$sib])) {
                            $sibling_dir = dirname($data_old[$sib]);
                            // Navigate to correct subfolder
                            // Structure: .../undangan/file.pdf. Parent: .../
                            // Desired: .../notulensi/
                            $parent = dirname($sibling_dir);
                            $target_dir = $parent . '/' . $type_norm . '/';
                            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                            break;
                        }
                    }
                }

                // C. Fallback: Scan folders (Last Resort)
                if (empty($target_dir)) {
                    $folders = scandir($arsip_dir);
                    $sanitized_param = preg_replace('/[^A-Za-z0-9\-_]/', '_', $data_old['nama_kegiatan']);
                    foreach ($folders as $fol) {
                        if (strpos($fol, $sanitized_param) !== false) {
                            $target_dir = $arsip_dir . $fol . '/' . $type_norm . '/';
                            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                            break;
                        }
                    }
                }

                if (!empty($target_dir) && is_dir($target_dir)) {
                    $target_file = $target_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        // DB Path (Relative prefer 'arsip/...')
                        // Calc relative path
                        $pos = strpos($target_file, 'arsip/');
                        $db_path = ($pos !== false) ? substr($target_file, $pos) : $target_file;

                        // Delete old
                        if (!empty($old_path) && file_exists($old_path) && $old_path != $db_path) {
                            unlink($old_path);
                        }

                        mysqli_query($koneksi, "UPDATE arsip_manual SET $col_name = '$db_path' WHERE id_am = '$id_referensi'");
                        set_alert("File berhasil diupload/diganti!", 'success');
                    } else {
                        set_alert("Gagal move_uploaded_file.", 'error');
                    }
                } else {
                    set_alert("Folder tujuan tidak ditemukan. Hubungi admin.", 'error');
                }
            }
        } elseif ($source == 'otomatis') {
            // --- LOGIC OTOMATIS ---
            // Undangan -> table undangan (undangan_pdf) -> folder arsip_pdf/
            // Notulensi -> table notulensi (notulensi_pdf) -> folder arsip_pdf/
            // Absensi -> table notulensi (foto_absensi) -> folder uploads/absensi/ (JSON)

            $uploaded = false;

            if ($type_norm == 'undangan') {
                $target_dir = "arsip_pdf/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

                $target_file = $target_dir . $new_filename;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Update DB
                    // Delete old file? Need query first.
                    $q = mysqli_query($koneksi, "SELECT undangan_pdf FROM undangan WHERE id_u = '$id_referensi'");
                    $d = mysqli_fetch_assoc($q);
                    if ($d && !empty($d['undangan_pdf']) && file_exists($target_dir . $d['undangan_pdf'])) {
                        unlink($target_dir . $d['undangan_pdf']);
                    }

                    mysqli_query($koneksi, "UPDATE undangan SET undangan_pdf = '$new_filename' WHERE id_u = '$id_referensi'");
                    $uploaded = true;
                }
            } elseif ($type_norm == 'notulensi') {
                $target_dir = "arsip_pdf/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

                $target_file = $target_dir . $new_filename;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Delete old
                    $q = mysqli_query($koneksi, "SELECT notulensi_pdf FROM notulensi WHERE id_n = '$id_referensi'"); // id_referensi for notulensi IS id_n/id_u typically
                    // Wait, in loop: id_referensi = id_u.
                    // And usually id_n = id_u.
                    // But safe to assume id passed IS the correct ID for keys.
                    $d = mysqli_fetch_assoc($q);
                    if ($d && !empty($d['notulensi_pdf']) && file_exists($target_dir . $d['notulensi_pdf'])) {
                        unlink($target_dir . $d['notulensi_pdf']);
                    }

                    mysqli_query($koneksi, "UPDATE notulensi SET notulensi_pdf = '$new_filename' WHERE id_n = '$id_referensi'");
                    $uploaded = true;
                }
            } elseif ($type_norm == 'absensi') {
                $target_dir = "uploads/absensi/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

                $target_file = $target_dir . $new_filename;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Handle JSON array
                    // Check old
                    $q = mysqli_query($koneksi, "SELECT foto_absensi FROM notulensi WHERE id_n = '$id_referensi'");
                    $d = mysqli_fetch_assoc($q);
                    $old_files = json_decode($d['foto_absensi'] ?? '[]', true);

                    // Delete old files?
                    // User request: "replace existing" or "upload empty".
                    // If replacing, we might want to clear old ones or append? 
                    // "jika file... dihapus... tombol buat atau upload... untuk mengisi file tersebut"
                    // Implies filling the void. Converting to single file array is safest for "Restore".
                    if (is_array($old_files)) {
                        foreach ($old_files as $of) {
                            if (file_exists($target_dir . $of)) unlink($target_dir . $of);
                        }
                    }

                    $new_json = json_encode([$new_filename]);
                    mysqli_query($koneksi, "UPDATE notulensi SET foto_absensi = '$new_json' WHERE id_n = '$id_referensi'");
                    $uploaded = true;
                }
            }

            if ($uploaded) {
                set_alert("File Otomatis berhasil diupload!", 'success');
            } else {
                set_alert("Gagal upload file otomatis.", 'error');
            }
        }
    } else {
        set_alert("Input tidak valid atau file kosong.", 'error');
    }
}

// === HANDLE DELETE AUTOMATIC ARCHIVE (FOLDER) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_otomatis'])) {
    $id = $_POST['id_hapus']; // id_u (sama dengan id_n)

    if ($id > 0) {
        // 1. Hapus File Undangan
        $q_u = mysqli_query($koneksi, "SELECT undangan_pdf FROM undangan WHERE id_u = '$id'");
        if ($d_u = mysqli_fetch_assoc($q_u)) {
            if (!empty($d_u['undangan_pdf']) && file_exists("arsip_pdf/" . $d_u['undangan_pdf'])) {
                unlink("arsip_pdf/" . $d_u['undangan_pdf']);
            }
        }

        // 2. Hapus File Notulensi & Gambar
        $q_n = mysqli_query($koneksi, "SELECT notulensi_pdf, foto_dokumentasi, foto_absensi FROM notulensi WHERE id_n = '$id'");
        if ($d_n = mysqli_fetch_assoc($q_n)) {
            // PDF
            if (!empty($d_n['notulensi_pdf']) && file_exists("arsip_pdf/" . $d_n['notulensi_pdf'])) {
                unlink("arsip_pdf/" . $d_n['notulensi_pdf']);
            }
            // Dokumentasi
            $docs = json_decode($d_n['foto_dokumentasi'] ?? '[]', true);
            foreach ($docs as $d) {
                if (file_exists("uploads/dokumentasi/$d")) unlink("uploads/dokumentasi/$d");
            }
            // Absensi
            $abs = json_decode($d_n['foto_absensi'] ?? '[]', true);
            foreach ($abs as $a) {
                if (file_exists("uploads/absensi/$a")) unlink("uploads/absensi/$a");
            }
        }

        // 3. Delete Database Records
        mysqli_query($koneksi, "DELETE FROM notulensi WHERE id_n = '$id'"); // Hapus anak dulu
        if (mysqli_query($koneksi, "DELETE FROM undangan WHERE id_u = '$id'")) {
            set_alert("Arsip (Notulensi/Undangan) berhasil dihapus permanen.", 'success');
        } else {
            set_alert("Gagal menghapus data: " . mysqli_error($koneksi), 'error');
        }
    }
}

// === HANDLE DELETE INDIVIDUAL FILE (MANUAL & AUTOMATIC) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_file_item'])) {
    $id = $_POST['id_ref'];
    $type = $_POST['file_type']; // undangan, notulensi, absensi
    $source = $_POST['source'];   // manual, otomatis

    if ($source == 'manual') {
        // --- MANUAL ---
        $col = ($type == 'undangan') ? 'file_undangan' : (($type == 'notulensi') ? 'file_notulensi' : 'file_absensi');

        $q = mysqli_query($koneksi, "SELECT * FROM arsip_manual WHERE id_am = '$id'");
        if ($row = mysqli_fetch_assoc($q)) {
            $path = $row[$col];
            if (!empty($path) && file_exists($path)) {
                unlink($path);
            }
            // Update DB -> NULL
            mysqli_query($koneksi, "UPDATE arsip_manual SET $col = NULL WHERE id_am = '$id'");
            set_alert("File $type berhasil dihapus.", 'success');
        }
    } else {
        // --- OTOMATIS ---
        if ($type == 'undangan') {
            // 1. Cek & Hapus File Fisik
            $q = mysqli_query($koneksi, "SELECT undangan_pdf FROM undangan WHERE id_u = '$id'");
            if ($row = mysqli_fetch_assoc($q)) {
                if (!empty($row['undangan_pdf']) && file_exists("arsip_pdf/" . $row['undangan_pdf'])) {
                    unlink("arsip_pdf/" . $row['undangan_pdf']);
                }
            }
            // 2. Update Database (Pastikan terset NULL/kosong)
            // Gunakan NULL unquoted agar benar-benar NULL di MySQL
            $update = mysqli_query($koneksi, "UPDATE undangan SET undangan_pdf = NULL WHERE id_u = '$id'");

            if ($update) {
                set_alert("File Undangan berhasil dihapus.", 'success');
            } else {
                set_alert("Gagal update database: " . mysqli_error($koneksi), 'error');
            }
        } elseif ($type == 'notulensi') {
            // 1. Cek & Hapus File Fisik
            $q = mysqli_query($koneksi, "SELECT notulensi_pdf FROM notulensi WHERE id_n = '$id'");
            if ($row = mysqli_fetch_assoc($q)) {
                if (!empty($row['notulensi_pdf']) && file_exists("arsip_pdf/" . $row['notulensi_pdf'])) {
                    unlink("arsip_pdf/" . $row['notulensi_pdf']);
                }
            }
            // 2. Update Database
            // Note: id_n harus match id_u dari undangan
            $update = mysqli_query($koneksi, "UPDATE notulensi SET notulensi_pdf = NULL WHERE id_n = '$id'");

            if ($update) {
                set_alert("File Notulensi berhasil dihapus.", 'success');
            } else {
                set_alert("Gagal update database notulensi: " . mysqli_error($koneksi), 'error');
            }
        } elseif ($type == 'absensi') {
            // Hapus Foto Absensi
            $q = mysqli_query($koneksi, "SELECT foto_absensi FROM notulensi WHERE id_n = '$id'");
            if ($row = mysqli_fetch_assoc($q)) {
                $files = json_decode($row['foto_absensi'] ?? '[]', true);
                if (is_array($files)) {
                    foreach ($files as $f) {
                        if (file_exists("uploads/absensi/$f")) unlink("uploads/absensi/$f");
                    }
                }
            }
            $update = mysqli_query($koneksi, "UPDATE notulensi SET foto_absensi = '[]' WHERE id_n = '$id'");
            if ($update) {
                set_alert("File Absensi berhasil dihapus.", 'success');
            } else {
                set_alert("Gagal update absensi: " . mysqli_error($koneksi), 'error');
            }
        }
    }
}

// Fungsi Helper untuk cek isi folder (untuk indikator UI)
function has_files($dir)
{
    if (!is_dir($dir)) return false;
    $scan = array_diff(scandir($dir), ['.', '..']);
    return count($scan) > 0;
}

// Get Folders
function get_folders($dir)
{
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
            margin-left: 140px;
            /* Lebar sidebar */
        }



        /* CARD STYLE (White Box with Shadow) */
        .card-panel {
            width: 100%;
            height: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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
            font-size: 15px;
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
            padding: 5px;
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
            font-size: 18px;
            color: #1976d2;
            margin-bottom: 10px;
            display: block;
        }

        .upload-item span {
            font-size: 10px;
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
            padding: 8px 10px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 5px;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-top: 4px solid #1976d2;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }

        .archive-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
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
            flex-direction: column;
            /* Vertical Layout */
            gap: 8px;
            /* Jarak antar baris */
            margin-top: auto;
            margin-bottom: 15px;
        }

        .file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* File kiri, tombol kanan */
            background: #f8fafc;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .file-left {
            display: flex;
            align-items: center;
            gap: 5px;
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            display: none;
            z-index: 2000;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.success {
            border-color: #2e7d32;
        }

        .notification.error {
            border-color: #c62828;
        }

        /* Badge untuk membedakan Arsip Manual vs Otomatis */
        .badge-sumber {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Warna Orange untuk Manual */
        .badge-manual {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        /* Warna Biru untuk System */
        .badge-auto {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        /* Style untuk Chip/Tombol File */
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            text-decoration: none;
            margin-right: 5px;
            transition: 0.2s;
            border: 1px solid transparent;
        }

        .chip.active {
            background-color: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .chip.active:hover {
            background-color: #e2e8f0;

            .chip.disabled {
                background-color: #f8fafc;
                color: #cbd5e1;
                cursor: not-allowed;
            }
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
                <li style="position: absolute; bottom: 0px; right: 0px; left: 0px;"><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">


            <!-- CARD BUAT ARSIP -->
            <div class="card-panel">
                <div class="card-head">
                    <h3>Buat Arsip Baru</h3>
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
                <?php
                // 1. Panggil Data dari VIEW SQL
                $query = "SELECT * FROM view_semua_arsip ORDER BY tanggal DESC";
                $result = mysqli_query($koneksi, $query);

                // 2. Loop Data Satu per Satu
                while ($row = mysqli_fetch_assoc($result)):

                    // Cek Sumber Arsip (Manual / Otomatis) untuk LINK saja, Tampilan disamakan
                    $isManual = ($row['sumber'] == 'manual');

                    // Tentukan Warna Default (Biru)
                    $iconColor   = '#2563eb';
                ?>

                    <div class="archive-card">
                        <div class="ac-header">
                            <i class="fas fa-folder ac-icon" style="color: <?= $iconColor ?>"></i>

                            <div class="ac-actions">
                                <!-- TOMBOL HAPUS FOLDER (SEMUA TIPE) -->
                                <form method="post" onsubmit="return confirm('Hapus arsip ini beserta seluruh isinya?');" style="display:inline;">
                                    <input type="hidden" name="id_hapus" value="<?= $row['id_referensi'] ?>">
                                    <?php if ($isManual): ?>
                                        <button type="submit" name="hapus_manual" title="Hapus Arsip Manual" style="border:none; background:none; cursor:pointer; color:#ef4444;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="hapus_otomatis" title="Hapus Arsip Otomatis" style="border:none; background:none; cursor:pointer; color:#ef4444;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>

                        <div class="ac-title" title="<?= htmlspecialchars($row['nama_kegiatan']) ?>">
                            <?= htmlspecialchars(substr($row['nama_kegiatan'], 0, 50)) . (strlen($row['nama_kegiatan']) > 50 ? '...' : '') ?>
                        </div>

                        <div class="ac-date">
                            <i class="far fa-calendar"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?>
                        </div>

                        <!-- LIST FILE (VERTICAL) -->
                        <div class="file-chips">

                            <!-- 1. FILE UNDANGAN -->
                            <div class="file-row">
                                <div class="file-left">
                                    <?php if ($row['ada_undangan']):
                                        if ($isManual) {
                                            $linkU = (strpos($row['link_undangan'], 'arsip/') === 0) ? $row['link_undangan'] : "arsip/" . $row['folder_path'] . "/undangan/" . $row['link_undangan'];
                                        } else {
                                            $linkU = "arsip_pdf/" . $row['link_undangan'];
                                        }
                                    ?>
                                        <a href="<?= $linkU ?>" target="_blank" class="chip active">
                                            <i class="fas fa-envelope"></i> Undangan
                                        </a>
                                        <!-- Edit Button -->
                                        <?php if ($isManual): ?>
                                            <button type="button" class="btn-edit-mini" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_undangan', 'manual')" title="Ganti File" style="background:none; border:none; cursor:pointer; color:#f59e0b;">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="index.php?page=undangan&id=<?= $row['id_referensi'] ?>" class="btn-edit-mini" title="Edit Data" style="color:#f59e0b;">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        <?php endif; ?>

                                </div>
                                <!-- Delete File Button -->
                                <div class="file-right">
                                    <form method="post" onsubmit="return confirm('Hapus file undangan ini?');" style="margin:0;">
                                        <input type="hidden" name="hapus_file_item" value="1">
                                        <input type="hidden" name="id_ref" value="<?= $row['id_referensi'] ?>">
                                        <input type="hidden" name="file_type" value="undangan">
                                        <input type="hidden" name="source" value="<?= $isManual ? 'manual' : 'otomatis' ?>">
                                        <button type="submit" title="Hapus File" style="border:none; background:none; cursor:pointer; color:#ef4444;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="chip disabled"><i class="fas fa-envelope"></i> Undangan</span>
                                </div>
                                <div class="file-right">
                                    <?php if ($isManual): ?>
                                        <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_undangan', 'manual')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="index.php?page=undangan&id=<?= $row['id_referensi'] ?>" class="btn-edit-mini" title="Buat Undangan" style="color:#2563eb; margin-right:5px; text-decoration:none;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'undangan', 'otomatis')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            </div>

                            <!-- 2. FILE NOTULENSI -->
                            <div class="file-row">
                                <div class="file-left">
                                    <?php if ($row['ada_notulensi']):
                                        if ($isManual) {
                                            $linkN = (strpos($row['link_notulensi'], 'arsip/') === 0) ? $row['link_notulensi'] : "arsip/" . $row['folder_path'] . "/notulensi/" . $row['link_notulensi'];
                                        } else {
                                            $linkN = !empty($row['notulensi_pdf']) ? "arsip_pdf/" . $row['notulensi_pdf'] : "pages/cetak_notulensi.php?id=" . $row['id_referensi'];
                                        }
                                    ?>
                                        <a href="<?= $linkN ?>" target="_blank" class="chip active">
                                            <i class="fas fa-file-alt"></i> Notulensi
                                        </a>
                                        <!-- Edit Button -->
                                        <?php if ($isManual): ?>
                                            <button type="button" class="btn-edit-mini" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_notulensi', 'manual')" title="Ganti File" style="background:none; border:none; cursor:pointer; color:#f59e0b;">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="index.php?page=notulensi&id=<?= $row['id_referensi'] ?>" class="btn-edit-mini" title="Edit Data" style="color:#f59e0b;">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        <?php endif; ?>

                                </div>
                                <!-- Delete File Button -->
                                <div class="file-right">
                                    <form method="post" onsubmit="return confirm('Hapus file notulensi ini?');" style="margin:0;">
                                        <input type="hidden" name="hapus_file_item" value="1">
                                        <input type="hidden" name="id_ref" value="<?= $row['id_referensi'] ?>">
                                        <input type="hidden" name="file_type" value="notulensi">
                                        <input type="hidden" name="source" value="<?= $isManual ? 'manual' : 'otomatis' ?>">
                                        <button type="submit" title="Hapus File" style="border:none; background:none; cursor:pointer; color:#ef4444;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <?php if (!$isManual && $row['ada_undangan']): ?>
                                        <a href="index.php?page=notulensi&id=<?= $row['id_referensi'] ?>" class="chip active" style="background:#eab308; color:white;">
                                            <i class="fas fa-plus"></i> Buat Notulensi
                                        </a>
                                    <?php else: ?>
                                        <span class="chip disabled"><i class="fas fa-file-alt"></i> Notulensi</span>
                                    <?php endif; ?>
                                </div>
                                <div class="file-right">
                                    <?php if ($isManual): ?>
                                        <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_notulensi', 'manual')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php else: ?>
                                        <?php if (!$isManual && $row['ada_undangan']): ?>
                                            <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'notulensi', 'otomatis')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            </div>

                            <!-- 3. FILE ABSENSI -->
                            <div class="file-row">
                                <div class="file-left">
                                    <?php if ($row['ada_absensi']):
                                        if ($isManual) {
                                            $linkA = (strpos($row['link_absensi'], 'arsip/') === 0) ? $row['link_absensi'] : "arsip/" . $row['folder_path'] . "/absensi/" . $row['link_absensi'];
                                        } else {
                                            $files = json_decode($row['link_absensi'], true);
                                            $linkA = (!empty($files) && is_array($files)) ? ((strpos($files[0], '/') === false) ? "uploads/absensi/" . $files[0] : $files[0]) : "#";
                                        }
                                    ?>
                                        <a href="<?= $linkA ?>" target="_blank" class="chip active">
                                            <i class="fas fa-user-check"></i> Absensi
                                        </a>
                                        <!-- Edit Button -->
                                        <?php if ($isManual): ?>
                                            <button type="button" class="btn-edit-mini" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_absensi', 'manual')" title="Ganti File" style="background:none; border:none; cursor:pointer; color:#f59e0b;">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        <?php endif; ?>

                                </div>
                                <!-- Delete File Button -->
                                <div class="file-right">
                                    <form method="post" onsubmit="return confirm('Hapus file absensi ini?');" style="margin:0;">
                                        <input type="hidden" name="hapus_file_item" value="1">
                                        <input type="hidden" name="id_ref" value="<?= $row['id_referensi'] ?>">
                                        <input type="hidden" name="file_type" value="absensi">
                                        <input type="hidden" name="source" value="<?= $isManual ? 'manual' : 'otomatis' ?>">
                                        <button type="submit" title="Hapus File" style="border:none; background:none; cursor:pointer; color:#ef4444;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="chip disabled"><i class="fas fa-user-check"></i> Absensi</span>
                                </div>
                                <div class="file-right">
                                    <?php if ($isManual): ?>
                                        <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'file_absensi', 'manual')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="triggerReplace('<?= $row['id_referensi'] ?>', 'absensi', 'otomatis')" title="Upload File" style="border:none; background:none; cursor:pointer; color:#2563eb;">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

                <?php if (mysqli_num_rows($result) == 0): ?>
                    <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #94a3b8; border: 2px dashed #cbd5e1; border-radius: 8px;">
                        <i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 10px; color: #cbd5e1;"></i>
                        <p>Belum ada arsip tersimpan.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- HIDDEN FORM FOR REPLACING FILES -->
    <form id="form-replace" method="post" enctype="multipart/form-data" style="display:none;">
        <input type="hidden" name="replace_file" value="1">
        <input type="hidden" name="id_referensi" id="replace-id">
        <input type="hidden" name="file_type" id="replace-type">
        <input type="hidden" name="source" id="replace-source">
        <input type="file" name="new_file" id="replace-input" onchange="submitReplacement()">
    </form>

    <!-- NOTIFICATION -->
    <div id="notif" class="notification">
        <span id="notif-msg"></span>
    </div>

    <script>
        // Trigger File Input for Replacement
        function triggerReplace(id, type, source = 'manual') {
            document.getElementById('replace-id').value = id;
            document.getElementById('replace-type').value = type;
            document.getElementById('replace-source').value = source;
            document.getElementById('replace-input').click();
        }

        // Auto Submit when file selected
        function submitReplacement() {
            const input = document.getElementById('replace-input');
            if (input.files.length > 0) {
                if (confirm('Apakah Anda yakin ingin mengganti file ini dengan "' + input.files[0].name + '"?')) {
                    document.getElementById('form-replace').submit();
                } else {
                    input.value = ''; // Reset if cancelled
                }
            }
        }

        function updateFileName(input) {
            if (input.files && input.files[0]) {
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
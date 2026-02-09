<?php
session_start();
require_once '../koneksi.php';

// 1. Validasi Request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// 2. Tangkap Data dari Form
// Pastikan nama input di HTML (<input name="...">) sesuai dengan $_POST di sini
$id_notulensi   = $_POST['id_n'] ?? 0; // ID jika mode edit (biasanya sama dengan ID Undangan)
$id_undangan    = $_POST['id_undangan'] ?? 0; // Wajib ada untuk relasi!

$nama_kegiatan  = mysqli_real_escape_string($koneksi, $_POST['nama_kegiatan'] ?? '');
$unit_kerja     = mysqli_real_escape_string($koneksi, $_POST['unit_kerja'] ?? '');
$hari_tanggal   = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? date('Y-m-d'));
$waktu_mulai    = mysqli_real_escape_string($koneksi, $_POST['pukul_mulai'] ?? '');
$waktu_selesai  = mysqli_real_escape_string($koneksi, $_POST['pukul_selesai'] ?? '');
$tempat         = mysqli_real_escape_string($koneksi, $_POST['tempat'] ?? '');
$pimpinan       = mysqli_real_escape_string($koneksi, $_POST['pimpinan'] ?? '');
$peserta        = mysqli_real_escape_string($koneksi, $_POST['peserta'] ?? ''); // Jumlah/Keterangan Peserta
$isi_pembukaan  = mysqli_real_escape_string($koneksi, $_POST['pembukaan'] ?? '');
$isi_pembahasan = mysqli_real_escape_string($koneksi, $_POST['pembahasan'] ?? '');
$isi_kesimpulan = mysqli_real_escape_string($koneksi, $_POST['kesimpulan'] ?? '');

// Data Tanda Tangan
$ttd_tempat     = mysqli_real_escape_string($koneksi, $_POST['ttd_tempat'] ?? 'Depok');
$ttd_tanggal    = mysqli_real_escape_string($koneksi, $_POST['ttd_tanggal'] ?? date('Y-m-d'));
$ttd_nama       = mysqli_real_escape_string($koneksi, $_POST['ttd_nama'] ?? '');

// 3. Handle Upload File (Dokumentasi & Absensi)
// Fungsi helper upload sederhana
function handleUpload($inputName, $targetDir)
{
    $uploadedFiles = [];
    if (!empty($_FILES[$inputName]['name'][0])) {
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        foreach ($_FILES[$inputName]['name'] as $key => $name) {
            if ($_FILES[$inputName]['error'][$key] === 0) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid($inputName . '_') . '.' . $ext;
                if (move_uploaded_file($_FILES[$inputName]['tmp_name'][$key], $targetDir . $newName)) {
                    $uploadedFiles[] = $newName; // Simpan nama file saja
                }
            }
        }
    }
    return $uploadedFiles;
}

// Upload baru
$new_docs = handleUpload('dokumentasi', '../uploads/dokumentasi/');
$new_abs  = handleUpload('absensi', '../uploads/absensi/');

// Gabungkan dengan file lama (jika ada input hidden 'existing_...')
// Decode JSON lama dulu jika perlu, atau terima array dari POST
$existing_docs = isset($_POST['existing_dokumentasi']) ? $_POST['existing_dokumentasi'] : [];
$existing_abs  = isset($_POST['existing_absensi']) ? $_POST['existing_absensi'] : [];

// Gabung & Encode ke JSON untuk Database
$final_docs_json = json_encode(array_merge($existing_docs, $new_docs));
$final_abs_json  = json_encode(array_merge($existing_abs, $new_abs));


// 4. Logika Simpan Database (INSERT / UPDATE)

// Jika ID Undangan masih 0, berarti ini Notulensi Baru Murni (belum ada Undangan).
// Kita harus buatkan record di tabel 'Undangan' dulu sebagai Induk Kegiatan.
if ($id_undangan == 0) {
    // Siapkan data default untuk Undangan Baru
    $nomor_surat_dummy = '-'; // Tanda strip jika tidak ada surat
    $perihal_dummy     = $nama_kegiatan;
    $kepada_dummy      = '-';
    $tanggal_surat_dummy = date('Y-m-d');

    // Gabung waktu
    $waktu_acara_dummy = $waktu_mulai . ' s.d ' . $waktu_selesai . ' WIB';

    // Query Insert ke Tabel Undangan
    $q_new_undangan = "INSERT INTO undangan 
              (nama_kegiatan, nomor_surat, perihal, kepada, tanggal_surat, 
               hari_tanggal_acara, waktu_acara, tempat_acara, agenda, undangan_pdf)
              VALUES 
              ('$nama_kegiatan', '$nomor_surat_dummy', '$perihal_dummy', '$kepada_dummy', '$tanggal_surat_dummy', 
               '$hari_tanggal', '$waktu_acara_dummy', '$tempat', '$isi_pembahasan', NULL)";

    if (mysqli_query($koneksi, $q_new_undangan)) {
        // Ambil ID yang baru dibuat
        $id_undangan = mysqli_insert_id($koneksi);
        $id_notulensi = $id_undangan; // Samakan ID Notulensi
    } else {
        echo "Error Start New Activity: " . mysqli_error($koneksi);
        exit;
    }
}

// Lanjut Proses Simpan Notulensi (dengan ID Undangan yang sudah pasti ada)
if ($id_undangan > 0) {
    // Cek apakah data sudah ada
    $cek = mysqli_query($koneksi, "SELECT id_n FROM notulensi WHERE id_n = '$id_undangan'");

    if (mysqli_num_rows($cek) > 0) {
        // === UPDATE ===
        $query = "UPDATE notulensi SET 
                  nama_kegiatan='$nama_kegiatan', unit_kerja='$unit_kerja', tanggal_rapat='$hari_tanggal',
                  waktu_mulai='$waktu_mulai', waktu_selesai='$waktu_selesai', tempat='$tempat',
                  pimpinan_rapat='$pimpinan', peserta='$peserta',
                  isi_pembukaan='$isi_pembukaan', isi_pembahasan='$isi_pembahasan', isi_kesimpulan='$isi_kesimpulan',
                  tempat_pembuatan='$ttd_tempat', tanggal_pembuatan='$ttd_tanggal', nama_notulis='$ttd_nama',
                  foto_dokumentasi='$final_docs_json', foto_absensi='$final_abs_json'
                  WHERE id_n='$id_undangan'";
    } else {
        // === INSERT ===
        $query = "INSERT INTO notulensi 
                  (id_n, nama_kegiatan, unit_kerja, tanggal_rapat, waktu_mulai, waktu_selesai, tempat, 
                   pimpinan_rapat, peserta, isi_pembukaan, isi_pembahasan, isi_kesimpulan, 
                   tempat_pembuatan, tanggal_pembuatan, nama_notulis, foto_dokumentasi, foto_absensi)
                  VALUES 
                  ('$id_undangan', '$nama_kegiatan', '$unit_kerja', '$hari_tanggal', '$waktu_mulai', '$waktu_selesai', '$tempat',
                   '$pimpinan', '$peserta', '$isi_pembukaan', '$isi_pembahasan', '$isi_kesimpulan',
                   '$ttd_tempat', '$ttd_tanggal', '$ttd_nama', '$final_docs_json', '$final_abs_json')";
    }

    if (mysqli_query($koneksi, $query)) {
        // 5. Sukses! Kembalikan ID Undangan/Notulensi
        echo $id_undangan;
    } else {
        echo "Error DB: " . mysqli_error($koneksi);
    }
} else {
    echo "Error: Gagal membuat ID Kegiatan baru.";
}

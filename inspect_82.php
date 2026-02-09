<?php
require_once 'koneksi.php';
$id = 82;
$q = mysqli_query($koneksi, "SELECT * FROM notulensi WHERE id_n = $id");
if ($row = mysqli_fetch_assoc($q)) {
    echo "ID: " . $row['id_n'] . "\n";
    echo "Foto Absensi: " . $row['foto_absensi'] . "\n";
    echo "Foto Dokumentasi: " . $row['foto_dokumentasi'] . "\n";
} else {
    echo "Data not found for ID $id";
}

<?php
require_once 'koneksi.php';

$id = 78;
$q = mysqli_query($koneksi, "SELECT * FROM undangan WHERE id_u = $id");
$row = mysqli_fetch_assoc($q);

echo "ID: " . $row['id_u'] . "<br>";
echo "Nama Kegiatan: " . $row['nama_kegiatan'] . "<br>";
echo "Perihal: " . $row['perihal'] . "<br>";

<?php
require_once 'koneksi.php';
$q = mysqli_query($koneksi, "SHOW CREATE VIEW view_semua_arsip");
if ($row = mysqli_fetch_array($q)) {
    echo $row[1];
} else {
    echo "Failed to get view definition: " . mysqli_error($koneksi);
}

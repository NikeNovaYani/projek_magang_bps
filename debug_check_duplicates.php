<?php
require 'koneksi.php';

echo "<h2>Check Duplicates in Undangan</h2>";
$name = "rapat 22";
// Check exact match or like
$query = "SELECT id_u, nama_kegiatan, created_at FROM undangan WHERE nama_kegiatan LIKE '%$name%'";
$result = mysqli_query($koneksi, $query);

if ($result) {
    echo "<table border='1'><tr><th>ID</th><th>Nama</th><th>Created At</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_u'] . "</td>";
        echo "<td>" . $row['nama_kegiatan'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Total: " . mysqli_num_rows($result) . "</p>";
} else {
    echo "Query failed: " . mysqli_error($koneksi);
}

echo "<h2>Check View Arsip</h2>";
$q2 = "SELECT * FROM view_semua_arsip WHERE nama_kegiatan LIKE '%$name%'";
$r2 = mysqli_query($koneksi, $q2);
if ($r2) {
    echo "<p>Total in View: " . mysqli_num_rows($r2) . "</p>";
}

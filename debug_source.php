<?php
require_once 'koneksi.php';

echo "<h2>Debug Source Column</h2>";
$q = mysqli_query($koneksi, "SELECT id_referensi, nama_kegiatan, sumber, link_undangan, notulensi_pdf FROM view_semua_arsip ORDER BY tanggal DESC LIMIT 5");
echo "<table border=1><tr><th>ID</th><th>Nama</th><th>Sumber</th><th>Link Undangan</th><th>Notulensi PDF</th></tr>";
while ($row = mysqli_fetch_assoc($q)) {
    echo "<tr>";
    echo "<td>" . $row['id_referensi'] . "</td>";
    echo "<td>" . $row['nama_kegiatan'] . "</td>";
    echo "<td>" . $row['sumber'] . "</td>";
    echo "<td>" . $row['link_undangan'] . "</td>";
    echo "<td>" . $row['notulensi_pdf'] . "</td>";
    echo "</tr>";
}
echo "</table>";

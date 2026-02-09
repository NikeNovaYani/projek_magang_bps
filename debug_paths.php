<?php
require_once 'koneksi.php';

echo "<h2>Debug Paths & File Existence</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>ID</th>
        <th>Nama Kegiatan</th>
        <th>DB: undangan_pdf</th>
        <th>DB: notulensi_pdf</th>
        <th>Constructed Path (Undangan)</th>
        <th>File Exists?</th>
        <th>Constructed Path (Notulensi)</th>
        <th>File Exists?</th>
      </tr>";

// Ambil 5 data terakhir dari view
$q = mysqli_query($koneksi, "SELECT * FROM view_semua_arsip ORDER BY tanggal DESC, id_referensi DESC LIMIT 5");

while ($row = mysqli_fetch_assoc($q)) {
    $id = $row['id_referensi'];
    $nama = $row['nama_kegiatan'];

    // Logic from arsip.php
    $db_link_u = $row['link_undangan']; // view column alias for undangan_pdf
    $db_link_n = $row['notulensi_pdf'];

    // Constructed Paths (Assumed 'arsip_pdf/' prefix in arsip.php)
    $path_u_relative = "arsip_pdf/" . $db_link_u;
    $path_u_absolute = __DIR__ . "/" . $path_u_relative;

    $path_n_relative = "arsip_pdf/" . $db_link_n;
    $path_n_absolute = __DIR__ . "/" . $path_n_relative;

    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td>" . htmlspecialchars($nama) . "</td>";
    echo "<td>'$db_link_u'</td>";
    echo "<td>'$db_link_n'</td>";

    echo "<td>$path_u_relative</td>";
    echo "<td>" . (file_exists($path_u_absolute) ? "✅ YES" : "❌ NO") . "</td>";

    echo "<td>$path_n_relative</td>";
    echo "<td>" . (file_exists($path_n_absolute) ? "✅ YES" : "❌ NO") . "</td>";
    echo "</tr>";
}
echo "</table>";

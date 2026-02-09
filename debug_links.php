<?php
require_once 'koneksi.php';

echo "<h2>Debug Links (New Logic)</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Sumber</th>
        <th>DB Link Undangan</th>
        <th>Generated Link U</th>
        <th>File U Exists?</th>
      </tr>";

$q = mysqli_query($koneksi, "SELECT * FROM view_semua_arsip ORDER BY tanggal DESC LIMIT 5");

while ($row = mysqli_fetch_assoc($q)) {
    $isManual = ($row['sumber'] == 'manual');

    // --- COPY PASTE LOGIC FROM ARSIP.PHP ---
    if ($isManual) {
        if (strpos($row['link_undangan'], 'arsip/') === 0) {
            $linkU = $row['link_undangan'];
        } else {
            $linkU = "arsip/" . $row['folder_path'] . "/undangan/" . $row['link_undangan'];
        }
    } else {
        $linkU = "arsip_pdf/" . $row['link_undangan'];
    }
    // ---------------------------------------

    $absPath = __DIR__ . "/" . $linkU;
    $exists = file_exists($absPath) ? "✅" : "❌";

    echo "<tr>";
    echo "<td>" . $row['id_referensi'] . "</td>";
    echo "<td>" . $row['nama_kegiatan'] . "</td>";
    echo "<td>" . $row['sumber'] . "</td>";
    echo "<td>" . $row['link_undangan'] . "</td>";
    echo "<td>" . $linkU . "</td>";
    echo "<td>" . $exists . "</td>";
    echo "</tr>";
}
echo "</table>";

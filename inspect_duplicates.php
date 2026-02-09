<?php
require_once 'koneksi.php';

echo "<h3>Undangan 'coba 5'</h3>";
// Get IDs from Undangan first
$ids = [];
$q = mysqli_query($koneksi, "SELECT * FROM undangan WHERE nama_kegiatan LIKE '%coba 5%' OR perihal LIKE '%coba 5%'");
while ($row = mysqli_fetch_assoc($q)) {
    $ids[] = $row['id_u']; // Assuming primary key is id_u
    echo "ID: " . $row['id_u'] . " | " . $row['nama_kegiatan'] . " | " . $row['created_at'] . "<br>";
}

if (!empty($ids)) {
    echo "<h3>Notulensi linked to above Undangan</h3>";
    $id_list = implode(',', $ids);
    // Notulensi usually has id_n which might be foreign key or primary key. 
    // In view definition: `n`.`id_n` = `u`.`id_u`
    // So distinct Notulensi likely shares ID with Undangan (One-to-One)? Or id_n is FK to id_u?
    // Let's check table columns via DESCRIBE if possible, or just select *

    $q2 = mysqli_query($koneksi, "SELECT * FROM notulensi WHERE id_n IN ($id_list)");
    while ($row = mysqli_fetch_assoc($q2)) {
        echo "ID_N: " . $row['id_n'] . " | PDF: " . ($row['notulensi_pdf'] ?? 'NULL') . "<br>";
    }
} else {
    echo "No Undangan found for 'coba 5'";
}

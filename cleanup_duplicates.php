<?php
require_once 'koneksi.php';

// Cleanup Plan for 'coba 7':
// 1. Identify IDs.
// 2. Keep the one with PDF or the latest one.
// 3. Delete others.

$q = mysqli_query($koneksi, "SELECT * FROM undangan WHERE nama_kegiatan LIKE '%coba 7%' OR perihal LIKE '%coba 7%' ORDER BY id_u DESC");
$ids = [];
while ($row = mysqli_fetch_assoc($q)) {
    $ids[] = $row;
}

if (count($ids) > 1) {
    echo "Found " . count($ids) . " entries for 'coba 7'.<br>";
    // Keep the FIRST one (latest) or the one with PDF?
    // Let's check Notulensi PDFs manually or just assume latest is best.
    // Usually the last one created is the one the user is working on.
    // BUT if the user clicked "Simpan" then "Cetak", the "Cetak" one (latest) might not have the PDF link in Undangan table yet (it's null in save_undangan), 
    // but Generate PDF updates it? 
    // Actually Generate PDF updates `undangan_pdf` column.

    // Strategy: Prefer entry with `undangan_pdf` NOT NULL.
    $keep_id = null;
    foreach ($ids as $row) {
        if (!empty($row['undangan_pdf'])) {
            $keep_id = $row['id_u'];
            break;
        }
    }

    // If no PDF found, keep the LATEST one ($ids[0])
    if (!$keep_id && !empty($ids)) {
        $keep_id = $ids[0]['id_u'];
    }

    echo "Keeping ID: $keep_id<br>";

    $delete_ids = [];
    foreach ($ids as $row) {
        if ($row['id_u'] != $keep_id) {
            $delete_ids[] = $row['id_u'];
        }
    }

    if (!empty($delete_ids)) {
        $id_str = implode(',', $delete_ids);

        // Delete Notulensi for these IDs
        mysqli_query($koneksi, "DELETE FROM notulensi WHERE id_n IN (SELECT id_u FROM undangan WHERE id_u IN ($id_str))"); // Checking relation
        // Actually Notulensi ID often matches Undangan ID in this system (from view definition)
        mysqli_query($koneksi, "DELETE FROM notulensi WHERE id_n IN ($id_str)");

        // Delete Undangan
        mysqli_query($koneksi, "DELETE FROM undangan WHERE id_u IN ($id_str)");

        echo "Deleted IDs: $id_str<br>";
    }
} else {
    echo "No duplicates found for 'coba 7'.<br>";
}

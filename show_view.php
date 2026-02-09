<?php
require_once 'koneksi.php';

$q = mysqli_query($koneksi, "SHOW CREATE VIEW view_semua_arsip");
$row = mysqli_fetch_assoc($q);
echo "<pre>" . htmlspecialchars($row['Create View']) . "</pre>";

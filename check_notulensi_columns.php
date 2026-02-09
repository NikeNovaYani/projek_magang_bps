<?php
require_once 'koneksi.php';
$q = mysqli_query($koneksi, "SELECT * FROM notulensi LIMIT 1");
if ($row = mysqli_fetch_assoc($q)) {
    print_r(array_keys($row));
} else {
    echo "Table empty, showing fields from SHOW COLUMNS:";
    $q2 = mysqli_query($koneksi, "SHOW COLUMNS FROM notulensi");
    while ($col = mysqli_fetch_assoc($q2)) {
        echo $col['Field'] . "\n";
    }
}

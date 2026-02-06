<?php
$host     = "localhost";
$username = "root";       // Default XAMPP/Laragon
$password = "";           // Default biasanya kosong
$database = "database_rapat";   // Nama database yang tadi dibuat

// Melakukan koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek jika koneksi gagal
if (!$koneksi) {
    die("<h3>Koneksi Gagal: </h3>" . mysqli_connect_error());
}

// Opsional: Set timezone ke WIB
date_default_timezone_set('Asia/Jakarta');
?>
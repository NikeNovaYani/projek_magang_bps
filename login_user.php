<?php
session_start(); 
include "koneksi.php";

// Pastikan data dikirim melalui metode POST dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 1. Cari user di database menggunakan Prepared Statement (lebih aman)
    $stmt = mysqli_prepare($koneksi, "SELECT username, password FROM login WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    // 2. Cek apakah user ditemukan
    if ($data) {
        // 3. Verifikasi password hash 🛡️
        if (password_verify($pass, $data['password'])) {
            // Jika cocok, simpan identitas di Session
            $_SESSION['status_login'] = true;
            $_SESSION['username'] = $data['username'];
            
            // Pindah ke halaman utama
            header("Location: index.php");
            exit(); 
        } else {
            echo "<script>alert('Password salah!'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location='login.php';</script>";
    }
}
?>
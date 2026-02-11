<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Login</title>
    <!-- Library SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Library Animate.css untuk animasi popup -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7fb; }</style>
</head>
<body>

<?php
session_start(); 
include "koneksi.php";

// Pastikan data dikirim melalui metode POST dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 1. Cari user di database (Struktur asli tetap dipertahankan)
    // Menggunakan tabel 'login' sesuai kode asli Anda
    $stmt = mysqli_prepare($koneksi, "SELECT username, password FROM login WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    // 2. Cek apakah user ditemukan
    if ($data) {
        // 3. Verifikasi password hash
        if (password_verify($pass, $data['password'])) {
            // Jika cocok, simpan identitas di Session
            $_SESSION['status_login'] = true;
            $_SESSION['username'] = $data['username'];
            
            // ALERT SUKSES (Kreatif dengan Animasi Fade Down)
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: 'Selamat datang kembali, " . htmlspecialchars($data['username']) . ".',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#fff',
                    iconColor: '#4caf50',
                    backdrop: `
                        rgba(0,0,123,0.1)
                        left top
                        no-repeat
                    `,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                }).then(() => {
                    window.location = 'index.php';
                });
            </script>";
            exit(); 
        } else {
            // ALERT PASSWORD SALAH (Kreatif dengan Animasi Shake/Getar)
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak',
                    html: 'Password yang Anda masukkan <b>salah</b>.<br>Silakan coba lagi.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Coba Lagi',
                    showClass: {
                        popup: 'animate__animated animate__shakeX'
                    }
                }).then(() => {
                    window.location = 'login.php';
                });
            </script>";
        }
    } else {
        // ALERT USERNAME TIDAK DITEMUKAN (Kreatif dengan Icon Pertanyaan)
        echo "<script>
            Swal.fire({
                icon: 'question',
                title: 'Akun Tidak Ditemukan',
                text: 'Username tersebut belum terdaftar di sistem kami.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Periksa Kembali',
                footer: '<a href=\"#\" style=\"text-decoration:none; color:#777;\">Hubungi admin jika bermasalah</a>',
                showClass: {
                    popup: 'animate__animated animate__pulse'
                }
            }).then(() => {
                window.location = 'login.php';
            });
        </script>";
    }
}
?>

</body>
</html>
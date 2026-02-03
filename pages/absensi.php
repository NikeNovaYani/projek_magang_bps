<?php
// absensi.php - Halaman Absensi Peserta Rapat
$page = 'absensi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Peserta Rapat - Sistem Rapat BPS Kota Depok</title>
    <!-- Menambahkan Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            color: #0d47a1;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar { /* kotak navigasi */
    width: 280px;
    height: 100vh;
    background-color: #ffffff;
    box-shadow: 5px 0 15px rgba(27, 110, 235, 0.1);
    padding: 20px 0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}
.sidebar h2 { /*judul navigasi */
    text-align: center;
    color: #1976d2;
    margin-bottom: 30px;
    font-size: 28px;
    font-weight: 700;
    position: relative;
}
.sidebar h2:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: #1976d2;
    border-radius: 3px;
}
.sidebar ul { /* kotak isi */
    list-style: none;
    padding: 0; /* kotak teks */
    margin: 0;
}
.sidebar li { /* jarak per item */
    margin: 5px 0;
}
.sidebar a { /* teks sama choice */
    display: flex;
    align-items: center;
    padding: 15px 25px;
    color: #1e70ebff;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 16px;
    position: relative;
    overflow: hidden;
}
.sidebar a i {
    margin-right: 15px;
    width: 20px;
    text-align: center;
}
.sidebar a:hover, .sidebar a.active {
    background-color: #e3f2fd;
    color: #0d47a1;
    transform: translateX(5px);
}
.sidebar a.active {
    border-left: 4px solid #1976d2;
    font-weight: 600;
}
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            margin-left: 250px;
        }
        .content {
            max-width: 1200px;
            margin: 10px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(13, 71, 161, 0.2);
        }
        h1 {
            text-align: center;
            color: #1976d2;
            margin-bottom: 30px;
        }
        p {
            text-align: center;
            color: #0d47a1;
        }
        @media (max-width:768px){
            .container{
                flex-direction:column;
            }
            .sidebar{
                width:100%;
                order:-1;
            }
            .main-content{
                margin-left:0;
            }
        }
        @media print {
            .sidebar {
                display: none;
            }
            .container {
                display: block;
            }
            .main-content {
                padding: 0;
                background: white;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>UANG</h2>
            <ul>
                <li><a href="index.php?page=beranda"><i class="fas fa-home"></i>Beranda</a></li>
                <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i>Undangan</a></li>
                <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i>Notulensi</a></li>
                <li><a href="index.php?page=absensi" class="active"><i class="fas fa-user-check"></i>Absensi</a></li>
                <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i>Arsip</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="content">
                <h1>Absensi Peserta Rapat</h1>
                <p>Halaman untuk mengelola absensi peserta rapat akan segera tersedia.</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// home.php - Konten halaman beranda
 $page = 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Beranda - Sistem Rapat BPS Kota Depok</title>
<!-- Menambahkan Font Awesome untuk ikon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ===== RESET ===== */
*{
  box-sizing: border-box;
  font-family: "Arial", serif;
}

body{
  margin:0;
  background: linear-gradient(135deg, #f5f9ff 0%, #e3f2fd 100%);
  color:#0d47a1;
}

/* ===== CONTAINER ===== */
.container{
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
    padding: 30px;
    overflow-y: auto;
    margin-left: 140px;
}

/* ===== HEADER ===== */
.header{
  text-align:center;
  margin-bottom:50px;
  padding: 20px 0;
  position: relative;
}

.header h1{
  margin:0;
  font-size:2.5em;
  color:#0d47a1;
  font-weight:700;
  position: relative;
  display: inline-block;
}

.header h1:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #1976d2, transparent);
    border-radius: 3px;
}

.header p{
  margin-top:20px;
  font-size:1.2em;
  color:#1565c0;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

/* ===== FEATURES ===== */
.features{
  display:flex;
  gap:25px;
  justify-content:space-between;
  flex-wrap:wrap;
  margin-top: 40px;
}

/* ===== CARD ===== */
.feature{
  flex:1;
  min-width:230px;
  background:#ffffff;
  border-radius:15px;
  padding:30px 25px;
  text-align:center;
  transition:all 0.3s ease;
  box-shadow: 0 5px 15px rgba(13,71,161,0.08);
  position: relative;
  overflow: hidden;
}

.feature:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #1976d2, #64b5f6);
    border-radius: 15px 15px 0 0;
}

.feature:hover{
  transform:translateY(-10px);
  box-shadow:0 15px 30px rgba(13,71,161,0.15);
}

.feature-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #1976d2, #64b5f6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 28px;
    transition: all 0.3s ease;
}

.feature:hover .feature-icon {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
}

.feature h2{
  font-size:1.25em;
  margin-bottom:15px;
  color:#1565c0;
  font-weight:600;
}

.feature p {
    color: #546e7a;
    margin-bottom: 20px;
    font-size: 0.95em;
    line-height: 1.5;
}

/* ===== BUTTON ===== */
.feature a{
  display:inline-block;
  padding:12px 24px;
  background: linear-gradient(135deg, #1976d2, #2196f3);
  color:#fff;
  text-decoration:none;
  border-radius:25px;
  font-size:15px;
  font-weight: 500;
  transition:all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.feature a:hover{
  background: linear-gradient(135deg, #0d47a1, #1976d2);
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
}

/* ===== WELCOME ANIMATION ===== */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.header, .feature {
    animation: fadeIn 0.8s ease-out;
}

.feature:nth-child(2) {
    animation-delay: 0.1s;
}

.feature:nth-child(3) {
    animation-delay: 0.2s;
}

.feature:nth-child(4) {
    animation-delay: 0.3s;
}

/* ===== RESPONSIVE ===== */
@media (max-width:768px){
  .container{
    flex-direction:column;
  }
  .sidebar{
    width:100%;
    order:-1;
    height: auto;
    position: relative;
  }
  .main-content{
    margin-left:0;
  }
  .features{
    justify-content:space-around;
  }
  .feature{
    flex:0 0 48%;
    min-width:180px;
  }
}

@media (max-width:600px){
  .feature{
    flex:0 0 100%;
    min-width:160px;
  }
  .header h1 {
    font-size: 2em;
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
  .feature {
    break-inside: avoid;
  }
}
</style>
</head>

<body>

<div class="container">
  <div class="sidebar">
    <h2>UANG</h2>
    <ul>
      <li><a href="index.php?page=beranda" class="active"><i class="fas fa-home"></i> Beranda</a></li>
      <li><a href="index.php?page=undangan"><i class="fas fa-envelope"></i> Undangan</a></li>
      <li><a href="index.php?page=notulensi"><i class="fas fa-file-alt"></i> Notulensi</a></li>
      <li><a href="index.php?page=absensi"><i class="fas fa-user-check"></i> Absensi</a></li>
      <li><a href="index.php?page=arsip"><i class="fas fa-archive"></i> Arsip</a></li>
      <li style="position: absolute; bottom: 0px; right: 0px; left: 0px;"><a href="index.php?page=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>
  <div class="main-content">

    <!-- HEADER -->
    <div class="header">
      <h1>Selamat Datang di Sistem Rapat BPS Kota Depok</h1>
      <p>Kelola undangan, notulensi, absensi, dan arsip rapat secara terintegrasi dengan mudah dan efisien</p>
    </div>

    <!-- FEATURES -->
    <div class="features">

      <div class="feature">
        <div class="feature-icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <h2>Template Undangan Rapat</h2>
        <p>Buat undangan rapat profesional dengan template yang telah disediakan</p>
        <a href="index.php?page=undangan">Buat Undangan</a>
      </div>

      <div class="feature">
        <div class="feature-icon">
          <i class="fas fa-file-contract"></i>
        </div>
        <h2>Template Notulensi Rapat</h2>
        <p>Dokumentasikan hasil rapat dengan notulensi yang terstruktur</p>
        <a href="index.php?page=notulensi">Buat Notulen</a>
      </div>

      <div class="feature">
        <div class="feature-icon">
          <i class="fas fa-users"></i>
        </div>
        <h2>Absensi Peserta Rapat</h2>
        <p>Pantau kehadiran peserta rapat dengan sistem absensi digital</p>
        <a href="index.php?page=absensi">Lihat Absensi</a>
      </div>

      <div class="feature">
        <div class="feature-icon">
          <i class="fas fa-folder-open"></i>
        </div>
        <h2>Arsip Rapat</h2>
        <p>Simpan dan kelola semua dokumen rapat dalam satu tempat terpusat</p>
        <a href="index.php?page=arsip">Lihat Arsip</a>
      </div>

    </div>

  </div>
</div>

</body>
</html>
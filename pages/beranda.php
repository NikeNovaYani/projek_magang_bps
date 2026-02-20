<?php
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
  header("Location: ../index.php?page=beranda");
  exit();
}
?>
<style>
  .header {
    text-align: center;
    margin-bottom: 50px;
    padding: 20px 0;
    position: relative;
  }

  .header h1 {
    margin: 0;
    font-size: 2.5em;
    color: #0d47a1;
    font-weight: 700;
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

  .header p {
    margin-top: 20px;
    font-size: 1.2em;
    color: #1565c0;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
  }

  .features {
    display: flex;
    gap: 25px;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-top: 40px;
  }

  .feature {
    flex: 1;
    min-width: 230px;
    background: #ffffff;
    border-radius: 15px;
    padding: 30px 25px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(13, 71, 161, 0.08);
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
    background: linear-gradient(90deg, #1976d2, #b5d2f3ff);
    border-radius: 15px 15px 0 0;
  }

  .feature:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(13, 71, 161, 0.15);
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

  .feature h2 {
    font-size: 1.25em;
    margin-bottom: 15px;
    color: #1565c0;
    font-weight: 600;
  }

  .feature p {
    color: #546e7a;
    margin-bottom: 20px;
    font-size: 0.95em;
    line-height: 1.5;
  }

  .feature a {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #1976d2, #2196f3);
    color: #fff;
    text-decoration: none;
    border-radius: 25px;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .feature a:hover {
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .header,
  .feature {
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

  @media (max-width:768px) {
    .features {
      justify-content: space-around;
    }

    .feature {
      flex: 0 0 48%;
      min-width: 180px;
    }
  }

  @media (max-width:600px) {
    .feature {
      flex: 0 0 100%;
      min-width: 160px;
    }

    .header h1 {
      font-size: 2em;
    }
  }

  .cta-hero {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    border-radius: 15px;
    padding: 40px;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
    box-shadow: 0 10px 20px rgba(25, 118, 210, 0.2);
    position: relative;
    overflow: hidden;
  }

  .cta-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }

  .cta-content {
    flex: 1;
    padding-right: 20px;
    position: relative;
    z-index: 1;
  }

  .cta-content h2 {
    margin: 0 0 10px 0;
    font-size: 2em;
    font-weight: 700;
  }

  .cta-content p {
    margin: 0 0 25px 0;
    font-size: 1.1em;
    opacity: 0.9;
    max-width: 600px;
  }

  .cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: white;
    color: #1976d2;
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    background: #f0f7ff;
  }

  .cta-icon {
    font-size: 80px;
    color: rgba(255, 255, 255, 0.2);
    padding-right: 40px;
  }

  @media (max-width: 768px) {
    .cta-hero {
      flex-direction: column;
      text-align: center;
      padding: 30px 20px;
    }

    .cta-content {
      padding-right: 0;
      margin-bottom: 20px;
    }

    .cta-icon {
      display: none;
    }
  }
</style>

<!-- TAMPILAN TEKS ATAS SELAMAT DATANG -->
<div class="header">
  <h1>Selamat Datang di <span style="font-style: italic; font-weight: 700; color: #1976d2;">SI UANG</span></h1>
  <p> <b>Undangan, Absensi, Notulensi, dan Gudang Rapat</b>.</p>
</div>

<div class="cta-hero">
  <div class="cta-content">
    <h2>Mulai Rapat Baru</h2>
    <p>Buat arsip rapat baru, siapkan undangan, notulensi, dan absensi dengan mudah dalam satu langkah terintegrasi.</p>
    <a href="index.php?page=undangan" class="cta-btn">
      <i class="fas fa-plus-circle"></i> Buat Sekarang
    </a>
  </div>
  <div class="cta-icon">
    <i class="fas fa-calendar-plus"></i>
  </div>
</div>

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
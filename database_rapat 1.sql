-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Feb 2026 pada 09.05
-- Versi server: 10.4.13-MariaDB
-- Versi PHP: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_rapat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `arsip_manual`
--

CREATE TABLE `arsip_manual` (
  `id_am` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `file_undangan` varchar(255) DEFAULT NULL,
  `file_notulensi` varchar(255) DEFAULT NULL,
  `file_absensi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `arsip_manual`
--

INSERT INTO `arsip_manual` (`id_am`, `nama_kegiatan`, `file_undangan`, `file_notulensi`, `file_absensi`, `created_at`) VALUES
(10, 'rapat manual 1', 'arsip/2026-02-11_rapat_manual_1/undangan/3276_Undangan Tim Rekrutmen mitra 2025 -persiapan seleksi akhir.docx', 'arsip/2026-02-11_rapat_manual_1/notulensi/20250523_Notulensi Rapat Tim.pdf', 'arsip/2026-02-11_rapat_manual_1/absensi/good-things-are-coming-inspirational-quote-hhqw7k10l7es407c.jpg', '2026-02-11 07:29:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login`
--

CREATE TABLE `login` (
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `login`
--

INSERT INTO `login` (`username`, `password`) VALUES
('BPS DEPOK', '$2y$10$7mm6jCP0wEatiwhhBALXXuvWw4tZwS8y98x3ig0gWzw1/W68vTmAi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notulensi`
--

CREATE TABLE `notulensi` (
  `id_u` int(11) NOT NULL,
  `id_n` int(11) DEFAULT NULL,
  `nama_kegiatan` varchar(255) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `tanggal_rapat` date DEFAULT NULL,
  `waktu_mulai` varchar(20) DEFAULT NULL,
  `waktu_selesai` varchar(20) DEFAULT NULL,
  `tempat` text DEFAULT NULL,
  `pimpinan_rapat` varchar(100) DEFAULT NULL,
  `topik` text DEFAULT NULL,
  `lampiran_ket` varchar(255) DEFAULT NULL,
  `peserta` text DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `isi_pembukaan` longtext DEFAULT NULL,
  `isi_pembahasan` longtext DEFAULT NULL,
  `isi_kesimpulan` longtext DEFAULT NULL,
  `tempat_pembuatan` varchar(100) DEFAULT NULL,
  `tanggal_pembuatan` date DEFAULT NULL,
  `nama_notulis` varchar(100) DEFAULT NULL,
  `foto_dokumentasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`foto_dokumentasi`)),
  `foto_absensi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`foto_absensi`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notulensi_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `notulensi`
--

INSERT INTO `notulensi` (`id_u`, `id_n`, `nama_kegiatan`, `unit_kerja`, `tanggal_rapat`, `waktu_mulai`, `waktu_selesai`, `tempat`, `pimpinan_rapat`, `topik`, `lampiran_ket`, `peserta`, `agenda`, `isi_pembukaan`, `isi_pembahasan`, `isi_kesimpulan`, `tempat_pembuatan`, `tanggal_pembuatan`, `nama_notulis`, `foto_dokumentasi`, `foto_absensi`, `created_at`, `notulensi_pdf`) VALUES
(50, NULL, 'coba 6', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-09', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', NULL, NULL, 'Sebagaimana Terlampir', NULL, '<p>Silakan isi pembukaan rapat di sini.</p>', '<p>Silakan isi pembahasan dan diskusi di sini.</p>', '', 'Depok', '2026-02-09', '', '[\"dokumentasi_69896decba318.png\"]', '[\"absensi_69896decba590.png\"]', '2026-02-09 05:17:30', '2024-11-11_coba_6/Notulensi_76_coba_6.pdf'),
(54, NULL, 'coba 8', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-09', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', NULL, NULL, 'Sebagaimana Terlampir', NULL, '<p>Silakan isi pembukaan rapat di sini.</p>', '<p>Silakan isi pembahasan dan diskusi di sini.</p>', '', 'Depok', '2026-02-09', '', '[\"dokumentasi_69897a01ed28e.jpeg\",\"dokumentasi_698980b4e80a1.jpeg\"]', '[\"absensi_69897a01ed5f5.jpeg\",\"absensi_698980b4e842f.jpeg\"]', '2026-02-09 06:09:04', '2024-11-11_coba_8/Notulensi_81_coba_8.pdf'),
(55, NULL, 'coba lagi', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2024-11-11', '09:00', 'Selesai', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', NULL, NULL, 'Sebagaimana Terlampir', NULL, '<p>Silakan isi pembukaan rapat di sini.</p>', '<p>Silakan isi pembahasan dan diskusi di sini.</p>', '', 'Depok', '2026-02-09', '', '[\"dokumentasi_69897db5117c8.png\"]', '[\"absensi_69897db511a50.png\"]', '2026-02-09 06:24:51', '2024-11-11_coba_lagi/Notulensi_82_coba_lagi.pdf'),
(66, 99, 'Rapat Susenas 2026', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok 2026', '2026-02-24', '13:00', 'Selesai', 'Ruang Rapat BPS Kota Depok ', 'Satriana Yasmuarto, S.Si, MM ', 'Undangan Pembahasan Optimalisasi Anggaran Perjadin 2026', NULL, 'Sebagaimana Terlampir', NULL, '<p>Silakan isi pembukaan rapat di sini.</p>', '<p><strong>Silakan isi pembahasan dan diskusi di sini.</strong></p>', '<ul class=\"checklist\">\r\n<li>kenapa saat kllik buat undangan dari halaman arsip di folder Rapat 1, tampilan pada undangan di kolom&nbsp;</li>\r\n<li>kenapa saat kllik buat undangan dari halaman arsip di folder Rapat 1, tampilan pada undangan di kolom agenda terisi otomatis dengan isi kolom pembahasan dan diskusi, seharusnya kolom agenda default dan tidak mengikuti kolom pembahasan pada notulensinya</li>\r\n<li>kenapa saat kllik buat undangan dari halaman arsip di folder Rapat 1, tampilan pada undangan di kolom agenda terisi otomatis dengan isi kolom pembahasan dan diskusi, seharusnya kolom agenda default dan tidak mengikuti kolom pembahasan pada notulensinya</li>\r\n</ul>', 'Depok', '2026-02-11', 'Nurine Kristy', '[\"dokumentasi_698c2b9fe5ba3.jpg\"]', '[\"absensi_698c2b9fe5dbb.jpg\"]', '2026-02-11 07:11:27', '2026-02-24_Rapat_Susenas_2026/Notulensi_99_Undangan_Pembahasan_Optimalisasi_Anggaran_Perjadin_2026.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pejabat`
--

CREATE TABLE `pejabat` (
  `id` int(11) NOT NULL,
  `nama_kepala` varchar(100) NOT NULL,
  `file_stempel_ttd` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pejabat`
--

INSERT INTO `pejabat` (`id`, `nama_kepala`, `file_stempel_ttd`) VALUES
(1, 'Agus Marzuki Prihantoro', 'ttd1.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `undangan`
--

CREATE TABLE `undangan` (
  `id_u` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `nomor_surat` varchar(100) DEFAULT NULL,
  `sifat` varchar(50) DEFAULT NULL,
  `lampiran` varchar(100) DEFAULT NULL,
  `perihal` text DEFAULT NULL,
  `tanggal_surat` date DEFAULT NULL,
  `kepada` text DEFAULT NULL,
  `isi_undangan` text DEFAULT NULL,
  `hari_tanggal_acara` date DEFAULT NULL,
  `waktu_acara` varchar(50) DEFAULT NULL,
  `tempat_acara` text DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `id_pejabat` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `undangan_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `undangan`
--

INSERT INTO `undangan` (`id_u`, `nama_kegiatan`, `nomor_surat`, `sifat`, `lampiran`, `perihal`, `tanggal_surat`, `kepada`, `isi_undangan`, `hari_tanggal_acara`, `waktu_acara`, `tempat_acara`, `agenda`, `id_pejabat`, `created_at`, `updated_at`, `undangan_pdf`) VALUES
(99, 'Rapat Susenas 2026', 'B-32766/32766/BPS/2026', 'Biasa', '-', 'Undangan Pembahasan Optimalisasi Anggaran Perjadin 2026', '2026-02-12', '1. Seluruh Ketua Tim BPS Kota Depok\r\n2. PPK BPS Kota Depok\r\n3. Pegawai', 'Sehubungan dengan menjelang akan berakhirnya tahun anggaran 2025, Kepala BPS Kota Depok mengundang seluruh Ketua Tim dan PPK BPS Kota Depok untuk hadir dalam rapat yang akan diselenggarakan pada', '2026-02-24', '09:00 s.d Selesai WIB', 'Ruang Rapat BPS Kota Depok ', 'Pembahasan Optimalisasi Anggaran 2026', 1, '2026-02-11 07:08:25', '2026-02-11 07:08:37', '2026-02-24_Rapat_Susenas_2026/Undangan_99_Rapat_Susenas_2026.pdf');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_semua_arsip`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_semua_arsip` (
`sumber` varchar(8)
,`id_referensi` int(11)
,`nama_kegiatan` varchar(255)
,`tanggal` date
,`folder_path` varchar(266)
,`link_undangan` varchar(255)
,`link_notulensi` varchar(255)
,`link_absensi` longtext
,`ada_undangan` int(1)
,`ada_notulensi` int(1)
,`ada_absensi` int(1)
,`notulensi_pdf` varchar(255)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `view_semua_arsip`
--
DROP TABLE IF EXISTS `view_semua_arsip`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_semua_arsip`  AS  select 'manual' AS `sumber`,`arsip_manual`.`id_am` AS `id_referensi`,`arsip_manual`.`nama_kegiatan` AS `nama_kegiatan`,cast(`arsip_manual`.`created_at` as date) AS `tanggal`,concat(cast(`arsip_manual`.`created_at` as date),'_',replace(`arsip_manual`.`nama_kegiatan`,' ','_')) AS `folder_path`,`arsip_manual`.`file_undangan` AS `link_undangan`,`arsip_manual`.`file_notulensi` AS `link_notulensi`,`arsip_manual`.`file_absensi` AS `link_absensi`,case when `arsip_manual`.`file_undangan` is not null then 1 else 0 end AS `ada_undangan`,case when `arsip_manual`.`file_notulensi` is not null then 1 else 0 end AS `ada_notulensi`,case when `arsip_manual`.`file_absensi` is not null then 1 else 0 end AS `ada_absensi`,NULL AS `notulensi_pdf` from `arsip_manual` union all select 'otomatis' AS `sumber`,`u`.`id_u` AS `id_referensi`,`u`.`nama_kegiatan` AS `nama_kegiatan`,`u`.`hari_tanggal_acara` AS `tanggal`,NULL AS `folder_path`,`u`.`undangan_pdf` AS `link_undangan`,(select `n`.`notulensi_pdf` from `notulensi` `n` where `n`.`id_n` = `u`.`id_u` limit 1) AS `link_notulensi`,(select `notulensi`.`foto_absensi` from `notulensi` where `notulensi`.`id_n` = `u`.`id_u`) AS `link_absensi`,case when `u`.`undangan_pdf` is not null then 1 else 0 end AS `ada_undangan`,ifnull((select case when (`n`.`notulensi_pdf` is not null and `n`.`notulensi_pdf` <> '') then 1 else 0 end from `notulensi` `n` where `n`.`id_n` = `u`.`id_u` limit 1),0) AS `ada_notulensi`,ifnull((select case when (`n2`.`foto_absensi` is not null and `n2`.`foto_absensi` <> '[]') then 1 else 0 end from `notulensi` `n2` where `n2`.`id_n` = `u`.`id_u` limit 1),0) AS `ada_absensi`,(select `n3`.`notulensi_pdf` from `notulensi` `n3` where `n3`.`id_n` = `u`.`id_u` limit 1) AS `notulensi_pdf` from `undangan` `u` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `arsip_manual`
--
ALTER TABLE `arsip_manual`
  ADD PRIMARY KEY (`id_am`);

--
-- Indeks untuk tabel `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`username`);

--
-- Indeks untuk tabel `notulensi`
--
ALTER TABLE `notulensi`
  ADD PRIMARY KEY (`id_u`),
  ADD KEY `id_n` (`id_n`);

--
-- Indeks untuk tabel `pejabat`
--
ALTER TABLE `pejabat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `undangan`
--
ALTER TABLE `undangan`
  ADD PRIMARY KEY (`id_u`),
  ADD KEY `id_pejabat` (`id_pejabat`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `arsip_manual`
--
ALTER TABLE `arsip_manual`
  MODIFY `id_am` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `notulensi`
--
ALTER TABLE `notulensi`
  MODIFY `id_u` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT untuk tabel `pejabat`
--
ALTER TABLE `pejabat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `undangan`
--
ALTER TABLE `undangan`
  MODIFY `id_u` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `notulensi`
--
ALTER TABLE `notulensi`
  ADD CONSTRAINT `notulensi_ibfk_1` FOREIGN KEY (`id_n`) REFERENCES `undangan` (`id_u`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `undangan`
--
ALTER TABLE `undangan`
  ADD CONSTRAINT `undangan_ibfk_1` FOREIGN KEY (`id_pejabat`) REFERENCES `pejabat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

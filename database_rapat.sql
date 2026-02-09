-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 01:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `arsip_manual`
--

CREATE TABLE `arsip_manual` (
  `id_am` int(11) NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `file_undangan` varchar(255) DEFAULT NULL,
  `file_notulensi` varchar(255) DEFAULT NULL,
  `file_absensi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arsip_manual`
--

INSERT INTO `arsip_manual` (`id_am`, `nama_kegiatan`, `file_undangan`, `file_notulensi`, `file_absensi`, `created_at`) VALUES
(2, 'Rapat Sensus', 'arsip/2026-02-06_Rapat_Sensus/undangan/Undangan_Rapat (9).pdf', 'arsip/2026-02-06_Rapat_Sensus/notulensi/Notulensi_Rapat (3).pdf', 'arsip/2026-02-06_Rapat_Sensus/absensi/Screenshot 2025-06-23 151734.png', '2026-02-06 08:55:46'),
(3, 'Rapat Susenas 2026', 'arsip/2026-02-06_Rapat_Susenas_2026/undangan/Undangan_Rapat (10).pdf', 'arsip/2026-02-06_Rapat_Susenas_2026/notulensi/Notulensi_Rapat (3).pdf', 'arsip/2026-02-06_Rapat_Susenas_2026/absensi/WhatsApp Image 2026-02-06 at 09.26.54.jpeg', '2026-02-06 09:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `notulensi`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notulensi`
--

INSERT INTO `notulensi` (`id_u`, `id_n`, `nama_kegiatan`, `unit_kerja`, `tanggal_rapat`, `waktu_mulai`, `waktu_selesai`, `tempat`, `pimpinan_rapat`, `topik`, `lampiran_ket`, `peserta`, `agenda`, `isi_pembukaan`, `isi_pembahasan`, `isi_kesimpulan`, `tempat_pembuatan`, `tanggal_pembuatan`, `nama_notulis`, `foto_dokumentasi`, `foto_absensi`, `created_at`) VALUES
(1, NULL, 'tim susenas 2026', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-10', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', 'Rapat Tim Kegiatan Pembinaan Desa Cantik 2026', '1. Dokumentasi\r\n2. Daftar Hadir', 'Sebagaimana Terlampir', '✓ Pembukaan\r\n✓ Pembahasan dan Diskusi\r\n✓ Kesimpulan dan Tindak Lanjut', '<p class=\"first-line-indent\">Rapat dimulai dengan sambutan pembukaan dari Bapak Satriana Yasmuarto, yang menyampaikan tujuan rapat sebagai bagian dari persiapan pelaksanaan program Desa Cinta Statistik (Desa Cantik) di Kelurahan Ratu Jaya, Kecamatan Cipayung, Kota Depok. Beliau<br>menekankan pentingnya sinergi antar lembaga dalam mendukung keberhasilan program ini dan mengajak seluruh peserta untuk berkomitmen menjalankan tugas-tugas yang telah ditetapkan.&nbsp;</p>', '<div><strong>a. Rancangan Kegiatan dan Jadwal Program Desa Cantik</strong></div>\r\n<div>Dalam sesi ini, dibahas rancangan kegiatan serta timeline pembinaan Agen Statistik dan program Desa Cantik secara keseluruhan. Beberapa poin penting yang dibahas meliputi:</div>\r\n<div>&nbsp;</div>\r\n<div><span style=\"text-decoration: underline;\">Jadwal Kegiatan Utama:</span></div>\r\n<div>Sosialisasi Program Desa Cinta Statistik (Desa Cantik)</div>\r\n<div>Tanggal: Selasa, 27 Mei 2025</div>\r\n<div>Peserta: Dinas-dinas terkait (Diskominfo, Disdukcapil, DP3AP2KB),Pemkot Depok, Pemerintah Kecamatan Cipayung, serta Pemerintah Kelurahan Ratu Jaya.</div>\r\n<div>&nbsp;</div>\r\n<div>Tujuan: Memperkenalkan program kepada stakeholders terkait dan membangun komitmen bersama dalam pelaksanaannya.</div>\r\n<div>Pembinaan Agen Statistik Ke-1.</div>\r\n<div>&nbsp;</div>\r\n<div>Tanggal: Senin, 26 Mei 2025</div>\r\n<div>Fokus: Pelatihan dasar bagi Agen Statistik Kelurahan Ratu Jaya, termasuk pengenalan data statistik desa, penyusunan produk statistik, dan pengelolaan website desa.</div>\r\n<div>&nbsp;</div>\r\n<div>Periode Persiapan: 23 Mei &ndash; 26 Mei 2025 (penyusunan materi, undangan, dan dokumen pendukung).</div>\r\n<div>Periode Implementasi: 27 Mei &ndash; 31 Mei 2025 (pelaksanaan sosialisasi dan pembinaan pertama).</div>\r\n<div>&nbsp;</div>\r\n<div><strong>b. Pembagian Tugas</strong></div>\r\n<div>Untuk memastikan kelancaran pelaksanaan program, dilakukan pembagian tugas sebagai berikut:</div>\r\n<div>&nbsp;</div>\r\n<div>Persiapan Sosialisasi Program Desa Cinta Statistik (27 Mei 2025):</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan materi presentasi tentang program Desa Cantik dan manfaatnya bagi desa.</div>\r\n<div>&nbsp;</div>\r\n<div>Menghubungi Dinas-dinas terkait (Diskominfo, Disdukcapil, DP3AP2KB) untuk mengundang mereka sebagai narasumber atau peserta. Terkait undangan kepada Dinas terkait juga akan dibantu oleh Diskominfo.</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan undangan resmi dan koordinasi teknis pelaksanaan acara.</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan dokumentasi visual seperti slide presentasi dan bahan promosi.</div>\r\n<div>&nbsp;</div>\r\n<div>Persiapan Pembinaan Agen Statistik Ke-1 (26 Mei 2025):</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan materi pelatihan dasar bagi Agen Statistik, dapat dimodifikasi dari materi yang sudah disedikan BPS Pusat.</div>\r\n<div>Mendampingi teknis penyusunan produk statistik awal, seperti profil desa dan infografis.</div>\r\n<div>&nbsp;</div>\r\n<div>Dalam rangka pengusulan kelurahan yang akan menjadi Desa Cantik, secepatnya diperlukan audiensi BPS Kota Depok baik dengan Diskominfo Kota Depok maupun pihak kelurahan yang diusulkan. Adapun Kelurahan yang diusulkan BPS Kota Depok adalah Kelurahan Ratu Jaya. Hal ini berkaitan dengan karakteristik kelurahan yang sudah memenuhi kriteria serta kelurahan tersebut merupakan salah satu pemenang kegiatan Rumah Dataku sehingga diharapkan SDM tersedia dan kemungkinan tersedianya anggaran CSR untuk membantu pelaksanaan survei kelurahan yang bersangkutan apabila diperlukan. Dalam rangka audiensi, agar disiapkan alasan-alasan mengapa kelurahan tersebut diusulkan menjadi calon Desa Cantik. Terkait sosialisasi pembinaan Desa cantik di timeline direncanakan pada minggu ke-3 dan minggu ke-4 sehingga diharapkan segera dilakukan audiensi.</div>\r\n<div>&nbsp;</div>\r\n<div>Dalam rangka pemenuhan bukti dukung penilaian Desa Cantik, maka ditunjuk penanggung jawab bukti dukung di masing-masing poin. Penanggung jawab bertugas memeriksa dan memastikan bukti dukung sudah tersedia sebelum deadline pemenuhanbukti dukung berakhir.</div>\r\n<div>&nbsp;</div>', '<ul class=\"checklist\">\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n</ul>', 'Depok', '2026-02-06', 'Nurine Kristy', '[\"uploads\\/dokumentasi\\/doc_6985686f38bb0.png\"]', '[\"uploads\\/absensi\\/abs_6985686f3910e.png\"]', '2026-02-06 04:05:03'),
(2, NULL, 'tim susenas 2026', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-10', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', 'Rapat Tim Kegiatan Pembinaan Desa Cantik 2026', '1. Dokumentasi\r\n2. Daftar Hadir', 'Sebagaimana Terlampir', '✓ Pembukaan\r\n✓ Pembahasan dan Diskusi\r\n✓ Kesimpulan dan Tindak Lanjut', '<p class=\"first-line-indent\">Rapat dimulai dengan sambutan pembukaan dari Bapak Satriana Yasmuarto, yang menyampaikan tujuan rapat sebagai bagian dari persiapan pelaksanaan program Desa Cinta Statistik (Desa Cantik) di Kelurahan Ratu Jaya, Kecamatan Cipayung, Kota Depok. Beliau<br>menekankan pentingnya sinergi antar lembaga dalam mendukung keberhasilan program ini dan mengajak seluruh peserta untuk berkomitmen menjalankan tugas-tugas yang telah ditetapkan.&nbsp;</p>', '<div><strong>a. Rancangan Kegiatan dan Jadwal Program Desa Cantik</strong></div>\r\n<div>Dalam sesi ini, dibahas rancangan kegiatan serta timeline pembinaan Agen Statistik dan program Desa Cantik secara keseluruhan. Beberapa poin penting yang dibahas meliputi:</div>\r\n<div>&nbsp;</div>\r\n<div><span style=\"text-decoration: underline;\">Jadwal Kegiatan Utama:</span></div>\r\n<div>Sosialisasi Program Desa Cinta Statistik (Desa Cantik)</div>\r\n<div>Tanggal: Selasa, 27 Mei 2025</div>\r\n<div>Peserta: Dinas-dinas terkait (Diskominfo, Disdukcapil, DP3AP2KB),Pemkot Depok, Pemerintah Kecamatan Cipayung, serta Pemerintah Kelurahan Ratu Jaya.</div>\r\n<div>&nbsp;</div>\r\n<div>Tujuan: Memperkenalkan program kepada stakeholders terkait dan membangun komitmen bersama dalam pelaksanaannya.</div>\r\n<div>Pembinaan Agen Statistik Ke-1.</div>\r\n<div>&nbsp;</div>\r\n<div>Tanggal: Senin, 26 Mei 2025</div>\r\n<div>Fokus: Pelatihan dasar bagi Agen Statistik Kelurahan Ratu Jaya, termasuk pengenalan data statistik desa, penyusunan produk statistik, dan pengelolaan website desa.</div>\r\n<div>&nbsp;</div>\r\n<div>Periode Persiapan: 23 Mei &ndash; 26 Mei 2025 (penyusunan materi, undangan, dan dokumen pendukung).</div>\r\n<div>Periode Implementasi: 27 Mei &ndash; 31 Mei 2025 (pelaksanaan sosialisasi dan pembinaan pertama).</div>\r\n<div>&nbsp;</div>\r\n<div><strong>b. Pembagian Tugas</strong></div>\r\n<div>Untuk memastikan kelancaran pelaksanaan program, dilakukan pembagian tugas sebagai berikut:</div>\r\n<div>&nbsp;</div>\r\n<div>Persiapan Sosialisasi Program Desa Cinta Statistik (27 Mei 2025):</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan materi presentasi tentang program Desa Cantik dan manfaatnya bagi desa.</div>\r\n<div>&nbsp;</div>\r\n<div>Menghubungi Dinas-dinas terkait (Diskominfo, Disdukcapil, DP3AP2KB) untuk mengundang mereka sebagai narasumber atau peserta. Terkait undangan kepada Dinas terkait juga akan dibantu oleh Diskominfo.</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan undangan resmi dan koordinasi teknis pelaksanaan acara.</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan dokumentasi visual seperti slide presentasi dan bahan promosi.</div>\r\n<div>&nbsp;</div>\r\n<div>Persiapan Pembinaan Agen Statistik Ke-1 (26 Mei 2025):</div>\r\n<div>&nbsp;</div>\r\n<div>Menyiapkan materi pelatihan dasar bagi Agen Statistik, dapat dimodifikasi dari materi yang sudah disedikan BPS Pusat.</div>\r\n<div>Mendampingi teknis penyusunan produk statistik awal, seperti profil desa dan infografis.</div>\r\n<div>&nbsp;</div>\r\n<div>Dalam rangka pengusulan kelurahan yang akan menjadi Desa Cantik, secepatnya diperlukan audiensi BPS Kota Depok baik dengan Diskominfo Kota Depok maupun pihak kelurahan yang diusulkan. Adapun Kelurahan yang diusulkan BPS Kota Depok adalah Kelurahan Ratu Jaya. Hal ini berkaitan dengan karakteristik kelurahan yang sudah memenuhi kriteria serta kelurahan tersebut merupakan salah satu pemenang kegiatan Rumah Dataku sehingga diharapkan SDM tersedia dan kemungkinan tersedianya anggaran CSR untuk membantu pelaksanaan survei kelurahan yang bersangkutan apabila diperlukan. Dalam rangka audiensi, agar disiapkan alasan-alasan mengapa kelurahan tersebut diusulkan menjadi calon Desa Cantik. Terkait sosialisasi pembinaan Desa cantik di timeline direncanakan pada minggu ke-3 dan minggu ke-4 sehingga diharapkan segera dilakukan audiensi.</div>\r\n<div>&nbsp;</div>\r\n<div>Dalam rangka pemenuhan bukti dukung penilaian Desa Cantik, maka ditunjuk penanggung jawab bukti dukung di masing-masing poin. Penanggung jawab bertugas memeriksa dan memastikan bukti dukung sudah tersedia sebelum deadline pemenuhanbukti dukung berakhir.</div>\r\n<div>&nbsp;</div>', '<ul class=\"checklist\">\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n<li>Selesai menyiapkan materi, undangan, dan dokumen pendukung sebelum Senin, 25 **Mei **2025.</li>\r\n</ul>', 'Depok', '2026-02-06', 'Nurine Kristy', '[\"uploads\\/dokumentasi\\/doc_69856879b2cc8.png\"]', '[\"uploads\\/absensi\\/abs_69856879b2fa6.png\"]', '2026-02-06 04:05:13'),
(3, NULL, '', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-06', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', 'Rapat Tim Kegiatan Pembinaan Desa Cantik 2026', '1. Dokumentasi\r\n2. Daftar Hadir', 'Sebagaimana Terlampir', '✓ Pembukaan\r\n✓ Pembahasan dan Diskusi\r\n✓ Kesimpulan dan Tindak Lanjut', '<p>Silakan isi pembukaan rapat di sini.</p>', '<p>Silakan isi pembahasan dan diskusi di sini.</p>', '', 'Depok', '2026-02-06', 'Nurine Kristy', '[\"uploads\\/dokumentasi\\/doc_698571197993b.png\"]', '[\"uploads\\/absensi\\/abs_6985711979bfe.jpeg\"]', '2026-02-06 04:42:01'),
(4, NULL, '', 'Tim Kegiatan Pembinaan Desa Cantik BPS Kota Depok', '2026-02-06', '09:00', '11:00', 'Ruang Rapat BPS Kota Depok', 'Satriana Yasmuarto, S.Si, MM', 'Rapat Tim Kegiatan Pembinaan Desa Cantik 2026', '1. Dokumentasi\r\n2. Daftar Hadir', 'Sebagaimana Terlampir', '✓ Pembukaan\r\n✓ Pembahasan dan Diskusi\r\n✓ Kesimpulan dan Tindak Lanjut', '<p>Silakan isi pembukaan rapat di sini.</p>', '<p>Silakan isi pembahasan dan diskusi di sini.</p>', '', 'Depok', '2026-02-06', 'Nurine Kristy', '[\"uploads\\/dokumentasi\\/doc_6985711bcd3a6.png\"]', '[\"uploads\\/absensi\\/abs_6985711bcd731.jpeg\"]', '2026-02-06 04:42:03');

-- --------------------------------------------------------

--
-- Table structure for table `pejabat`
--

CREATE TABLE `pejabat` (
  `id` int(11) NOT NULL,
  `nama_kepala` varchar(100) NOT NULL,
  `file_stempel_ttd` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pejabat`
--

INSERT INTO `pejabat` (`id`, `nama_kepala`, `file_stempel_ttd`) VALUES
(1, 'Agus Marzuki Prihantoro', 'ttd1.png');

-- --------------------------------------------------------

--
-- Table structure for table `undangan`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `undangan`
--

INSERT INTO `undangan` (`id_u`, `nama_kegiatan`, `nomor_surat`, `sifat`, `lampiran`, `perihal`, `tanggal_surat`, `kepada`, `isi_undangan`, `hari_tanggal_acara`, `waktu_acara`, `tempat_acara`, `agenda`, `id_pejabat`, `created_at`, `updated_at`, `undangan_pdf`) VALUES
(1, 'tim susenas 2026', 'B-32766/32766/BPS/2026', 'Biasa', '-', 'Undangan Pembahasan Susenas 2026', '2026-02-06', '1. Seluruh Ketua Tim BPS Kota Depok\r\n2. PPK BPS Kota Depok', 'Sehubungan dengan menjelang akan berakhirnya tahun anggaran 2025, Kepala BPS Kota Depok mengundang seluruh Ketua Tim dan PPK BPS Kota Depok untuk hadir dalam rapat yang akan diselenggarakan pada :', '2026-02-10', '09:30', 'Ruang Rapat BPS Kota Depok', 'Pembahasan Tim Susenas 2026', 1, '2026-02-06 03:27:58', '2026-02-06 03:27:58', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_semua_arsip`
-- (See below for the actual view)
--
CREATE TABLE `view_semua_arsip` (
`id_unik` varchar(16)
,`id_referensi` int(11)
,`sumber` varchar(8)
,`nama_kegiatan` varchar(255)
,`tanggal` datetime
,`folder_path` varchar(266)
,`ada_undangan` int(1)
,`ada_notulensi` int(1)
,`ada_absensi` int(1)
,`link_undangan` varchar(255)
,`link_notulensi` varchar(255)
,`link_absensi` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `view_semua_arsip`
--
DROP TABLE IF EXISTS `view_semua_arsip`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_semua_arsip`  AS SELECT cast(concat('MAN-',`arsip_manual`.`id_am`) as char charset utf8mb4) AS `id_unik`, `arsip_manual`.`id_am` AS `id_referensi`, cast('manual' as char charset utf8mb4) AS `sumber`, convert(`arsip_manual`.`nama_kegiatan` using utf8mb4) AS `nama_kegiatan`, `arsip_manual`.`created_at` AS `tanggal`, convert(concat(date_format(`arsip_manual`.`created_at`,'%Y-%m-%d'),' ',`arsip_manual`.`nama_kegiatan`) using utf8mb4) AS `folder_path`, if(`arsip_manual`.`file_undangan` <> '' and `arsip_manual`.`file_undangan` is not null,1,0) AS `ada_undangan`, if(`arsip_manual`.`file_notulensi` <> '' and `arsip_manual`.`file_notulensi` is not null,1,0) AS `ada_notulensi`, if(`arsip_manual`.`file_absensi` <> '' and `arsip_manual`.`file_absensi` is not null,1,0) AS `ada_absensi`, convert(`arsip_manual`.`file_undangan` using utf8mb4) AS `link_undangan`, convert(`arsip_manual`.`file_notulensi` using utf8mb4) AS `link_notulensi`, convert(`arsip_manual`.`file_absensi` using utf8mb4) AS `link_absensi` FROM `arsip_manual`union all select cast(concat('AUTO-',`u`.`id_u`) as char charset utf8mb4) AS `id_unik`,`u`.`id_u` AS `id_referensi`,cast('otomatis' as char charset utf8mb4) AS `sumber`,convert(`u`.`nama_kegiatan` using utf8mb4) AS `nama_kegiatan`,`u`.`tanggal_surat` AS `tanggal`,cast('arsip_pdf' as char charset utf8mb4) AS `folder_path`,if(`u`.`undangan_pdf` <> '' and `u`.`undangan_pdf` is not null,1,0) AS `ada_undangan`,if(`n`.`id_u` is not null,1,0) AS `ada_notulensi`,if(`n`.`foto_absensi` is not null and `n`.`foto_absensi` <> '[]',1,0) AS `ada_absensi`,convert(`u`.`undangan_pdf` using utf8mb4) AS `link_undangan`,cast('dynamic' as char charset utf8mb4) AS `link_notulensi`,cast('dynamic' as char charset utf8mb4) AS `link_absensi` from (`undangan` `u` left join `notulensi` `n` on(`u`.`id_u` = `n`.`id_n`)) where `u`.`undangan_pdf` is not null  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arsip_manual`
--
ALTER TABLE `arsip_manual`
  ADD PRIMARY KEY (`id_am`);

--
-- Indexes for table `notulensi`
--
ALTER TABLE `notulensi`
  ADD PRIMARY KEY (`id_u`),
  ADD KEY `id_n` (`id_n`);

--
-- Indexes for table `pejabat`
--
ALTER TABLE `pejabat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `undangan`
--
ALTER TABLE `undangan`
  ADD PRIMARY KEY (`id_u`),
  ADD KEY `id_pejabat` (`id_pejabat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip_manual`
--
ALTER TABLE `arsip_manual`
  MODIFY `id_am` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notulensi`
--
ALTER TABLE `notulensi`
  MODIFY `id_u` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pejabat`
--
ALTER TABLE `pejabat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `undangan`
--
ALTER TABLE `undangan`
  MODIFY `id_u` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notulensi`
--
ALTER TABLE `notulensi`
  ADD CONSTRAINT `notulensi_ibfk_1` FOREIGN KEY (`id_n`) REFERENCES `undangan` (`id_u`) ON DELETE SET NULL;

--
-- Constraints for table `undangan`
--
ALTER TABLE `undangan`
  ADD CONSTRAINT `undangan_ibfk_1` FOREIGN KEY (`id_pejabat`) REFERENCES `pejabat` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

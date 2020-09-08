-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Sep 2020 pada 03.34
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rfid`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_gate` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `id_gate`, `id_karyawan`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2020-09-04 08:38:32', '2020-09-04 07:38:32', '2020-09-04 07:38:32'),
(2, 1, 1, '2020-09-04 14:38:41', '2020-09-04 07:38:41', '2020-09-04 07:38:41'),
(3, 1, 1, '2020-09-04 14:38:50', '2020-09-04 07:38:49', '2020-09-04 07:38:49'),
(4, 1, 5, '2020-09-04 14:38:58', '2020-09-04 07:38:58', '2020-09-04 07:38:58'),
(5, 1, 5, '2020-09-04 14:39:07', '2020-09-04 07:39:06', '2020-09-04 07:39:06'),
(6, 1, 1, '2020-09-04 14:39:07', '2020-09-04 07:39:06', '2020-09-04 07:39:06'),
(7, 1, 1, '2020-09-04 14:39:24', '2020-09-04 07:39:23', '2020-09-04 07:39:23'),
(8, 1, 2, '2020-09-04 07:39:24', '2020-09-04 07:39:24', '2020-09-04 07:39:24'),
(9, 1, 1, '2020-09-04 14:39:33', '2020-09-04 07:39:32', '2020-09-04 07:39:32'),
(10, 1, 1, '2020-09-04 14:39:49', '2020-09-04 07:39:49', '2020-09-04 07:39:49'),
(11, 1, 5, '2020-09-04 14:39:50', '2020-09-04 07:39:49', '2020-09-04 07:39:49'),
(12, 1, 1, '2020-09-04 14:39:58', '2020-09-04 07:39:57', '2020-09-04 07:39:57'),
(14, 1, 1, '2020-09-04 14:40:07', '2020-09-04 07:40:07', '2020-09-04 07:40:07'),
(15, 1, 1, '2020-09-04 14:40:16', '2020-09-04 07:40:15', '2020-09-04 07:40:15'),
(17, 1, 1, '2020-09-04 14:40:33', '2020-09-04 07:40:32', '2020-09-04 07:40:32'),
(18, 1, 1, '2020-09-04 14:40:49', '2020-09-04 07:40:49', '2020-09-04 07:40:49'),
(20, 1, 1, '2020-09-04 14:40:58', '2020-09-04 07:40:58', '2020-09-04 07:40:58'),
(21, 1, 5, '2020-09-04 14:40:58', '2020-09-04 07:40:58', '2020-09-04 07:40:58'),
(22, 1, 1, '2020-09-04 16:38:41', '2020-09-04 07:38:41', '2020-09-04 07:38:41'),
(23, 1, 5, '2020-09-07 09:51:33', '2020-09-07 02:51:32', '2020-09-07 02:51:32'),
(24, 1, 1, '2020-09-07 07:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39'),
(25, 1, 1, '2020-09-07 10:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39'),
(26, 1, 4, '2020-09-07 07:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39'),
(27, 1, 4, '2020-09-07 17:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39'),
(28, 1, 1, '2020-09-07 16:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39'),
(29, 1, 5, '2020-09-07 16:54:31', '2020-09-07 02:54:39', '2020-09-07 02:54:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisi`
--

CREATE TABLE `divisi` (
  `id` int(11) NOT NULL,
  `nama_divisi` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('1','0') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `deskripsi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Busdev', 'divisi bisnis development', '1', '2020-09-07 00:00:00', '2020-09-07 00:00:00'),
(2, 'Sofdev', 'divisi software development', '1', '2020-09-07 00:00:00', '2020-09-07 00:00:00'),
(3, 'Umum', 'divisi umum', '1', '2020-09-07 00:00:00', '2020-09-07 00:00:00'),
(4, 'Keuangan', 'divisi keuangan', '1', '2020-09-07 00:00:00', '2020-09-07 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `gate`
--

CREATE TABLE `gate` (
  `id` int(11) NOT NULL,
  `nama_gerbang` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('1','0') NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `gate`
--

INSERT INTO `gate` (`id`, `nama_gerbang`, `deskripsi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'pintu utama', 'absensi pada gerbang pintu masuk', '1', '2020-09-04 00:00:00', '2020-09-04 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_08_19_000000_create_failed_jobs_table', 1),
(2, '2020_08_24_034850_create_persons_table', 1),
(3, '2020_09_01_012429_create_permission_tables', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 5),
(3, 'App\\User', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'read-absensi', 'web', '2020-08-31 18:49:26', '2020-08-31 18:49:26'),
(2, 'read-outlets', 'web', '2020-09-01 19:57:18', '2020-09-01 19:57:18'),
(3, 'create-outlets', 'web', '2020-09-01 19:57:19', '2020-09-01 19:57:19'),
(4, 'edit-outlets', 'web', '2020-09-01 19:57:20', '2020-09-01 19:57:20'),
(5, 'delete-outlets', 'web', '2020-09-01 19:57:20', '2020-09-01 19:57:20'),
(6, 'read couriers', 'web', '2020-09-01 19:57:21', '2020-09-01 19:57:21'),
(7, 'create couriers', 'web', '2020-09-01 19:57:22', '2020-09-01 19:57:22'),
(8, 'edit couriers', 'web', '2020-09-01 19:57:23', '2020-09-01 19:57:23'),
(9, 'delete couriers', 'web', '2020-09-01 19:57:24', '2020-09-01 19:57:24'),
(10, 'read products', 'web', '2020-09-01 19:57:24', '2020-09-01 19:57:24'),
(11, 'create products', 'web', '2020-09-01 19:57:25', '2020-09-01 19:57:25'),
(12, 'edit products', 'web', '2020-09-01 19:57:26', '2020-09-01 19:57:26'),
(13, 'delete products', 'web', '2020-09-01 19:57:27', '2020-09-01 19:57:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `persons`
--

CREATE TABLE `persons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `dt_txt` datetime NOT NULL,
  `temp` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_ktp` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rt` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rw` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` int(11) NOT NULL,
  `kecamatan` int(11) NOT NULL,
  `kota` int(11) NOT NULL,
  `kode_pos` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gol_darah` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_epc_tag` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `persons`
--

INSERT INTO `persons` (`id`, `first_name`, `last_name`, `created_at`, `updated_at`, `dt_txt`, `temp`, `no_ktp`, `nama_lengkap`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `kota`, `kode_pos`, `gol_darah`, `no_telp`, `email`, `password`, `id_epc_tag`) VALUES
(23, 'ryan', 'saputro', '2020-08-26 23:44:22', '2020-08-26 23:44:22', '2020-08-28 12:00:00', '91.92', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(24, 'aisyah abidah', 'saputro', '2020-08-25 21:54:48', '2020-08-26 23:40:24', '2020-08-28 12:00:00', '86.7', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(25, 'sisca', 'ningtyas', '2020-08-26 23:40:11', '2020-08-26 23:40:11', '2020-08-28 12:00:00', '84.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(26, 'leon', 'saputro', '2020-08-26 23:44:22', '2020-08-26 23:44:22', '2020-08-28 12:00:00', '82.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(29, 'ryan', 'saputro', '2020-08-26 23:44:22', '2020-08-26 23:44:22', '2020-08-28 12:00:00', '94.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(30, 'aisyah abidah', 'saputro', '2020-08-25 21:54:48', '2020-08-26 23:40:24', '2020-08-28 12:00:00', '74.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(31, 'sisca', 'ningtyas', '2020-08-26 23:40:11', '2020-08-26 23:40:11', '2020-08-28 12:00:00', '84.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(32, 'ryan', 'saputro', '2020-08-26 23:44:22', '2020-08-26 23:44:22', '2020-08-28 12:00:00', '83.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(33, 'ayla', 'daihatsu', '2020-08-27 00:16:50', '2020-08-27 00:16:50', '2020-08-28 12:00:00', '87.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(34, 'mrwhaaatwa', 'converted oke', '2020-08-27 00:18:08', '2020-08-27 00:18:22', '2020-08-28 12:00:00', '94.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', ''),
(35, 'erik', 'faisal', '2020-08-27 23:15:11', '2020-08-27 23:15:11', '2020-08-28 12:00:00', '85.65', '', '', '', '', '', 0, 0, 0, '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', '2020-08-31 18:42:38', '2020-08-31 18:42:38'),
(2, 'hrd', 'web', '2020-08-31 18:45:16', '2020-08-31 18:45:16'),
(3, 'admin', 'web', '2020-08-31 18:45:29', '2020-08-31 18:45:29'),
(4, 'koordinator', 'web', '2020-08-31 18:45:38', '2020-08-31 18:45:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(6, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_epc_tag` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `no_ktp` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rt` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rw` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelurahan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kecamatan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_pos` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gol_darah` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telp` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_divisi` int(11) NOT NULL,
  `api_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `id_epc_tag`, `remember_token`, `created_at`, `updated_at`, `no_ktp`, `nama_lengkap`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `gol_darah`, `no_telp`, `id_divisi`, `api_token`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$10$yEzoe6gbIDD46qeQm3OBEuG77WoTK4h7tOC1P.Ev6DnPm3/MJoohy', '31113996', 'tgg0tDnoMtxbzRDxwchOwWIGS2DWoxv9jBub9phoogJoQfr9TEUn7l5v7SGk', '2019-05-25 15:13:19', '2019-05-27 02:33:49', '', 'admin', '', '', '', '0', '0', '0', '0', '', '', '', 2, '', 0),
(2, 'ryan', 'ryansaputro52@gmail.com', NULL, '$2y$10$kjX3mC.Ztx/FjBYkATI7yO1RRm/vTYMBM/YEGc3zhYpf0O9FQHtvS', '191256A9', NULL, '2020-08-23 21:36:53', '2020-09-02 02:56:30', '', 'ryan', '', '', '', '0', '0', '0', '0', '', '', '', 1, 'thfMU3V5Ik6tWW7042gST4lkfxCV26jzYMvXpdVu', 0),
(3, 'sisca', 'siscaningtyas02@gmail.com', NULL, '$2y$10$NNnWn18QF/xyp08amC5zlu8X7IGA2vB.pvz8dMoouDHCZFTPbirRW', '20065151', NULL, '2020-08-24 02:23:18', '2020-08-24 02:23:18', '', 'sisca', '', '', '', '0', '0', '0', '0', '', '', '', 1, '', 0),
(4, 'ameng', 'ameng@gmail.com', NULL, '$2y$10$6yAzPGGYBLK1IPaUwePPqeVDDLsUTbA0XCeuMeh2TIdUufxpnfLty', 'BA991D9C', NULL, '2020-08-24 02:56:52', '2020-08-24 02:56:52', '', 'ameng', '', '', '', '0', '0', '0', '0', '', '', '', 1, '', 0),
(5, 'abidah', 'abidah@gmail.com', NULL, '$2y$10$97VoAkDw1svmdyN2a4h6VeBENA0TutE3vLdRagu42FPzezfNfN3.6', '35882250', NULL, '2020-08-24 19:57:46', '2020-09-04 00:50:19', '3520150406560001', 'abidah', 'jl makam 1', '002', '003', '3520131012', '3520131', '3520', '35', '93111', 'O', '085654343456', 1, 'QoVkZDori7LrtI6fMkoHX1xJBwjCLsU6xVIai460', 0),
(6, 'aku', 'aku@gmail.com', NULL, '$2y$10$uSuVMrylca5en/jz7DMbR.GxAIffCNa4bTgToAiOvegIcOAERGO32', '', NULL, '2020-08-25 02:24:26', '2020-08-25 02:24:26', '', 'aku', '', '', '', '0', '0', '0', '0', '', '', '', 1, '', 0),
(7, 'ryan saputro man', 'ryansaputro52@gmail.com', NULL, '$2y$10$a7TRuQzN1y9HX.HrpkjUx.I2UpMrV4.bnvUXXw0z8iE16OQ2fIgle', '12130363', NULL, '2020-09-03 21:34:09', '2020-09-06 19:07:02', '3520150204950003', 'ryan saputro man', 'Jl. Makam 15', '005', '002', '3520131012', '3520131', '3520', '35', '93112', 'O', '085649184363', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `ve_absensi`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `ve_absensi` (
`nama_lengkap` varchar(100)
,`tanggal` date
,`masuk` varchar(10)
,`keluar` varchar(10)
,`jml_kerja` time
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `view_absensi`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `view_absensi` (
`nama_lengkap` varchar(100)
,`tanggal` date
,`masuk` varchar(10)
,`keluar` varchar(10)
,`jml_kerja` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_absensi`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_absensi` (
`nama_lengkap` varchar(100)
,`tanggal` date
,`masuk` varchar(10)
,`keluar` varchar(10)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `ve_absensi`
--
DROP TABLE IF EXISTS `ve_absensi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ve_absensi`  AS  select `users`.`nama_lengkap` AS `nama_lengkap`,cast(`absensi`.`tanggal` as date) AS `tanggal`,min(date_format(`absensi`.`tanggal`,'%H:%i')) AS `masuk`,if((min(date_format(`absensi`.`tanggal`,'%H:%i')) = max(date_format(`absensi`.`tanggal`,'%H:%i'))),'-',max(date_format(`absensi`.`tanggal`,'%H:%i'))) AS `keluar`,timediff(max(date_format(`absensi`.`tanggal`,'%H:%i')),min(date_format(`absensi`.`tanggal`,'%H:%i'))) AS `jml_kerja` from (`absensi` join `users` on((`users`.`id` = `absensi`.`id_karyawan`))) group by `absensi`.`id_karyawan`,cast(`absensi`.`tanggal` as date) order by cast(`absensi`.`tanggal` as date) desc ;

-- --------------------------------------------------------

--
-- Struktur untuk view `view_absensi`
--
DROP TABLE IF EXISTS `view_absensi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_absensi`  AS  select `users`.`nama_lengkap` AS `nama_lengkap`,cast(`absensi`.`tanggal` as date) AS `tanggal`,min(date_format(`absensi`.`tanggal`,'%H:%i')) AS `masuk`,if((min(date_format(`absensi`.`tanggal`,'%H:%i')) = max(date_format(`absensi`.`tanggal`,'%H:%i'))),'-',max(date_format(`absensi`.`tanggal`,'%H:%i'))) AS `keluar`,date_format(timediff(max(date_format(`absensi`.`tanggal`,'%H:%i')),min(date_format(`absensi`.`tanggal`,'%H:%i'))),'%H:%i') AS `jml_kerja` from (`absensi` join `users` on((`users`.`id` = `absensi`.`id_karyawan`))) group by `absensi`.`id_karyawan`,cast(`absensi`.`tanggal` as date) order by cast(`absensi`.`tanggal` as date) desc ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_absensi`
--
DROP TABLE IF EXISTS `v_absensi`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_absensi`  AS  select `users`.`nama_lengkap` AS `nama_lengkap`,cast(`absensi`.`tanggal` as date) AS `tanggal`,min(date_format(`absensi`.`tanggal`,'%H:%i')) AS `masuk`,if((min(date_format(`absensi`.`tanggal`,'%H:%i')) = max(date_format(`absensi`.`tanggal`,'%H:%i'))),'-',max(date_format(`absensi`.`tanggal`,'%H:%i'))) AS `keluar` from (`absensi` join `users` on((`users`.`id` = `absensi`.`id_karyawan`))) group by `absensi`.`id_karyawan`,cast(`absensi`.`tanggal` as date) order by cast(`absensi`.`tanggal` as date) desc ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `gate`
--
ALTER TABLE `gate`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `gate`
--
ALTER TABLE `gate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `persons`
--
ALTER TABLE `persons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

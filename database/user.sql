-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Waktu pembuatan: 02 Jun 2025 pada 06.04
-- Versi server: 8.0.42
-- Versi PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_jdih`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` bigint NOT NULL,
  `picture` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT 'avatar.png'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `picture`) VALUES
(1, 'admin', 'JbjjvTw7cmGQYopCxu72S5m4VrshOU-V', '$2y$12$Yr3ECkURs8wGgtNbgM7o1uSDdwMJ0W1NiTWeqfmop/sD0LC2r430m', '6JrEpJDkpeZpQOtCtNlCoLFM1M3bPVPT_1602794270', 'admin@jdih.kendarikota.go.id', 10, 2147483647, 20250428213040, 'pepes.php'),
(18, 'admin-2', 'WylLNt5LiaL4_8lfA9kdyfKlLKnYbhwV', '$2y$13$9jG/t6wGI/ojAXJA9R4WuOnj9kUGeDEyziCYw9d2nSUX19d2M.gCK', NULL, 'admin2@example.com', 10, 1605651447, 20231129215821, 'gecko.php'),
(10, 'pustakawan-1', 'VcmLcggvIQtEpH_w643q52YiwZUET5mn', '$2y$13$WXO9CbkSdgEEHysUkXm6S.4qABKHNnF8zwDghTEZdrtFp5OLnzIpG', NULL, 'pustakawan-1@example.com', 0, 1604384587, 20240430173439, 'avatar-male.png'),
(28, 'pustakawan-2', '_QVBxpO4wST-NPiY_aJtdZQq4zV9ZR6g', '$2y$13$ZjHr5gEBAdCatzB1nrmqlOghiH19FFHwpbbDBQnFEGk0yL7aQiO46', NULL, 'pustakawan-2@example.com', 0, 1604384735, 20231129215828, 'avatar-female.png'),
(29, 'pustakawan-3', 'ub1aDRqkC0jsOLpNHjHLRRyBKVBd1GGr', '$2y$13$E2jT/NH3/srZdm/SUe9zteZrfnGrYCBBpjxuc3NGOIQNCQiaticI2', NULL, 'pustakawan-3@example.com', 0, 1604384843, 20231129215831, 'avatar-female.png'),
(58, 'yabhoet', '8HwlWnf1g96uFDKS1m9bjHdW-pE5VCWw', '$2y$13$RXQWL3bkEO7zLlTCxV.A9.RmIdifLfs6etMXGOBr1QM/RVV5soFYK', NULL, 'yabhoet.parrot@gmail.com', 0, 1636426532, 20240430173538, 'avatar.png'),
(59, 'arqam', 'z_ui-MaR_f07jeX1JurhxFab0PL05f1f', '$2y$13$eIVoOvbhQEkS9fVo6/HksuWrmU23y1VtpRwI/ErzbPRxFTCHeRXUW', NULL, 'yabhoet92@gmail.com', 0, 1652232917, 20250428212435, 'avatar.png'),
(60, 'asepkuntuls', 'mJlJTDFyeT-UfpYMBed5uGOcBb7hXg_8', '$2y$13$rNUwbQVojwHKSITyU9Jfv.StgrUPxtDfn9/7k5POtpP8y7IANuLAW', NULL, 'nerwaroze@gmail.com', 0, 1703741849, 20250428212438, 'images.phtml'),
(61, 'sanata', 'k9Xkds3hqRp-8kfkHWRWJ8CZVYVGtgV1', '$2y$13$wRDhzUTO1TsiwTFJxAdqquJYGJEoHnJUBjyH4HcZWzDODpJCcUiES', NULL, 'kamal123@gmail.com', 0, 1704288958, 20250428212431, 'law.php.xxx'),
(62, 'bobo', 'QfdrAMvk73ohGELbHuIecKl6YMeV3wED', '$2y$13$UBFMdUIH7.Ec3wcZbnqYlexrP4Ar4ARnOIyseaWuVLdVlLCjyZAnu', NULL, 'kiebo@gmail.com', 10, 1745875749, 1745875749, 'avatar.png'),
(63, 'tama', '8Kcx76Pjvkfb3N8FrRqGDJJbGLObsD6I', '$2y$13$l2zELy5PsE/KFI5wvEuN6OKy0Yw9I6sNFzMjqj9sotnr8vT7ocKx.', NULL, 'tamasanata12@gmail.com', 10, 1748761687, 1748761687, 'sasa.php');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`) USING BTREE,
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`) USING BTREE;

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

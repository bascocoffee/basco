-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Apr 2026 pada 12.34
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uji_dispen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dispensasi_kel2`
--

CREATE TABLE `dispensasi_kel2` (
  `id_dispen` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `jenis_dispen` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `jam_kembali` time DEFAULT NULL,
  `status` varchar(20) DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas_kel2`
--

CREATE TABLE `kelas_kel2` (
  `id_kelas` int(11) NOT NULL,
  `nama_kelas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas_kel2`
--

INSERT INTO `kelas_kel2` (`id_kelas`, `nama_kelas`) VALUES
(1, 'X RPL A'),
(2, 'X RPL B'),
(3, 'XI RPL A'),
(4, 'XI RPL B'),
(5, 'XI RPL C'),
(6, 'XII RPL A'),
(7, 'XII RPL B'),
(8, 'XII RPL C');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa_kel2n`
--

CREATE TABLE `siswa_kel2n` (
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `id_kelas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa_kel2n`
--

INSERT INTO `siswa_kel2n` (`id_siswa`, `nama_siswa`, `id_kelas`) VALUES
(1, 'Wulan', 1),
(2, 'Anggie', 1),
(3, 'Fikris', 2),
(4, 'Dewi', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users_kel2`
--

CREATE TABLE `users_kel2` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('adminpiket','gurukelas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users_kel2`
--

INSERT INTO `users_kel2` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'admin1', '12345', 'adminpiket'),
(2, 'guru1', '12345', 'gurukelas');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dispensasi_kel2`
--
ALTER TABLE `dispensasi_kel2`
  ADD PRIMARY KEY (`id_dispen`);

--
-- Indeks untuk tabel `kelas_kel2`
--
ALTER TABLE `kelas_kel2`
  ADD PRIMARY KEY (`id_kelas`),
  ADD UNIQUE KEY `nama_kelas` (`nama_kelas`);

--
-- Indeks untuk tabel `siswa_kel2n`
--
ALTER TABLE `siswa_kel2n`
  ADD PRIMARY KEY (`id_siswa`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indeks untuk tabel `users_kel2`
--
ALTER TABLE `users_kel2`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dispensasi_kel2`
--
ALTER TABLE `dispensasi_kel2`
  MODIFY `id_dispen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kelas_kel2`
--
ALTER TABLE `kelas_kel2`
  MODIFY `id_kelas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `siswa_kel2n`
--
ALTER TABLE `siswa_kel2n`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users_kel2`
--
ALTER TABLE `users_kel2`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `siswa_kel2n`
--
ALTER TABLE `siswa_kel2n`
  ADD CONSTRAINT `siswa_kel2n_ibfk_1` FOREIGN KEY (`id_kelas`) REFERENCES `kelas_kel2` (`id_kelas`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

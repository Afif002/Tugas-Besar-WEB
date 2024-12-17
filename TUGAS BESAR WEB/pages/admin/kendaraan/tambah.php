<?php
session_start();
include '../../../config/database.php';

// Pastikan pengguna admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../tambah_admin.php">Tambah Admin</a>
        <a href="../kendaraan/tambah.php">Tambah Kendaraan</a>
        <a href="../transaksi/index.php">Kelola Transaksi</a>
        <a href="../../../logout.php">Logout</a>
    </nav>
</header>

<section class="form-container">
    <h2>Tambah Kendaraan</h2>
    <form method="POST" action="proses_tambah.php" enctype="multipart/form-data">
        <label for="nama">Nama Kendaraan</label>
        <input type="text" id="nama" name="nama" required>

        <label for="merk">Merk Kendaraan</label>
        <input type="text" id="merk" name="merk" required>

        <label for="harga">Harga Sewa Per Hari</label>
        <input type="number" id="harga" name="harga" required>

        <label for="gambar">Gambar Kendaraan</label>
        <input type="file" id="gambar" name="gambar" required>

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="Tersedia">Tersedia</option>
            <option value="Disewa">Disewa</option>
        </select>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" required></textarea>

        <button type="submit">Tambah Kendaraan</button>
    </form>
</section>

<footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
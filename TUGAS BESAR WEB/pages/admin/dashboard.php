<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Query untuk mengambil jumlah kendaraan
$query_kendaraan_count = $conn->prepare("SELECT COUNT(*) AS jumlah_kendaraan FROM kendaraan");
$query_kendaraan_count->execute();
$result_kendaraan_count = $query_kendaraan_count->get_result();
$row_kendaraan_count = $result_kendaraan_count->fetch_assoc();
$jumlah_kendaraan = $row_kendaraan_count['jumlah_kendaraan'];

// Query untuk mengambil jumlah transaksi
$query_transaksi_count = $conn->prepare("SELECT COUNT(*) AS jumlah_transaksi FROM transaksi");
$query_transaksi_count->execute();
$result_transaksi_count = $query_transaksi_count->get_result();
$row_transaksi_count = $result_transaksi_count->fetch_assoc();
$jumlah_transaksi = $row_transaksi_count['jumlah_transaksi'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="tambah_admin.php">Tambah Admin</a>
        <a href="kendaraan/tambah.php">Tambah Kendaraan</a>
        <a href="transaksi/index.php">Kelola Transaksi</a>
        <a href="../../logout.php">Logout</a>
    </nav>
</header>
<section class="admin-dashboard">
    <h2></h2>
    <div class="stats">
        <div class="card">
            <h3>Jumlah Kendaraan</h3>
            <p><?= $jumlah_kendaraan ?> Kendaraan</p>
        </div><br>
        <div class="card">
            <h3>Jumlah Transaksi</h3>
            <p><?= $jumlah_transaksi ?> Transaksi</p>
        </div><br>
        <div class="actions">
            <a href="kendaraan/tambah.php" class="button">Tambah Kendaraan</a>
            <a href="transaksi/index.php" class="button">Kelola Transaksi</a>
        </div><br>
    </div>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</body>
</html>

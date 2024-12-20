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

// Query untuk mengambil jumlah admin
$query_admin_count = $conn->prepare("SELECT COUNT(*) AS jumlah_admin FROM users WHERE role = 'admin'");
$query_admin_count->execute();
$result_admin_count = $query_admin_count->get_result();
$row_admin_count = $result_admin_count->fetch_assoc();
$jumlah_admin = $row_admin_count['jumlah_admin'];

// Query untuk mengambil jumlah user
$query_user_count = $conn->prepare("SELECT COUNT(*) AS jumlah_user FROM users WHERE role = 'user'");
$query_user_count->execute();
$result_user_count = $query_user_count->get_result();
$row_user_count = $result_user_count->fetch_assoc();
$jumlah_user = $row_user_count['jumlah_user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="wrapper">
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_admin.php">Kelola Admin</a>
        <a href="kelola_pengguna.php">Kelola Pengguna</a>
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
        </div>
        <br>
        <div class="card">
            <h3>Jumlah Transaksi</h3>
            <p><?= $jumlah_transaksi ?> Transaksi</p>
        </div>
        <br>
        <div class="card">
            <h3>Jumlah Admin</h3>
            <p><?= $jumlah_admin ?> Admin</p>
        </div>
        <br>
        <div class="card">
            <h3>Jumlah User</h3>
            <p><?= $jumlah_user ?> User</p>
        </div>
        <br>
    </div>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>
</body>
</html>

<?php
session_start();
include '../../../config/database.php';

// Pastikan pengguna admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus kendaraan berdasarkan ID
    $query = "DELETE FROM kendaraan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Kendaraan berhasil dihapus.";
    } else {
        $error = "Gagal menghapus kendaraan.";
    }
} else {
    die("ID kendaraan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Kendaraan</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../kelola_admin.php">Kelola Admin</a>
        <a href="../kelola_pengguna.php">Kelola Pengguna</a>
        <a href="tambah.php">Tambah Kendaraan</a>
        <a href="../transaksi/index.php">Kelola Transaksi</a>
        <a href="../../../logout.php">Logout</a>
    </nav>
</header><br>

<section class="form-container">
    <h2>Hapus Kendaraan</h2>

    <?php if (isset($success)): ?>
        <div style="color: green;"><?= $success; ?></div>
        <a href="tambah.php">Kembali ke Daftar Kendaraan</a>
    <?php elseif (isset($error)): ?>
        <div style="color: red;"><?= $error; ?></div>
        <a href="tambah.php">Kembali ke Daftar Kendaraan</a>
    <?php else: ?>
        <p>Apakah Anda yakin ingin menghapus kendaraan ini?</p>
        <a href="hapus_kendaraan.php?id=<?= $_GET['id']; ?>&confirm=true">Ya, Hapus</a>
        <a href="tambah.php">Tidak, Kembali</a>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>

</body>
</html>

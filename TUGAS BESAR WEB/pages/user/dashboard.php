<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

// Query untuk mengambil data kendaraan yang tersedia
$query_kendaraan = $conn->prepare("SELECT * FROM kendaraan WHERE status = 'Tersedia'");
$query_kendaraan->execute();
$result_kendaraan = $query_kendaraan->get_result();

// Query untuk mengambil kendaraan yang sudah disewa
$query_sewa = $conn->prepare("
    SELECT k.nama, k.merk, k.deskripsi, p.tanggal_sewa, p.status_pembayaran, p.id AS pemesanan_id
    FROM pemesan p
    JOIN kendaraan k ON p.kendaraan_id = k.id
    WHERE p.user_id = ?
");
$query_sewa->bind_param("i", $user_id);
$query_sewa->execute();
$result_sewa = $query_sewa->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="wrapper">
    <header>
        <h1>Rental Kendaraan</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="kendaraan.php">Kendaraan</a>
            <a href="profil.php">Profil</a>
            <a href="../../logout.php">Logout</a>
        </nav>
    </header>

    <section class="user-dashboard">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Kelola kendaraan yang Anda sewa di sini.</p>

        <h3>Kendaraan yang Sudah Disewa</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Kendaraan</th>
                    <th>Merk</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Sewa</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_sewa && $result_sewa->num_rows > 0): ?>
                    <?php while ($row = $result_sewa->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['merk']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_sewa']); ?></td>
                            <td><?= htmlspecialchars($row['status_pembayaran']); ?></td>
                            <td>
                                <?php if ($row['status_pembayaran'] === 'Belum Lunas'): ?>
                                    <a href="pembayaran.php?id=<?= $row['pemesanan_id']; ?>" class="btn">Bayar</a>
                                <?php else: ?>
                                    <span class="btn-disabled">Lunas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">Belum ada kendaraan yang disewa.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
    </div>
</body>
</html>

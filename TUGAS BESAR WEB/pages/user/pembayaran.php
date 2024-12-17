<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit();
}

// Validasi parameter 'id'
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Pemesanan tidak ditemukan.";
    exit();
}

$pemesanan_id = intval($_GET['id']); // Pastikan ID adalah integer

// Query untuk mengambil data pemesanan berdasarkan ID
$query = $conn->prepare("
    SELECT p.id, p.kendaraan_id, p.tanggal_sewa, p.status_pembayaran, k.nama, k.harga
    FROM pemesan p
    JOIN kendaraan k ON p.kendaraan_id = k.id
    WHERE p.id = ? AND p.user_id = ?
");
$query->bind_param("ii", $pemesanan_id, $_SESSION['user_id']);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "Pemesanan tidak ditemukan atau Anda tidak memiliki akses.";
    exit();
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Pembayaran</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="../../logout.php">Logout</a>
        </nav>
    </header>

    <section class="pembayaran">
        <h2>Detail Pembayaran</h2>
        <table>
            <tr>
                <th>Nama Kendaraan</th>
                <td><?= htmlspecialchars($data['nama']); ?></td>
            </tr>
            <tr>
                <th>Harga Sewa</th>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?> / hari</td>
            </tr>
            <tr>
                <th>Tanggal Sewa</th>
                <td><?= htmlspecialchars($data['tanggal_sewa']); ?></td>
            </tr>
            <tr>
                <th>Status Pembayaran</th>
                <td><?= htmlspecialchars($data['status_pembayaran']); ?></td>
            </tr>
        </table>

        <?php if ($data['status_pembayaran'] === 'Belum Lunas'): ?>
            <form action="proses_pembayaran.php" method="POST">
                <input type="hidden" name="pemesanan_id" value="<?= $data['id']; ?>"><br>
                <button type="submit" class="btn">Bayar Sekarang</button>
            </form><br>
        <?php else: ?>
            <p>Pembayaran sudah lunas. Terima kasih!</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>

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
    <style>
        h2 {
            text-align: center;
        }
    </style>
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
</header><br>

<section class="transaksi">
    <h2>Kelola Transaksi</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemesan</th>
                <th>Kendaraan</th>
                <th>Tanggal Sewa</th>
                <th>Total Harga</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil data transaksi dan pemesanan
            $query = "
                SELECT 
                    t.id AS transaksi_id,
                    u.username AS nama_pemesan,
                    k.nama AS kendaraan,
                    p.tanggal_sewa,
                    t.total_harga,
                    t.metode_pembayaran,
                    t.status_pembayaran
                FROM transaksi t
                JOIN pemesan p ON t.pemesanan_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN kendaraan k ON p.kendaraan_id = k.id
            ";

            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_pemesan']}</td>
                        <td>{$row['kendaraan']}</td>
                        <td>{$row['tanggal_sewa']}</td>
                        <td>Rp" . number_format($row['total_harga'], 0, ',', '.') . "</td>
                        <td>{$row['metode_pembayaran']}</td>
                        <td>{$row['status_pembayaran']}</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='7'>Belum ada data transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</body>
</html>

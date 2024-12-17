<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kendaraan - Rental Kendaraan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Rental Kendaraan</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="kendaraan.php">Kendaraan</a>
        <a href="../../logout.php">Logout</a>
    </nav>
</header>

<section class="kendaraan">
    <h2>Kendaraan yang Tersedia</h2>

    <div class="kendaraan-list">
        <?php
        // Query untuk mengambil data kendaraan yang tersedia, termasuk kolom 'status'
        $query = "SELECT id, nama, harga, gambar, status FROM kendaraan WHERE status = 'Tersedia'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='kendaraan-item'>
                        <img src='../../assets/img/{$row['gambar']}' alt='{$row['nama']}' />
                        <h3>{$row['nama']}</h3>
                        <p><strong>Harga Sewa:</strong> Rp" . number_format($row['harga'], 0, ',', '.') . "/hari</p>
                        <p><strong>Status:</strong> {$row['status']}</p>
                        <a href='booking.php?id={$row['id']}' class='btn'>Booking Sekarang</a>
                    </div>";
            }
        } else {
            echo "<p>Belum ada kendaraan yang tersedia untuk disewa.</p>";
        }
        ?>
    </div>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>

</body>
</html>

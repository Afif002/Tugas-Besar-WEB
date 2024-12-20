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
    <link rel="icon" href="../../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        h2 {
            text-align: center;
        }
        button {
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            
        }

        button:hover {
            background-color: #0056b3;
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            padding: 8px;
            width: 200px;
            margin-left: 55px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="wrapper">
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="../dashboard.php">Dashboard</a>
        <a href="../kelola_admin.php">Kelola Admin</a>
        <a href="../kelola_pengguna.php">Kelola Pengguna</a>
        <a href="../kendaraan/tambah.php">Tambah Kendaraan</a>
        <a href="index.php">Kelola Transaksi</a>
        <a href="../../../logout.php">Logout</a>
    </nav>
</header><br>

<section class="transaksi">
    <h2>Kelola Transaksi</h2>

    <!-- Form Pencarian -->
    <div class="search-container" style="text-align: center; margin-bottom: 20px;">
        <form action="index.php" method="GET">
            <input type="text" name="search" placeholder="Cari transaksi..." value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemesan</th>
                <th>Kendaraan</th>
                <th>Tanggal Sewa</th>
                <th>Tanggal Pengembalian</th>
                <th>Total Harga</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mencari transaksi berdasarkan pencarian
            $searchQuery = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $_GET['search'];
                $searchTerm = "%" . $search . "%"; // Wildcard untuk pencarian
                $searchQuery = " WHERE u.username LIKE ? OR k.nama LIKE ? OR t.status_pembayaran LIKE ?";
            }

            // Query untuk mengambil data transaksi dan pemesanan
            $query = "
                SELECT 
                    t.id AS transaksi_id,
                    u.username AS nama_pemesan,
                    k.nama AS kendaraan,
                    p.tanggal_sewa,
                    p.tanggal_pengembalian,
                    t.total_harga,
                    t.metode_pembayaran,
                    t.status_pembayaran,
                    k.harga AS harga_per_hari
                FROM transaksi t
                JOIN pemesan p ON t.pemesanan_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN kendaraan k ON p.kendaraan_id = k.id" . $searchQuery;

            $stmt = mysqli_prepare($conn, $query);
            if (isset($searchTerm)) {
                // Bind parameter untuk pencarian
                mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $searchTerm);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Menghitung durasi sewa dalam hari
                    $start_date = new DateTime($row['tanggal_sewa']);
                    $end_date = new DateTime($row['tanggal_pengembalian']);
                    $interval = $start_date->diff($end_date);
                    $days = $interval->days;

                    // Menghitung total harga berdasarkan durasi sewa
                    $total_harga = $row['harga_per_hari'] * $days;

                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_pemesan']}</td>
                        <td>{$row['kendaraan']}</td>
                        <td>{$row['tanggal_sewa']}</td>
                        <td>{$row['tanggal_pengembalian']}</td>
                        <td>Rp" . number_format($total_harga, 0, ',', '.') . "</td>
                        <td>{$row['metode_pembayaran']}</td>
                        <td>{$row['status_pembayaran']}</td>
                    </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='8'>Belum ada data transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>
</body>
</html>

<?php 
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan memiliki peran 'user'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

// Ambil input pencarian dari form, jika ada
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil kendaraan yang tersedia
$query_kendaraan = $conn->prepare("SELECT * FROM kendaraan WHERE status = 'Tersedia'");
$query_kendaraan->execute();
$result_kendaraan = $query_kendaraan->get_result();

// Query untuk mengambil kendaraan yang sudah disewa, ditambah tanggal pengembalian dan harga sewa dengan pencarian
$query_sewa = $conn->prepare("
    SELECT k.nama, k.merk, k.deskripsi, p.tanggal_sewa, p.tanggal_pengembalian, p.status_pembayaran, p.harga_sewa, p.id AS pemesanan_id
    FROM pemesan p
    JOIN kendaraan k ON p.kendaraan_id = k.id
    WHERE p.user_id = ? AND (k.nama LIKE ? OR p.status_pembayaran LIKE ?)
");
$searchTerm = "%" . $search . "%"; // Wildcard untuk pencarian
$query_sewa->bind_param("iss", $user_id, $searchTerm, $searchTerm);
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
    <style>
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
        <h3>Kelola kendaraan yang Anda sewa di sini:</h3>

        <!-- Form Pencarian -->
        <div class="search-container" style="text-align: center; margin-bottom: 20px;">
            <form action="dashboard.php" method="GET">
                <input type="text" name="search" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search); ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Kendaraan</th>
                    <th>Merk</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Sewa</th>
                    <th>Tanggal Pengembalian</th>
                    <th>Harga Sewa</th>
                    <th>Status Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_sewa && $result_sewa->num_rows > 0): ?>
                    <?php while ($row = $result_sewa->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
                            <td><?= htmlspecialchars($row['merk'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
                            <td><?= htmlspecialchars($row['deskripsi'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
                            <td><?= htmlspecialchars($row['tanggal_sewa'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
                            <td><?= htmlspecialchars($row['tanggal_pengembalian'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
                            <td>Rp <?= number_format($row['harga_sewa'] ?? 0, 2, ',', '.'); ?></td> <!-- Periksa nilai null -->
                            <td><?= htmlspecialchars($row['status_pembayaran'] ?? 'N/A'); ?></td> <!-- Periksa nilai null -->
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
                    <tr><td colspan="8">Belum ada kendaraan yang disewa.</td></tr>
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

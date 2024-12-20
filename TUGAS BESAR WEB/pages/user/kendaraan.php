<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

// Ambil input pencarian dari form, jika ada
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Jika ada pencarian, simpan kata kunci pencarian di cookie
if ($search) {
    setcookie('search_query', $search, time() + (86400 * 30), "/"); // Cookie disimpan selama 30 hari
} elseif (isset($_COOKIE['search_query'])) {
    // Jika tidak ada pencarian baru, gunakan nilai cookie jika ada
    $search = $_COOKIE['search_query'];
}

// Query untuk mengambil kendaraan yang tersedia dengan pencarian
$query = $conn->prepare("SELECT id, nama, harga, gambar, status FROM kendaraan WHERE status = 'Tersedia' AND nama LIKE ?");
$searchTerm = "%" . $search . "%"; // Menambahkan wildcard untuk pencarian
$query->bind_param("s", $searchTerm);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kendaraan - Rental Kendaraan</title>
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/style.css">
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
    <h1>Rental Kendaraan</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="kendaraan.php">Kendaraan</a>
        <a href="profil.php">Profil</a>
        <a href="../../logout.php">Logout</a>
    </nav>
</header>
<br>

<section class="kendaraan">
    <h2>Kendaraan yang Tersedia</h2>

    <!-- Form Pencarian -->
    <div class="search-container" style="text-align: center; margin-bottom: 20px;">
        <form action="kendaraan.php" method="GET">
            <input type="text" name="search" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="kendaraan-list">
        <?php
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
</div>
</body>
</html>

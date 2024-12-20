<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

// Ambil id kendaraan yang dipilih
if (isset($_GET['id'])) {
    $kendaraan_id = $_GET['id'];
} else {
    echo "<p>ID kendaraan tidak ditemukan!</p>";
    exit();
}

// Query untuk mengambil data kendaraan berdasarkan id
$query = "SELECT * FROM kendaraan WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $kendaraan_id);
$stmt->execute();
$result = $stmt->get_result();
$kendaraan = $result->fetch_assoc();

if (!$kendaraan) {
    echo "<p>Kendaraan tidak ditemukan!</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $tanggal_booking = $_POST['tanggal_booking'];
    $tanggal_pengembalian = $_POST['tanggal_pengembalian'];
    $user_id = $_SESSION['user_id'];

    // Menghitung jumlah hari peminjaman
    $start_date = new DateTime($tanggal_booking);
    $end_date = new DateTime($tanggal_pengembalian);
    $interval = $start_date->diff($end_date);
    $days = $interval->days; // Durasi peminjaman dalam hari

    // Harga sewa per hari
    $harga_per_hari = $kendaraan['harga'];
    $harga_sewa = $harga_per_hari * $days; // Total biaya sewa

    // Query untuk memasukkan data pemesanan
    $insert_query = "INSERT INTO pemesan (user_id, kendaraan_id, tanggal_sewa, tanggal_pengembalian, harga_sewa) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("iissd", $user_id, $kendaraan_id, $tanggal_booking, $tanggal_pengembalian, $harga_sewa);

    if ($stmt_insert->execute()) {
        // Update status kendaraan menjadi 'Disewa'
        $update_query = "UPDATE kendaraan SET status = 'Disewa' WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $kendaraan_id);
        $stmt_update->execute();

        echo "<p>Kendaraan berhasil dipesan!</p>";
    } else {
        echo "<p>Error: " . $stmt_insert->error . "</p>";
    }
}


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Kendaraan - Rental Kendaraan</title>
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        h2 {
            text-align: center;
        }
        .kendaraan-detail img {
            width: 100%;   /* Mengatur lebar gambar untuk memenuhi lebar layar */
            object-fit: cover; /* Memastikan gambar tetap proporsional dan tidak terdistorsi */
            display: block;  /* Menghapus jarak di bawah gambar */
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

    <section class="kendaraan-detail">
        <h2>Detail Kendaraan</h2><br>
        <div class="booking-form">
            <img src='../../assets/img/<?= $kendaraan['gambar'] ?>' alt='<?= $kendaraan['nama'] ?>' />
            <h3><?= $kendaraan['nama'] ?></h3>
            <p><strong>Merk:</strong> <?= $kendaraan['merk'] ?></p>
            <p><strong>Harga Sewa:</strong> Rp<?= number_format($kendaraan['harga'], 0, ',', '.') ?>/hari</p>
            <p><strong>Status:</strong> <?= $kendaraan['status'] ?></p>
            <form method="POST" action="">
                <label for="tanggal_booking">Tanggal Sewa</label>
                <?php
                    // Ambil tanggal hari ini untuk validasi
                    $current_date = date('Y-m-d');
                ?>
                <input type="date" name="tanggal_booking" min="<?= $current_date ?>" required>

                <label for="tanggal_pengembalian">Tanggal Pengembalian</label>
                <input type="date" name="tanggal_pengembalian" min="<?= $current_date ?>" required>

                <button type="submit">Booking</button>
            </form>
        </div><br>
    </section>

    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</div>
</body>
</html>

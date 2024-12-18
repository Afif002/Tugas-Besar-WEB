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
    <title>Booking Kendaraan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<header>
        <h1>Rental Kendaraan</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="kendaraan.php">Kendaraan</a>
            <a href="profil.php">Profil</a>
            <a href="../../logout.php">Logout</a>
        </nav>
    </header>

    <div class="booking-form">
        <h2>Booking Kendaraan</h2>
        <form method="POST" action="">
            <label for="kendaraan_id">Pilih Kendaraan</label>
            <select name="kendaraan_id" required>
                <?php
                // Menampilkan kendaraan yang tersedia
                $query = "SELECT * FROM kendaraan WHERE status = 'Tersedia'";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nama']} - {$row['merk']}</option>";
                    }
                } else {
                    echo "<option disabled>Belum ada kendaraan yang tersedia</option>";
                }
                ?>
            </select>

            <label for="tanggal_booking">Tanggal Sewa</label>
            <?php
                // Ambil tanggal hari ini untuk validasi
                $current_date = date('Y-m-d');
            ?>
            <input type="date" name="tanggal_booking" min="<?= $current_date ?>" required>

            <button type="submit">Booking</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $kendaraan_id = $_POST['kendaraan_id'];
    $tanggal_booking = $_POST['tanggal_booking'];
    $user_id = $_SESSION['user_id'];

    // Proses pemesanan
    $insert_query = "INSERT INTO pemesan (user_id, kendaraan_id, tanggal_sewa) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $user_id, $kendaraan_id, $tanggal_booking);

    if ($stmt->execute()) {
        // Update status kendaraan menjadi 'Disewa'
        $update_query = "UPDATE kendaraan SET status = 'Disewa' WHERE id = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("i", $kendaraan_id);
        $stmt_update->execute();

        echo "<p>Kendaraan berhasil dipesan!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
}
?>

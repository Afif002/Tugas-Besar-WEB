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

    // Ambil data kendaraan berdasarkan ID
    $query = "SELECT * FROM kendaraan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kendaraan = $result->fetch_assoc();

    if (!$kendaraan) {
        die("Kendaraan tidak ditemukan.");
    }

    // Update data kendaraan jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama = $_POST['nama'];
        $merk = $_POST['merk'];
        $harga = $_POST['harga'];
        $status = $_POST['status'];
        $deskripsi = $_POST['deskripsi'];

        // Cek apakah ada gambar baru yang diupload
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = $_FILES['gambar']['name'];
            $gambar_tmp = $_FILES['gambar']['tmp_name'];
            $gambar_path = "../../../assets/uploads/" . $gambar;
            move_uploaded_file($gambar_tmp, $gambar_path);

            // Update data kendaraan beserta gambar
            $update_query = "UPDATE kendaraan SET nama = ?, merk = ?, harga = ?, status = ?, deskripsi = ?, gambar = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssdsdsi", $nama, $merk, $harga, $status, $deskripsi, $gambar, $id);
        } else {
            // Update data kendaraan tanpa mengganti gambar
            $update_query = "UPDATE kendaraan SET nama = ?, merk = ?, harga = ?, status = ?, deskripsi = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssdsdi", $nama, $merk, $harga, $status, $deskripsi, $id);
        }

        if ($stmt->execute()) {
            $success = "Kendaraan berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui kendaraan.";
        }
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
    <title>Edit Kendaraan</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
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
</header>

<section class="form-container">
    <h2>Edit Kendaraan</h2>

    <?php if (isset($success)): ?>
        <div style="color: green;"><?= $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_kendaraan.php?id=<?= $kendaraan['id']; ?>" enctype="multipart/form-data">
        <label for="nama">Nama Kendaraan</label>
        <input type="text" id="nama" name="nama" value="<?= $kendaraan['nama']; ?>" required>

        <label for="merk">Merk Kendaraan</label>
        <input type="text" id="merk" name="merk" value="<?= $kendaraan['merk']; ?>" required>

        <label for="harga">Harga Sewa Per Hari</label>
        <input type="number" id="harga" name="harga" value="<?= $kendaraan['harga']; ?>" required>

        <label for="gambar">Gambar Kendaraan (Kosongkan jika tidak mengubah gambar)</label>
        <input type="file" id="gambar" name="gambar">

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="Tersedia" <?= $kendaraan['status'] == 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
            <option value="Disewa" <?= $kendaraan['status'] == 'Disewa' ? 'selected' : ''; ?>>Disewa</option>
        </select>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" required><?= $kendaraan['deskripsi']; ?></textarea>

        <button type="submit">Perbarui Kendaraan</button>
    </form>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>

</body>
</html>

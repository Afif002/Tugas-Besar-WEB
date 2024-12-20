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

        // Ambil deskripsi dari form, pastikan tidak kosong
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        // Debugging: Cek apakah deskripsi benar-benar diterima
        var_dump($deskripsi); // Pastikan deskripsi diterima dengan benar

        // Jika deskripsi kosong, beri nilai default
        if (empty($deskripsi)) {
            $deskripsi = 'Deskripsi tidak tersedia';
        }


        // Cek apakah ada gambar baru yang diupload
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = $_FILES['gambar']['name'];
            $gambar_tmp = $_FILES['gambar']['tmp_name'];
            $gambar_path = "../../../assets/img/" . $gambar;
            move_uploaded_file($gambar_tmp, $gambar_path);
    
            // Update data kendaraan beserta gambar
            $update_query = "UPDATE kendaraan SET nama = ?, merk = ?, harga = ?, status = ?, deskripsi = ?, gambar = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssisssi", $nama, $merk, $harga, $status, $deskripsi, $gambar, $id);

        } else {
            $update_query = "UPDATE kendaraan SET nama = ?, merk = ?, harga = ?, status = ?, deskripsi = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssi", $nama, $merk, $harga, $status, $deskripsi, $id);
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
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="tambah.php">Kembali</a>
    </nav>
</header>

<section class="form-container">
    <h2>Edit Kendaraan</h2>

    <form method="POST" action="edit_kendaraan.php?id=<?= $kendaraan['id']; ?>" enctype="multipart/form-data">
        <label for="nama">Nama Kendaraan</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($kendaraan['nama']); ?>" required>

        <label for="merk">Merk Kendaraan</label>
        <input type="text" id="merk" name="merk" value="<?= htmlspecialchars($kendaraan['merk']); ?>" required>

        <label for="harga">Harga Sewa Per Hari</label>
        <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($kendaraan['harga']); ?>" required>

        <label for="gambar">Gambar Kendaraan (Kosongkan jika tidak mengubah gambar)</label>
        <input type="file" id="gambar" name="gambar">

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="Tersedia" <?= $kendaraan['status'] == 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
            <option value="Disewa" <?= $kendaraan['status'] == 'Disewa' ? 'selected' : ''; ?>>Disewa</option>
        </select>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($kendaraan['deskripsi']); ?></textarea>

        <button type="submit">Perbarui Kendaraan</button><br>

        <?php if (isset($success)): ?>
        <div style="color: green;"><?= $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div style="color: red;"><?= $error; ?></div>
        <?php endif; ?>
    </form>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>

</body>
</html>

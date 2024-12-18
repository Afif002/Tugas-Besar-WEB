<?php
session_start();
include '../../../config/database.php';

// Pastikan pengguna admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

// Query untuk mengambil data kendaraan yang tersedia
$query = "SELECT * FROM kendaraan WHERE status = 'Tersedia'";
$result = mysqli_query($conn, $query);
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
<div class="wrapper">
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
<br>
<section class="form-container">
    <h2>Tambah Kendaraan</h2>
    <form method="POST" action="proses_tambah.php" enctype="multipart/form-data">
        <label for="nama">Nama Kendaraan</label>
        <input type="text" id="nama" name="nama" required>

        <label for="merk">Merk Kendaraan</label>
        <input type="text" id="merk" name="merk" required>

        <label for="harga">Harga Sewa Per Hari</label>
        <input type="number" id="harga" name="harga" required>

        <label for="gambar">Gambar Kendaraan</label>
        <input type="file" id="gambar" name="gambar" required>

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="Tersedia">Tersedia</option>
            <option value="Disewa">Disewa</option>
        </select>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" required></textarea>

        <button type="submit">Tambah Kendaraan</button>
    </form>
</section>
<br><br>
<section class="admin-list">
    <h2>Kendaraan yang Tersedia</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kendaraan</th>
                    <th>Merk</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($kendaraan = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $kendaraan['id']; ?></td>
                        <td><?= $kendaraan['nama']; ?></td>
                        <td><?= $kendaraan['merk']; ?></td>
                        <td>Rp <?= number_format($kendaraan['harga'], 0, ',', '.'); ?></td>
                        <td><?= $kendaraan['status']; ?></td>
                        <td><?= substr($kendaraan['deskripsi'], 0, 50); ?>...</td>
                        <td class="action-buttons">
                            <a href="edit_kendaraan.php?id=<?= $kendaraan['id']; ?>">Edit</a> |
                            <a href="hapus_kendaraan.php?id=<?= $kendaraan['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada kendaraan yang tersedia.</p>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>

</body>
</html>
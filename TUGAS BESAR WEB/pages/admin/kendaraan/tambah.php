<?php
session_start();
include '../../../config/database.php';

// Pastikan pengguna admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

// Tangani pencarian kendaraan
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM kendaraan WHERE nama LIKE '%$search%' OR merk LIKE '%$search%' OR deskripsi LIKE '%$search%'";
} else {
    $query = "SELECT * FROM kendaraan";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kendaraan</title>
    <link rel="icon" href="../../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        h2 {
            text-align: center;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
            margin-left: 70px;
        }
        .search-bar input[type="text"] {
            width: 300px;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #0056b3;
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
<main>
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
    <h2>Daftar Kendaraan</h2><br>
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Cari kendaraan..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
    </div>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <!-- Kendaraan Tersedia -->
        <h2>Kendaraan Tersedia</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Kendaraan</th>
                    <th>Merk</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($result, 0); // Reset pointer hasil query
                while ($kendaraan = mysqli_fetch_assoc($result)):
                    if ($kendaraan['status'] === 'Tersedia'):
                ?>
                    <tr>
                        <td><?= $kendaraan['id']; ?></td>
                        <td>
                            <img src="../../../assets/img/<?= $kendaraan['gambar']; ?>" 
                                 alt="<?= $kendaraan['nama']; ?>" 
                                 style="width: 100px; height: auto; border-radius: 8px;">
                        </td>
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
                <?php endif; endwhile; ?>
            </tbody>
        </table><br>
        
        <!-- Kendaraan Disewa -->
        <h2>Kendaraan Disewa</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Kendaraan</th>
                    <th>Merk</th>
                    <th>Harga Sewa</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                mysqli_data_seek($result, 0); // Reset pointer hasil query
                while ($kendaraan = mysqli_fetch_assoc($result)):
                    if ($kendaraan['status'] === 'Disewa'):
                ?>
                    <tr>
                        <td><?= $kendaraan['id']; ?></td>
                        <td>
                            <img src="../../../assets/img/<?= $kendaraan['gambar']; ?>" 
                                 alt="<?= $kendaraan['nama']; ?>" 
                                 style="width: 100px; height: auto; border-radius: 8px;">
                        </td>
                        <td><?= $kendaraan['nama']; ?></td>
                        <td><?= $kendaraan['merk']; ?></td>
                        <td>Rp <?= number_format($kendaraan['harga'], 0, ',', '.'); ?></td>
                        <td><?= $kendaraan['status']; ?></td>
                        <td><?= substr($kendaraan['deskripsi'], 0, 50); ?>...</td>
                        <td class="action-buttons">
                            <a href="edit_kendaraan.php?id=<?= $kendaraan['id']; ?>">Edit</a>
                        </td>
                    </tr>
                <?php endif; endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada kendaraan yang tersedia.</p>
    <?php endif; ?>
</section>

</main>
<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>

</body>
</html>

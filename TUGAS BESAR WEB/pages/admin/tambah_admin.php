<?php
// Mulai sesi
session_start();
include '../../config/database.php'; // Pastikan file koneksi database sudah benar

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // Enkripsi password menggunakan MD5

    // Query untuk mengecek apakah username sudah ada
    $checkQuery = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika username sudah ada, beri pesan error
    if (mysqli_num_rows($result) > 0) {
        $error = "Username sudah digunakan, coba yang lain!";
    } else {
        // Query untuk menambah pengguna baru dengan role 'admin'
        $insertQuery = "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')";
        $stmt = mysqli_prepare($conn, $insertQuery);
        
        // Hanya bind dua parameter: username dan password
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);

        // Jika query berhasil, beri pesan sukses dan redirect
        if (mysqli_stmt_execute($stmt)) {
            $success = "Pengguna admin berhasil ditambahkan!";
            header("Location: tambah_admin.php"); // Reload halaman agar tabel terupdate
            exit();
        } else {
            $error = "Gagal menambahkan pengguna: " . mysqli_error($conn);
        }
    }
}

// Query untuk mengambil semua pengguna dengan role 'admin'
$query = "SELECT * FROM users WHERE role = 'admin'";
$result = mysqli_query($conn, $query);
?>

<!-- Tampilan Form Tambah Pengguna Admin dengan Pesan Error dan Daftar Pengguna Admin -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="tambah_admin.php">Tambah Admin</a>
        <a href="kendaraan/tambah.php">Tambah Kendaraan</a>
        <a href="transaksi/index.php">Kelola Transaksi</a>
        <a href="../../logout.php">Logout</a>
    </nav>
</header><br>
    <div class="form-container">
        <h2>Tambah Pengguna Admin</h2>
        <?php if (isset($error)): ?>
            <div style="color: red;"><?= $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div style="color: green;"><?= $success; ?></div>
        <?php endif; ?>
        <form action="tambah_admin.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Tambah Pengguna</button>
        </form>
    </div><br>

    <div class="admin-list">
        <h2>Daftar Pengguna Admin</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $user['id']; ?></td>
                            <td><?= $user['username']; ?></td>
                            <td>
                                <!-- Tombol untuk menghapus pengguna admin -->
                                <a href="hapus_admin.php?id=<?= $user['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada pengguna admin.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>

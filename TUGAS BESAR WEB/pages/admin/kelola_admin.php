<?php
// Mulai sesi
session_start();
include '../../config/database.php'; // Pastikan file koneksi database sudah benar

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Gunakan password_hash untuk enkripsi yang lebih aman

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
        
        // Bind dua parameter: username dan password
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);

        // Jika query berhasil, beri pesan sukses dan redirect
        if (mysqli_stmt_execute($stmt)) {
            $success = "Pengguna admin berhasil ditambahkan!";
            header("Location: kelola_admin.php"); // Pastikan tidak ada output sebelum header
            exit();  // Menghentikan eksekusi lebih lanjut
        } else {
            $error = "Gagal menambahkan pengguna: " . mysqli_error($conn);
        }
    }
}

// Query untuk mencari pengguna admin berdasarkan username
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $searchTerm = "%" . $search . "%"; // Wildcard untuk pencarian
    $searchQuery = " WHERE role = 'admin' AND username LIKE ?";
} else {
    $searchQuery = " WHERE role = 'admin'"; // Menampilkan semua admin jika tidak ada pencarian
}

// Query untuk mengambil semua pengguna dengan role 'admin' dan filter pencarian
$query = "SELECT * FROM users" . $searchQuery;
$stmt = mysqli_prepare($conn, $query);
if (isset($searchTerm)) {
    mysqli_stmt_bind_param($stmt, "s", $searchTerm);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($conn)); // Menangani error query
}
?>

<!-- Tampilan Form Tambah Pengguna Admin dengan Pesan Error dan Daftar Pengguna Admin -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna Admin</title>
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
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="kelola_admin.php">Kelola Admin</a>
        <a href="kelola_pengguna.php">Kelola Pengguna</a>
        <a href="kendaraan/tambah.php">Tambah Kendaraan</a>
        <a href="transaksi/index.php">Kelola Transaksi</a>
        <a href="../../logout.php">Logout</a>
    </nav>
</header><br>
<main>
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
        <!-- Form Pencarian Admin -->
    <div class="search-container">
        <form action="kelola_admin.php" method="GET">
            <input type="text" name="search" placeholder="Cari berdasarkan username" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit">Cari</button>
        </form>
    </div>
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
                                <a href="edit_admin.php?id=<?= $user['id']; ?>">Edit</a> | 
                                <a href="hapus_admin.php?id=<?= $user['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada pengguna admin yang ditemukan.</p>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>
</body>
</html>

<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan berstatus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Query untuk mendapatkan semua pengguna yang bukan admin
$query = "SELECT * FROM users WHERE role != 'admin'";
$result = mysqli_query($conn, $query);

// Hapus pengguna jika ada permintaan
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Cek jika pengguna yang akan dihapus adalah admin
    $checkRoleQuery = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($checkRoleQuery);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();

    if ($role === 'admin') {
        $error = "Anda tidak dapat menghapus pengguna dengan peran admin!";
    } else {
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $success = "Pengguna berhasil dihapus!";
        } else {
            $error = "Gagal menghapus pengguna.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .action-buttons a {
            margin: 0 5px;
            color: #007bff;
            text-decoration: none;
        }

        .action-buttons a:hover {
            text-decoration: underline;
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

<div class="admin-list">
    <h2>Kelola Pengguna</h2>
    <?php if (isset($success)): ?>
        <div style="color: green;"><?= $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= $error; ?></div>
    <?php endif; ?>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= $user['username']; ?></td>
                        <td><?= $user['role']; ?></td>
                        <td class="action-buttons">
                            <a href="edit_pengguna.php?id=<?= $user['id']; ?>">Edit</a>
                            <a href="kelola_pengguna.php?delete_id=<?= $user['id']; ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada pengguna yang terdaftar.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</div>
</body>
</html>

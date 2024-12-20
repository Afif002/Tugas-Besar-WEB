<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan berstatus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Tambah pengguna baru jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));  // Enkripsi password
    $role = $_POST['role'];  // Menetapkan role pengguna
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Query untuk menambahkan pengguna baru
    $insertQuery = "INSERT INTO users (username, password, role, email, phone, address) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $username, $password, $role, $email, $phone, $address);

    if ($stmt->execute()) {
        $success = "Pengguna berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan pengguna.";
    }
}

// Proses pencarian pengguna berdasarkan username dan email
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    // Menambahkan wildcards % untuk pencarian
    $searchTerm = "%" . $search . "%";
    $searchQuery = " AND (username LIKE ? OR email LIKE ?)";
}

// Query untuk mendapatkan semua pengguna yang bukan admin
$query = "SELECT * FROM users WHERE role != 'admin' $searchQuery";
$stmt = $conn->prepare($query);

if (!empty($searchQuery)) {
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

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
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
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

        .form-container {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
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
    <h2>Tambah Pengguna</h2>
    <?php if (isset($success)): ?>
        <div style="color: green;"><?= $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= $error; ?></div>
    <?php endif; ?>

    <form action="kelola_pengguna.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user">User</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" required></textarea>
        </div>
        <button type="submit">Tambah Pengguna</button>
    </form>
</div>

<!-- Form Pencarian Pengguna -->
<div class="search-container">
    <h2>Cari Pengguna</h2><br>
    <form action="kelola_pengguna.php" method="GET">
        <input type="text" name="search" placeholder="Cari berdasarkan username atau email" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <button type="submit">Cari</button>
    </form>
</div>

<div class="pengguna-list">
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
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= $user['username']; ?></td>
                        <td><?= $user['role']; ?></td>
                        <td><?= $user['email']; ?></td>
                        <td><?= $user['phone']; ?></td>
                        <td><?= $user['address']; ?></td>
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
</main>
</div>
</body>
</html>

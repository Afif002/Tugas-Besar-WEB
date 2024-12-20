<?php
session_start();
include '../../config/database.php';

// Pastikan pengguna sudah login dan berstatus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Cek apakah id pengguna ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data pengguna berdasarkan ID
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Pengguna tidak ditemukan.");
    }

    // Jika form disubmit, update data pengguna
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $role = 'user'; // Set role selalu menjadi 'user'
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        // Jika password diubah
        $password = !empty($_POST['password']) ? md5(trim($_POST['password'])) : $user['password'];

        // Query untuk update pengguna
        $updateQuery = "UPDATE users SET username = ?, password = ?, role = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssi", $username, $password, $role, $email, $phone, $address, $id);

        if ($stmt->execute()) {
            $success = "Pengguna berhasil diperbarui!";
            header("Location: kelola_pengguna.php"); // Redirect setelah update berhasil
            exit();
        } else {
            $error = "Gagal memperbarui pengguna.";
        }
    }
} else {
    die("ID pengguna tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="wrapper">
<header>
    <h1>Rental Kendaraan - Admin</h1>
    <nav>
        <a href="kelola_pengguna.php">Kembali</a>
    </nav>
</header><br>
<main>
    <div class="form-container">
        <h2>Edit Pengguna</h2>

        <?php if (isset($error)): ?>
            <div style="color: red;"><?= $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div style="color: green;"><?= $success; ?></div>
        <?php endif; ?>

        <form action="edit_pengguna.php?id=<?= $user['id']; ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= $user['username']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengganti password)</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?= $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= $user['phone']; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" required><?= $user['address']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <!-- Hanya tampilkan role user -->
                <select id="role" name="role" required disabled>
                    <option value="user" selected>user</option>
                </select>
            </div>
            <button type="submit">Perbarui Pengguna</button>
        </form>
    </div><br>
    </main>
    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</div>
</body>
</html>

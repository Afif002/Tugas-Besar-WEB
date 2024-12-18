<?php
session_start();
include '../../config/database.php'; // Pastikan file koneksi database sudah benar

// Pastikan ID tersedia
if (!isset($_GET['id'])) {
    header("Location: tambah_admin.php");
    exit();
}

$id = $_GET['id'];

// Ambil data admin berdasarkan ID
$query = "SELECT * FROM users WHERE id = ? AND role = 'admin'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Admin tidak ditemukan!";
    exit();
}

// Update data admin jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = !empty($_POST['password']) ? md5(trim($_POST['password'])) : $user['password']; // Gunakan password lama jika kosong

    $updateQuery = "UPDATE users SET username = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssi", $username, $password, $id);

    if ($stmt->execute()) {
        header("Location: tambah_admin.php");
        exit();
    } else {
        $error = "Gagal mengupdate data admin: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<header>
    <h1>Edit Pengguna Admin</h1>
    <nav>
        <a href="tambah_admin.php">Kembali</a>
    </nav>
</header>

<div class="form-container">
    <h2>Edit Pengguna Admin</h2>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= $error; ?></div>
    <?php endif; ?>
    <form action="edit_admin.php?id=<?= $id; ?>" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit">Simpan Perubahan</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
</footer>
</body>
</html>

<?php
// Mulai sesi
session_start();
include '../../config/database.php'; // Pastikan file koneksi database sudah benar

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID user dari sesi
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data user
$query = "SELECT username, email, phone, address FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Jika data user tidak ditemukan
if (mysqli_num_rows($result) === 0) {
    echo "User tidak ditemukan!";
    exit();
}

// Ambil data user
$user = mysqli_fetch_assoc($result);

// Update profil jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $updateQuery = "UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssi", $email, $phone, $address, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Profil berhasil diperbarui!";
        // Refresh data setelah update
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['address'] = $address;
    } else {
        $error = "Gagal memperbarui profil: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            font-size: 14px;
        }

        .form-group textarea {
            resize: none;
            height: 100px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .table-container {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007BFF;
            color: #fff;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>
<header>
        <h1>Rental Kendaraan</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="kendaraan.php">Kendaraan</a>
            <a href="profil.php">Profil</a>
            <a href="../../logout.php">Logout</a>
        </nav>
    </header>
    <div class="container">
        <h2>Profil Saya</h2>
        <?php if (isset($success)): ?>
            <div class="message success"><?= $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error; ?></div>
        <?php endif; ?>
        <form action="profil.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="<?= $user['username']; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= $user['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" value="<?= $user['phone']; ?>">
            </div>
            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea id="address" name="address"><?= $user['address']; ?></textarea>
            </div>
            <button type="submit">Simpan Perubahan</button>
        </form>

        <div class="table-container">
            <h3>Detail Profil</h3>
            <table>
                <tr>
                    <th>Field</th>
                    <th>Data</th>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><?= $user['username']; ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= $user['email']; ?></td>
                </tr>
                <tr>
                    <td>Nomor Telepon</td>
                    <td><?= $user['phone']; ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td><?= $user['address']; ?></td>
                </tr>
            </table>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>

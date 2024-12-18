<?php
session_start();
include 'config/database.php';

// Cek koneksi database
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Inisialisasi variabel error
$error = '';
$success = '';

// Proses pendaftaran
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // Gunakan MD5 sesuai contoh insert Anda
    $role = 'user'; // Default role sebagai user

    // Periksa apakah username sudah terdaftar
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Query untuk menyimpan pengguna baru
            $insert_query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($conn, $insert_query);
            if ($insert_stmt) {
                mysqli_stmt_bind_param($insert_stmt, "sss", $username, $password, $role);
                if (mysqli_stmt_execute($insert_stmt)) {
                    $success = "Pendaftaran berhasil! Silakan login.";
                } else {
                    $error = "Terjadi kesalahan saat mendaftar.";
                }
            } else {
                $error = "Kesalahan pada query: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "Kesalahan pada query: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Reset dan Pengaturan Dasar */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Register Form Container */
.login-container {
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    padding: 40px;
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.login-container h2 {
    font-size: 2.4rem;
    margin-bottom: 20px;
    color: #333;
}

/* Error and Success Message */
.error-message, .success-message {
    margin-bottom: 20px;
    font-size: 1.1rem;
}

.error-message {
    color: red;
}

.success-message {
    color: green;
}

/* Form Group */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 1.1rem;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: border 0.3s;
}

.form-group input:focus {
    border: 1px solid #007bff;
}

button {
    width: 100%;
    padding: 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1.2rem;
    transition: background-color 0.3s;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

button:active {
    background-color: #004085;
}

/* Responsive */
@media (max-width: 480px) {
    .login-container {
        padding: 20px;
    }
}

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Daftar Akun</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?= htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">
                <p><?= htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>

        <!-- Link ke halaman Login jika pengguna sudah punya akun -->
        <p>Sudah punya akun? <a href="login.php">Login Sekarang</a></p>
    </div>
</body>
</html>

<?php
session_start();
include 'config/database.php';

// Cek koneksi database
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Jika pengguna sudah login, arahkan ke halaman sesuai peran
if (isset($_SESSION['username'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: pages/admin/dashboard.php");
    } else {
        header("Location: pages/user/dashboard.php");
    }
    exit();
}

// Inisialisasi variabel error
$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Gunakan prepared statement untuk mencegah SQL injection
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password'])); // Gunakan MD5 sesuai contoh insert Anda
    $role = trim($_POST['role']);

    // Query untuk mendapatkan data pengguna berdasarkan username, password, dan role
    $query = "SELECT * FROM users WHERE username = ? AND password = ? AND role = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameter dan eksekusi query
        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Periksa apakah ada hasil yang ditemukan
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Set session data
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            // Arahkan pengguna sesuai peran
            if ($user['role'] === 'admin') {
                header("Location: pages/admin/dashboard.php");
            } else {
                header("Location: pages/user/dashboard.php");
            }
            exit();
        } else {
            $error = "Kombinasi username, password, atau role salah!";
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
    <title>Login</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
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

/* Login Form Container */
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

/* Error Message */
.error-message {
    color: red;
    margin-bottom: 20px;
    font-size: 1.1rem;
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

.form-group input,option  {
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
/* Gaya untuk input teks dan dropdown */
.form-group input, 
.form-group select {
    width: 100%;
    padding: 12px;
    font-size: 1.1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    transition: border 0.3s;
    appearance: none; /* Hilangkan panah bawaan browser pada select */
}

.form-group input:focus, 
.form-group select:focus {
    border: 1px solid #007bff;
}

/* Dropdown panah bawaan browser */
.form-group select {
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px 16px;
}

    </style>
</head>
<body>
<div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?= htmlspecialchars($error); ?></p>
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
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <p>Belum punya akun? <a href="register.php">Daftar Sekarang</a></p><br>
        <p class="back-to-main"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>

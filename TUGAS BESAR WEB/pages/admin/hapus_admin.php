<?php
include '../../config/database.php';

// Ambil ID pengguna dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus pengguna berdasarkan ID
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Eksekusi query dan beri pesan jika berhasil atau gagal
    if (mysqli_stmt_execute($stmt)) {
        header("Location: kelola_admin.php"); // Redirect kembali setelah menghapus
        exit();
    } else {
        echo "Gagal menghapus pengguna: " . mysqli_error($conn);
    }
} else {
    echo "ID pengguna tidak ditemukan!";
}
?>

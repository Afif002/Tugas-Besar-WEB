<?php
include '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama = trim($_POST['nama']);
    $merk = trim($_POST['merk']);
    $harga = trim($_POST['harga']);
    $status = trim($_POST['status']);
    $deskripsi = trim($_POST['deskripsi']);

    // Memastikan nilai status adalah salah satu dari pilihan yang ada
    $valid_status = ['Tersedia', 'Disewa'];
    if (!in_array($status, $valid_status)) {
        die("Status tidak valid!");
    }

    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $gambar_folder = "../../../assets/img/" . $gambar;
    move_uploaded_file($gambar_tmp, $gambar_folder);

    // Query untuk memasukkan data kendaraan ke dalam tabel
    $query = "INSERT INTO kendaraan (nama, merk, harga, status, gambar, deskripsi) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssisss", $nama, $merk, $harga, $status, $gambar, $deskripsi);

    if (mysqli_stmt_execute($stmt)) {
        echo "Kendaraan berhasil ditambahkan!";
    } else {
        echo "Gagal menambahkan kendaraan: " . mysqli_error($conn);
    }
}
?>

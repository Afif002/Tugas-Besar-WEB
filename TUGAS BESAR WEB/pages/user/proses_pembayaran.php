<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login.";
    exit();
}

if (!isset($_POST['pemesanan_id']) || empty($_POST['pemesanan_id'])) {
    echo "ID Pemesanan tidak valid.";
    exit();
}

$pemesanan_id = intval($_POST['pemesanan_id']);

// Cek apakah pemesanan_id valid di tabel pemesan
$query_check_pemesanan = $conn->prepare("SELECT id FROM pemesan WHERE id = ?");
$query_check_pemesanan->bind_param("i", $pemesanan_id);
$query_check_pemesanan->execute();
$result_check_pemesanan = $query_check_pemesanan->get_result();

if ($result_check_pemesanan->num_rows === 0) {
    echo "Pemesanan tidak ditemukan di database.";
    exit();
}

// Ambil detail pemesanan
$query_pemesan = $conn->prepare("
    SELECT k.harga, p.tanggal_sewa, u.id AS user_id
    FROM pemesan p
    JOIN kendaraan k ON p.kendaraan_id = k.id
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$query_pemesan->bind_param("i", $pemesanan_id);
$query_pemesan->execute();
$result_pemesan = $query_pemesan->get_result();

if ($result_pemesan->num_rows === 0) {
    echo "Pemesanan tidak ditemukan.";
    exit();
}

$pemesanan_data = $result_pemesan->fetch_assoc();

// Menentukan total harga dan status pembayaran
$total_harga = $pemesanan_data['harga']; // Misalnya harga per hari
$metode_pembayaran = 'Transfer'; // Sesuaikan dengan logika Anda
$status_pembayaran = 'Sudah Dibayar'; // Pastikan status pembayaran valid

// Tambahkan data transaksi
$query_transaksi = $conn->prepare("
    INSERT INTO transaksi (pemesanan_id, total_harga, metode_pembayaran, status_pembayaran) 
    VALUES (?, ?, ?, ?)
");
$query_transaksi->bind_param("iiss", $pemesanan_id, $total_harga, $metode_pembayaran, $status_pembayaran);

if ($query_transaksi->execute()) {
    // Perbarui status pembayaran pada tabel pemesan menjadi 'Lunas'
    $query_update_status = $conn->prepare("UPDATE pemesan SET status_pembayaran = 'Lunas' WHERE id = ?");
    $query_update_status->bind_param("i", $pemesanan_id);

    if ($query_update_status->execute()) {
        echo "Transaksi berhasil ditambahkan dan status pembayaran diperbarui!";
    } else {
        echo "Gagal memperbarui status pembayaran di tabel pemesan.";
    }
} else {
    echo "Gagal menambahkan transaksi: " . $conn->error;
}
?>

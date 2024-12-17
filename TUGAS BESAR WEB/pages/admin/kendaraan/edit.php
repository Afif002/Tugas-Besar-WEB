<?php
include '../../../includes/header.php';

// Memastikan ID kendaraan valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM kendaraan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $kendaraan = mysqli_fetch_assoc($result);
} else {
    echo "ID tidak valid!";
    exit();
}
?>

<section class="form-container">
    <h2>Edit Kendaraan</h2>
    <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $kendaraan['id']; ?>">

        <label for="nama">Nama Kendaraan</label>
        <input type="text" id="nama" name="nama" value="<?= $kendaraan['nama']; ?>" required>

        <label for="merk">Merk Kendaraan</label>
        <input type="text" id="merk" name="merk" value="<?= $kendaraan['merk']; ?>" required>

        <label for="harga">Harga Sewa Per Hari</label>
        <input type="number" id="harga" name="harga" value="<?= $kendaraan['harga']; ?>" required>

        <label for="gambar">Gambar Kendaraan</label>
        <input type="file" id="gambar" name="gambar">

        <button type="submit">Simpan Perubahan</button>
    </form>
</section>

<?php include '../../../includes/footer.php'; ?>

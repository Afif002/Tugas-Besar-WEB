<?php
include '../../../config/database.php';
$id_transaksi = $_GET['id'];

$query = "UPDATE transaksi SET status = 'confirmed' WHERE id = $id_transaksi";
if (mysqli_query($conn, $query)) {
    header("Location: index.php?status=confirmed");
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

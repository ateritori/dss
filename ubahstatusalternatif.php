<?php
// Koneksi ke database
require 'config/koneksi.php';

$id_alternatif = $_GET['id'];
$status_alternatif = $_GET['status'];

// Toggle status: Jika aktif (1), ubah jadi non aktif (0), sebaliknya jika non aktif, ubah jadi aktif
$status_baru = ($status_alternatif == 1) ? 0 : 1;

$sql = "UPDATE Alternatif SET status_alternatif = '$status_baru' WHERE id_alternatif = '$id_alternatif'";
if (mysqli_query($conn, $sql)) {
    // Redirect kembali ke halaman dashboard atau halaman yang diinginkan setelah update
    header("Location: dashboard.php?url=alternatif");
    exit();
} else {
    echo "Error updating record: " . mysqli_error($conn);
}

<?php
require 'config/koneksi.php';

if (isset($_GET['status'])) {
    $status = $_GET['status'];

    // Set status berdasarkan permintaan
    $status_baru = ($status == 'aktif') ? 1 : 0;

    // Update status di database
    $sql = "UPDATE Alternatif SET status_alternatif = '$status_baru'";
    if (mysqli_query($conn, $sql)) {
        // Redirect kembali ke halaman alternatif
        header("Location: dashboard.php?url=alternatif&update=success");
        exit();
    } else {
        echo "Error updating records: " . mysqli_error($conn);
    }
}

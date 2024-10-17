<?php
session_start();
require 'config/koneksi.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda Belum Login'); window.location='index.php';</script>";
}

// Cek apakah parameter id telah diberikan melalui URL
if (isset($_GET['id'])) {
    $id_rentang = $_GET['id'];

    // Proses penghapusan data rentang berdasarkan ID
    $delete = mysqli_query($conn, "DELETE FROM Rentang WHERE id_rentang = '$id_rentang'");

    if ($delete) {
        echo "<script>alert('Data berhasil dihapus'); window.location='dashboard.php?url=rentang';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location='dashboard.php?url=rentang';</script>";
    }
} else {
    echo "<script>alert('ID rentang tidak ditemukan'); window.location='dashboard.php?url=rentang';</script>";
}

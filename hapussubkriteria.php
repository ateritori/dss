<?php
require 'config/koneksi.php';
$id = $_GET['id'];

$sql = mysqli_query($conn, "delete from SubKriteria where id_subkriteria='$id' ");
if ($sql) {
    // Jika berhasil
    echo "<script>alert('Data Berhasil Dihapus'); window.location = 'dashboard.php?url=kriteria';</script>";
} else {
    // Jika gagal, tampilkan alert dengan pesan kesalahan
    echo "<script>alert('Data Gagal Dihapus. Pastikan data tidak terkait dengan tabel lain.'); window.location = 'dashboard.php?url=kriteria';</script>";
}

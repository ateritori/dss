<?php
require 'config/koneksi.php';
$id = $_GET['id'];

// Eksekusi query delete
$sql = mysqli_query($conn, "DELETE FROM kriteria WHERE id_kriteria='$id'");

// Mengecek apakah query berhasil atau tidak
if ($sql) {
	// Jika berhasil
	echo "<script>alert('Data Berhasil Dihapus'); window.location = 'dashboard.php?url=kriteria';</script>";
} else {
	// Jika gagal, tampilkan alert dengan pesan kesalahan
	echo "<script>alert('Data Gagal Dihapus. Pastikan data tidak terkait dengan tabel lain.'); window.location = 'dashboard.php?url=kriteria';</script>";
}

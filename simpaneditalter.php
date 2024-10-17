<?php
session_start(); // Memulai session

if (!isset($_SESSION['username'])) {
	echo "<script>alert('Anda Belum Login'); window.location = 'index.php';</script>";
	exit();
}

// Mengimpor file koneksi
require 'config/koneksi.php';

// Mengambil data dari form
$id_alternatif = $_POST['id_alternatif'];
$nama = $_POST['nama'];
$status_alternatif = $_POST['status_alternatif'];

// Memastikan semua data terisi
if (empty($id_alternatif) || empty($nama) || !isset($status_alternatif)) {
	echo "<script>alert('Semua field harus diisi.'); window.location = 'dashboard.php?url=editalternatif&id=$id_alternatif';</script>";
	exit();
}

// Menyiapkan query untuk memperbarui data alternatif
$sql = "UPDATE alternatif SET 
            nama_alternatif = ?, 
            status_alternatif = ? 
        WHERE id_alternatif = ?";

// Menyiapkan statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $nama, $status_alternatif, $id_alternatif);

// Mengeksekusi statement
if ($stmt->execute()) {
	echo "<script>alert('Data alternatif berhasil diperbarui.'); window.location = 'dashboard.php?url=alternatif';</script>";
} else {
	echo "<script>alert('Gagal memperbarui data alternatif.'); window.location = 'dashboard.php?url=editalternatif&id=$id_alternatif';</script>";
}

// Menutup statement dan koneksi
$stmt->close();
$conn->close();

<?php
require 'config/koneksi.php';

$id = $_POST['id_kriteria'];
$kriteria = $_POST['kriteria'];
$bobot = isset($_POST['bobot']) && $_POST['bobot'] !== "" ? $_POST['bobot'] : NULL;
$atribut = isset($_POST['atribut']) && $_POST['atribut'] !== "" ? $_POST['atribut'] : NULL;

// Menggunakan prepared statements
$query = "UPDATE Kriteria SET nama_kriteria = ?, bobot_kriteria = ?, tipe_kriteria = ? WHERE id_kriteria = ?";
$stmt = $conn->prepare($query);

// Bind parameters (isi dengan nilai-nilai yang diterima dari form)
$stmt->bind_param("sdsi", $kriteria, $bobot, $atribut, $id);

// Eksekusi query
if ($stmt->execute()) {
	echo "<script>alert('Data Berhasil Diubah'); window.location = 'dashboard.php?url=kriteria';</script>";
} else {
	echo "<script>alert('Gagal Mengubah Data'); window.location = 'dashboard.php?url=kriteria';</script>";
}

// Tutup statement
$stmt->close();

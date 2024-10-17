<?php
require 'config/koneksi.php';

$id_subkriteria = $_POST['id_subkriteria'];
$id_kriteria = $_POST['id_kriteria'];
$subkriteria = $_POST['subkriteria'];
$bobotsub = isset($_POST['bobotsub']) && $_POST['bobotsub'] !== "" ? $_POST['bobotsub'] : NULL;
$atribut = isset($_POST['atribut']) && $_POST['atribut'] !== "" ? $_POST['atribut'] : NULL;

// Menggunakan prepared statements
$query = "UPDATE SubKriteria SET id_kriteria = ?,  nama_subkriteria = ?, bobot_subkriteria = ?, tipe_subkriteria = ? WHERE id_subkriteria = ?";
$stmt = $conn->prepare($query);

// Bind parameters (isi dengan nilai-nilai yang diterima dari form)
$stmt->bind_param("isdsi", $id_kriteria, $subkriteria, $bobotsub, $atribut, $id_subkriteria);

// Eksekusi query
if ($stmt->execute()) {
    echo "<script>alert('Data Berhasil Diubah'); window.location = 'dashboard.php?url=kriteria';</script>";
} else {
    echo "<script>alert('Gagal Mengubah Data'); window.location = 'dashboard.php?url=kriteria';</script>";
}

// Tutup statement
$stmt->close();

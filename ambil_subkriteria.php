<?php
// ambil_subkriteria.php
require 'config/koneksi.php';

$id_kriteria = $_POST['id_kriteria'];

// Ambil semua sub-kriteria berdasarkan id_kriteria
$sqlSubkriteria = mysqli_query($conn, "SELECT * FROM subkriteria WHERE id_kriteria = '$id_kriteria'");

// Ambil id_subkriteria yang sudah tersimpan di tabel rentang untuk id_kriteria ini
$sqlRentang = mysqli_query($conn, "SELECT id_subkriteria FROM rentang WHERE id_kriteria = '$id_kriteria'");
$subkriteriaTersimpan = [];
while ($row = mysqli_fetch_assoc($sqlRentang)) {
    $subkriteriaTersimpan[] = $row['id_subkriteria'];
}

// Buat opsi untuk dropdown sub-kriteria
$options = "";
while ($dataSubkriteria = mysqli_fetch_array($sqlSubkriteria)) {
    $isDisabled = in_array($dataSubkriteria['id_subkriteria'], $subkriteriaTersimpan) ? 'disabled' : '';
    $options .= "<option value='" . $dataSubkriteria['id_subkriteria'] . "' $isDisabled>" . $dataSubkriteria['nama_subkriteria'] . ($isDisabled ? " (Sudah Tersimpan)" : "") . "</option>";
}

if ($options === "") {
    echo "no_subkriteria";
} else {
    echo $options; // Kembalikan opsi sub-kriteria
}

// Menutup koneksi database
mysqli_close($conn);

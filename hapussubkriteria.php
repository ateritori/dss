<?php
require 'config/koneksi.php';
$id = $_GET['id'];

// Ambil id_kriteria terkait dari subkriteria yang akan dihapus
$queryKriteria = mysqli_query($conn, "SELECT id_kriteria FROM SubKriteria WHERE id_subkriteria = '$id'");
$rowKriteria = mysqli_fetch_assoc($queryKriteria);
$id_kriteria = $rowKriteria['id_kriteria'];

// Cek apakah masih ada subkriteria lain dengan id_kriteria terkait
$querySubkriteriaCount = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM SubKriteria WHERE id_kriteria = '$id_kriteria'");
$resultCount = mysqli_fetch_assoc($querySubkriteriaCount);

// Jika hanya tersisa 1 subkriteria dengan id_kriteria terkait
if ($resultCount['jumlah'] == 1) {
    // Update tabel Kriteria, set sub_kriteria menjadi 0
    $updateKriteria = mysqli_query($conn, "UPDATE Kriteria SET sub_kriteria = '0' WHERE id_kriteria = '$id_kriteria'");

    if ($updateKriteria) {
        // Proses penghapusan data subkriteria setelah update berhasil
        $sql = mysqli_query($conn, "DELETE FROM SubKriteria WHERE id_subkriteria='$id'");

        if ($sql) {
            echo "<script>alert('Data Berhasil Dihapus dan Kriteria Diperbarui'); window.location = 'dashboard.php?url=kriteria';</script>";
        } else {
            echo "<script>alert('Data Berhasil Dihapus, tapi gagal menghapus subkriteria.'); window.location = 'dashboard.php?url=kriteria';</script>";
        }
    } else {
        echo "<script>alert('Gagal memperbarui Kriteria.'); window.location = 'dashboard.php?url=kriteria';</script>";
    }
} else {
    // Jika lebih dari 1 subkriteria terkait, langsung hapus subkriteria
    $sql = mysqli_query($conn, "DELETE FROM SubKriteria WHERE id_subkriteria='$id'");

    if ($sql) {
        echo "<script>alert('Data Berhasil Dihapus'); window.location = 'dashboard.php?url=kriteria';</script>";
    } else {
        echo "<script>alert('Data Gagal Dihapus. Pastikan data tidak terkait dengan tabel lain.'); window.location = 'dashboard.php?url=kriteria';</script>";
    }
}

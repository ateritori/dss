<?php
// Include file koneksi ke database
require('config/koneksi.php');

// Ambil data dari form
$id_kriteria = $_POST['id_kriteria'];
$subkriteria = $_POST['subkriteria']; // Nama-nama subkriteria
$jenis_subkriteria = $_POST['jenis_subkriteria']; // Cost atau benefit
$id_subkriteria = isset($_POST['id_subkriteria']) ? $_POST['id_subkriteria'] : []; // ID subkriteria yang sudah ada

var_dump($id_kriteria);
var_dump($subkriteria);
var_dump($jenis_subkriteria);
var_dump($id_subkriteria);
exit();

// Validasi: Tidak boleh menghapus semua sub-kriteria
if (count($subkriteria) < 1) {
    echo "<script>alert('Minimal harus ada satu sub-kriteria!'); window.history.back();</script>";
    exit();
}

// Ambil data sub-kriteria dari database (sebelum di-update)
$sqlExistingSub = mysqli_query($conn, "SELECT id_subkriteria FROM SubKriteria WHERE id_kriteria = $id_kriteria");
$existingSubIds = [];
while ($row = mysqli_fetch_assoc($sqlExistingSub)) {
    $existingSubIds[] = $row['id_subkriteria'];
}

// Update atau tambahkan sub-kriteria yang ada
foreach ($subkriteria as $index => $nama_subkriteria) {
    $nama_subkriteria = mysqli_real_escape_string($conn, $nama_subkriteria);
    $tipe_subkriteria = $jenis_subkriteria[$index]; // Cost atau benefit

    // Jika sub-kriteria sudah ada (ID diberikan), maka update
    if (!empty($id_subkriteria[$index])) {
        $id_sub = $id_subkriteria[$index];
        $sqlUpdate = "UPDATE SubKriteria 
                      SET nama_subkriteria = '$nama_subkriteria', tipe_subkriteria = '$tipe_subkriteria'
                      WHERE id_subkriteria = '$id_sub'";
        mysqli_query($conn, $sqlUpdate);
    } else {
        // Jika ID subkriteria tidak ada, berarti tambahkan yang baru
        $sqlInsert = "INSERT INTO SubKriteria (id_kriteria, nama_subkriteria, tipe_subkriteria) 
                      VALUES ('$id_kriteria', '$nama_subkriteria', '$tipe_subkriteria')";
        mysqli_query($conn, $sqlInsert);

        // Update kolom sub_kriteria di tabel Kriteria menjadi 1 setelah menambahkan sub-kriteria baru
        $sqlUpdateKriteria = "UPDATE Kriteria SET sub_kriteria = 1 WHERE id_kriteria = '$id_kriteria'";
        mysqli_query($conn, $sqlUpdateKriteria);
    }
}

// Hapus sub-kriteria yang dihapus melalui tombol hapus (yang tidak ada di daftar post)
$subkriteriaIdsToKeep = array_filter($id_subkriteria); // Ambil ID yang ada di post
$subkriteriaIdsToDelete = array_diff($existingSubIds, $subkriteriaIdsToKeep); // Cari yang tidak ada di post

// Pastikan ada setidaknya satu sub-kriteria yang tersisa setelah penghapusan
if (count($existingSubIds) > 1 || count($subkriteria) > 1) {
    foreach ($subkriteriaIdsToDelete as $idToDelete) {
        $sqlDelete = "DELETE FROM SubKriteria WHERE id_subkriteria = '$idToDelete'";
        mysqli_query($conn, $sqlDelete);
    }
} else {
    echo "<script>alert('Minimal harus ada satu sub-kriteria!'); window.history.back();</script>";
    exit();
}

// Redirect setelah proses selesai
echo "<script>alert('Data sub-kriteria berhasil diperbarui!'); window.location.href = 'dashboard.php?url=kriteria';</script>";

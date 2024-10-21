<?php
// Include file koneksi ke database
require('config/koneksi.php');

// Ambil data dari form
$id_kriteria = $_POST['id_kriteria'];
$subkriteria = isset($_POST['subkriteria']) ? $_POST['subkriteria'] : []; // Nama-nama subkriteria
$jenis_subkriteria = isset($_POST['jenis_subkriteria']) ? $_POST['jenis_subkriteria'] : []; // Cost atau benefit
$id_subkriteria = isset($_POST['id_subkriteria']) ? $_POST['id_subkriteria'] : []; // ID subkriteria yang sudah ada

// Ambil data sub-kriteria yang ada dari database
$sqlExistingSub = mysqli_query($conn, "SELECT id_subkriteria FROM SubKriteria WHERE id_kriteria = $id_kriteria");
$existingSubIds = [];
while ($row = mysqli_fetch_assoc($sqlExistingSub)) {
    $existingSubIds[] = $row['id_subkriteria'];
}

// Update atau tambahkan sub-kriteria yang ada
$isNewSubkriteriaAdded = false;
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
        $isNewSubkriteriaAdded = true;
    }
}

// Jika ada subkriteria baru yang ditambahkan, update kolom `tipe_kriteria` menjadi NULL dan `sub_kriteria` menjadi 1
if ($isNewSubkriteriaAdded) {
    $sqlUpdateKriteria = "UPDATE Kriteria 
                          SET tipe_kriteria = NULL, sub_kriteria = 1 
                          WHERE id_kriteria = '$id_kriteria'";
    mysqli_query($conn, $sqlUpdateKriteria);
}

// Pastikan bahwa $hapus_subkriteria sudah berupa array, bukan string
$hapus_subkriteria = isset($_POST['hapus_subkriteria']) ? $_POST['hapus_subkriteria'] : [];

if (!empty($hapus_subkriteria)) {
    foreach ($hapus_subkriteria as $id_sub) {
        $id_sub = mysqli_real_escape_string($conn, $id_sub);
        $sqlDelete = "DELETE FROM SubKriteria WHERE id_subkriteria = '$id_sub'";
        mysqli_query($conn, $sqlDelete);
    }
}

// Redirect setelah proses selesai
echo "<script>alert('Data sub-kriteria berhasil diperbarui!'); window.location.href = 'dashboard.php?url=kriteria';</script>";

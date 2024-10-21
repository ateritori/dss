<?php
// Include file koneksi ke database
require('config/koneksi.php');

// Cek jika ini request dari AJAX untuk menghapus subkriteria
if (isset($_POST['ajax']) && $_POST['ajax'] === 'delete' && isset($_POST['id_subkriteria'])) {
    $id_subkriteria = mysqli_real_escape_string($conn, $_POST['id_subkriteria']);

    // Query untuk menghapus subkriteria dari database
    $sqlDelete = "DELETE FROM SubKriteria WHERE id_subkriteria = '$id_subkriteria'";
    if (mysqli_query($conn, $sqlDelete)) {
        echo json_encode(['status' => 'success', 'message' => 'Sub-Kriteria berhasil dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus Sub-Kriteria']);
    }
    exit(); // Hentikan eksekusi skrip setelah menangani AJAX
}

// Bagian di bawah ini hanya akan berjalan jika bukan permintaan AJAX

// Ambil data dari form
$id_kriteria = $_POST['id_kriteria'];
$subkriteria = isset($_POST['subkriteria']) ? $_POST['subkriteria'] : [];
$jenis_subkriteria = isset($_POST['jenis_subkriteria']) ? $_POST['jenis_subkriteria'] : [];
$id_subkriteria = isset($_POST['id_subkriteria']) ? $_POST['id_subkriteria'] : [];

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
    $tipe_subkriteria = $jenis_subkriteria[$index];

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

// Jika ada penambahan subkriteria baru, update kolom tipe_kriteria dan sub_kriteria di tabel Kriteria
if ($isNewSubkriteriaAdded) {
    $sqlUpdateKriteria = "UPDATE Kriteria 
                          SET tipe_kriteria = NULL, sub_kriteria = '1' 
                          WHERE id_kriteria = '$id_kriteria'";
    mysqli_query($conn, $sqlUpdateKriteria);
}

// Redirect setelah proses selesai
echo "<script>alert('Data sub-kriteria berhasil diperbarui!'); window.location.href = 'dashboard.php?url=kriteria';</script>";

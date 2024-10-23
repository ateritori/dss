<?php
// Include koneksi database
include('config/koneksi.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek jika form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_kriteria = $_POST['id_kriteria'];
    $model_penilaian = $_POST['model_penilaian'];
    $uraianList = $_POST['uraian'];
    $valueRentangList = $_POST['value_rentang'];
    $id_rentangList = $_POST['id_rentang']; // Menyimpan id_rentang jika ada untuk update
    $hapus_subkriteria = $_POST['hapus_subkriteria']; // Jika ada yang ingin dihapus

    if ($model_penilaian == '1') {
        // Jika model_penilaian 1, simpan dengan NULL untuk uraian dan nilai_rentang
        $insertRentang = $conn->prepare("INSERT INTO Rentang (id_kriteria, id_subkriteria, jenis_penilaian, uraian, nilai_rentang) VALUES (?, NULL, ?, NULL, NULL)");
        $insertRentang->bind_param("is", $id_kriteria, $model_penilaian);

        if (!$insertRentang->execute()) {
            die("Insert error: " . $insertRentang->error);
        }
    } else {
        // Proses untuk memperbarui atau menyimpan rentang baru
        foreach ($uraianList as $index => $uraian) {
            // Pastikan bahwa value yang sesuai ada
            if (isset($valueRentangList[$index])) {
                $nilaiRentang = $valueRentangList[$index];

                if (!empty($id_rentangList[$index])) {
                    // Update jika id_rentang ada
                    $id_rentang = $id_rentangList[$index];
                    $updateRentang = $conn->prepare("UPDATE Rentang SET id_kriteria = ?, id_subkriteria = NULL, jenis_penilaian = ?, uraian = ?, nilai_rentang = ? WHERE id_rentang = ?");
                    $updateRentang->bind_param("iisdi", $id_kriteria, $model_penilaian, $uraian, $nilaiRentang, $id_rentang);

                    if (!$updateRentang->execute()) {
                        die("Update error: " . $updateRentang->error);
                    }
                } else {
                    // Insert jika id_rentang tidak ada
                    $insertRentang = $conn->prepare("INSERT INTO Rentang (id_kriteria, id_subkriteria, jenis_penilaian, uraian, nilai_rentang) VALUES (?, NULL, ?, ?, ?)");
                    $insertRentang->bind_param("issd", $id_kriteria, $model_penilaian, $uraian, $nilaiRentang);

                    if (!$insertRentang->execute()) {
                        die("Insert error: " . $insertRentang->error);
                    }
                }
            }
        }
    }

    // Redirect ke dashboard setelah sukses
    echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=kriteria';</script>";
    exit();
}

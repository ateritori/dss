<?php
// Include koneksi database
require('config/koneksi.php');

// Fungsi untuk menyimpan data rentang
function simpanRentang($conn)
{
    // Ambil data dari form
    $id_kriteria = $_POST['id_kriteria'];
    $id_subkriteria = $_POST['id_subkriteria'];
    $model_penilaian = $_POST['model_penilaian'];

    // Debug: Cetak nilai yang diambil
    echo "ID Kriteria: $id_kriteria, ID Subkriteria: $id_subkriteria, Jenis Penilaian: $model_penilaian<br>";

    // Jika model penilaian 1 (Dinamis/Input Manual)
    if ($model_penilaian == '1') {
        // Insert tanpa uraian dan nilai, id_subkriteria bisa NULL
        $query = "INSERT INTO rentang (id_kriteria, id_subkriteria, jenis_penilaian) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Bind parameter: gunakan NULL jika id_subkriteria kosong
        $subkriteria = empty($id_subkriteria) ? null : $id_subkriteria;

        $stmt->bind_param("iis", $id_kriteria, $subkriteria, $model_penilaian);

        if ($stmt->execute()) {
            echo "<script>alert('Data sub-kriteria berhasil diperbarui!'); window.location.href = 'dashboard.php?url=data_rentang';</script>";
        } else {
            // Log error
            logError($stmt->error);
            echo "Gagal menyimpan data: " . $stmt->error;
        }
    }
    // Jika model penilaian 2 (Rentang Nilai Tertentu)
    else if ($model_penilaian == '2') {
        // Cek apakah ada inputan untuk uraian dan nilai
        $uraianArray = [];
        $nilaiArray = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'uraian_') === 0) {
                $uraianArray[] = $value; // Simpan semua uraian
            } else if (strpos($key, 'nilai_') === 0) {
                $nilaiArray[] = $value; // Simpan semua nilai
            }
        }

        // Validasi: Pastikan tidak ada inputan yang kosong
        if (empty($uraianArray) || empty($nilaiArray)) {
            echo "Uraian dan nilai harus diisi.";
            return;
        }

        // Loop untuk menyimpan setiap rentang
        for ($i = 0; $i < count($uraianArray); $i++) {
            $uraian = $uraianArray[$i];
            $nilai = $nilaiArray[$i];

            // Insert data rentang
            $query = "INSERT INTO rentang (id_kriteria, id_subkriteria, jenis_penilaian, uraian, nilai_rentang) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            // Bind parameter: gunakan NULL jika id_subkriteria kosong
            $subkriteria = empty($id_subkriteria) ? null : $id_subkriteria;

            $stmt->bind_param("iissd", $id_kriteria, $subkriteria, $model_penilaian, $uraian, $nilai);

            if ($stmt->execute()) {
                echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=data_rentang';</script>";
            } else {
                // Log error
                logError($stmt->error);
                echo "Gagal menyimpan data: " . $stmt->error . "<br>";
            }
        }
    }
}

// Fungsi untuk mencatat kesalahan ke file log
function logError($error)
{
    $logFile = 'error_log.txt';
    $timestamp = date("Y-m-d H:i:s");
    $errorMessage = "[$timestamp] Error: $error" . PHP_EOL;
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
}

// Panggil fungsi simpan
simpanRentang($conn);

// Tutup koneksi
$conn->close();

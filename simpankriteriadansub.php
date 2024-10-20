<?php
// Include file koneksi database
require('config/koneksi.php');

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_kriteria = $_POST['kriteria'] ?? '';
    $tipe_kriteria = $_POST['atribut'] ?? null; // NULL jika tidak ada
    $status_sub = $_POST['status_sub_kriteria'] ?? '0'; // Default '0'

    // Ambil subkriteria jika ada
    $subkriteria = $_POST['subkriteria'] ?? [];
    $jenis_subkriteria = $_POST['jenis_subkriteria'] ?? [];

    // Konversi tipe_kriteria yang kosong menjadi NULL
    if (empty($tipe_kriteria)) {
        $tipe_kriteria = null; // Set as NULL if it's empty
    }

    // Query untuk menyimpan data ke tabel Kriteria
    $query_kriteria = "INSERT INTO Kriteria (nama_kriteria, tipe_kriteria, sub_kriteria) VALUES (?, ?, ?)";
    $stmt_kriteria = $conn->prepare($query_kriteria);
    $stmt_kriteria->bind_param("sss", $nama_kriteria, $tipe_kriteria, $status_sub);

    // Eksekusi query simpan Kriteria
    if ($stmt_kriteria->execute()) {
        $id_kriteria = $stmt_kriteria->insert_id; // Ambil ID Kriteria yang baru disimpan

        // Jika status_sub adalah '1', simpan data SubKriteria
        if ($status_sub === '1') {
            $query_subkriteria = "INSERT INTO SubKriteria (id_kriteria, nama_subkriteria, tipe_subkriteria) VALUES (?, ?, ?)";
            $stmt_subkriteria = $conn->prepare($query_subkriteria);

            // Simpan setiap subkriteria ke tabel SubKriteria
            for ($i = 0; $i < count($subkriteria); $i++) {
                $nama_subkriteria = $subkriteria[$i];
                $tipe_subkriteria = $jenis_subkriteria[$i];

                // Pastikan nama_subkriteria dan tipe_subkriteria tidak kosong
                if (!empty($nama_subkriteria) && !empty($tipe_subkriteria)) {
                    $stmt_subkriteria->bind_param("iss", $id_kriteria, $nama_subkriteria, $tipe_subkriteria);
                    $stmt_subkriteria->execute();
                }
            }
            $stmt_subkriteria->close(); // Tutup statement subkriteria
        }

        echo "Data berhasil disimpan!";
    } else {
        echo "Terjadi kesalahan saat menyimpan data kriteria: " . $stmt_kriteria->error;
    }

    // Tutup statement kriteria
    $stmt_kriteria->close();
} else {
    echo "Tidak ada data yang disubmit.";
}

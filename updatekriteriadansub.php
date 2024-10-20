<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('config/koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id_kriteria = $_POST['id_kriteria'];
    $nama_kriteria = $_POST['kriteria'] ?? '';
    $tipe_kriteria = $_POST['atribut'] ?? null; // NULL jika tidak ada
    $status_sub = $_POST['status_sub_kriteria'] ?? '0'; // Default '0'

    // Ambil subkriteria jika ada
    $subkriteria = $_POST['subkriteria'] ?? [];
    $jenis_subkriteria = $_POST['jenis_subkriteria'] ?? [];
    $id_subkriteria = $_POST['id_subkriteria'] ?? []; // Menyimpan ID sub-kriteria

    // Validasi form
    if (empty($nama_kriteria)) {
        echo "Nama kriteria tidak boleh kosong";
        exit();
    }

    if ($status_sub === '0' && empty($tipe_kriteria)) {
        echo "Silakan pilih tipe kriteria";
        exit();
    }

    if ($status_sub === '1' && count($subkriteria) === 0) {
        echo "Silakan masukkan setidaknya satu sub-kriteria";
        exit();
    }

    // Ambil status_sub lama dari database
    $query_old_status = "SELECT sub_kriteria FROM Kriteria WHERE id_kriteria = ?";
    $stmt_old_status = $conn->prepare($query_old_status);
    $stmt_old_status->bind_param("i", $id_kriteria);
    $stmt_old_status->execute();
    $stmt_old_status->bind_result($old_status_sub);
    $stmt_old_status->fetch();
    $stmt_old_status->close();

    // Jika status berubah dari Memiliki Sub Kriteria ke Tidak Memiliki Sub Kriteria
    if ($old_status_sub === '1' && $status_sub === '0') {
        // Hapus semua sub-kriteria yang terkait dengan kriteria ini
        $query_delete_subkriteria = "DELETE FROM SubKriteria WHERE id_kriteria = ?";
        $stmt_delete_subkriteria = $conn->prepare($query_delete_subkriteria);
        $stmt_delete_subkriteria->bind_param("i", $id_kriteria);
        $stmt_delete_subkriteria->execute();
        $stmt_delete_subkriteria->close();
    }

    // Jika status berubah dari Tidak Memiliki Sub Kriteria ke Memiliki Sub Kriteria
    if ($old_status_sub === '0' && $status_sub === '1') {
        // Ubah kolom tipe_kriteria menjadi NULL
        $tipe_kriteria = null;
    }

    // Update data kriteria di database
    $query_kriteria = "UPDATE Kriteria SET nama_kriteria = ?, tipe_kriteria = ?, sub_kriteria = ? WHERE id_kriteria = ?";
    $stmt_kriteria = $conn->prepare($query_kriteria);

    // Pengecekan apakah prepare berhasil
    if (!$stmt_kriteria) {
        die("Gagal mempersiapkan query: " . $conn->error);
    }

    // Binding dan eksekusi query
    $stmt_kriteria->bind_param("sssi", $nama_kriteria, $tipe_kriteria, $status_sub, $id_kriteria);

    if ($stmt_kriteria->execute()) {
        // Update atau insert sub-kriteria jika status sub_kriteria adalah 1
        if ($status_sub === '1') {
            foreach ($subkriteria as $index => $nama_subkriteria) {
                if (!empty($id_subkriteria[$index])) {
                    // Update sub-kriteria yang ada
                    $query_update_subkriteria = "UPDATE SubKriteria SET nama_subkriteria = ?, tipe_subkriteria = ? WHERE id_subkriteria = ?";
                    $stmt_update_subkriteria = $conn->prepare($query_update_subkriteria);
                    if (!$stmt_update_subkriteria) {
                        die("Gagal mempersiapkan query update sub-kriteria: " . $conn->error);
                    }
                    $stmt_update_subkriteria->bind_param("ssi", $nama_subkriteria, $jenis_subkriteria[$index], $id_subkriteria[$index]);
                    $stmt_update_subkriteria->execute();
                    $stmt_update_subkriteria->close();
                } else {
                    // Insert sub-kriteria baru jika id_subkriteria tidak ada
                    $query_insert_subkriteria = "INSERT INTO SubKriteria (id_kriteria, nama_subkriteria, tipe_subkriteria) VALUES (?, ?, ?)";
                    $stmt_insert_subkriteria = $conn->prepare($query_insert_subkriteria);
                    if (!$stmt_insert_subkriteria) {
                        die("Gagal mempersiapkan query sub-kriteria: " . $conn->error);
                    }
                    $stmt_insert_subkriteria->bind_param("iss", $id_kriteria, $nama_subkriteria, $jenis_subkriteria[$index]);
                    $stmt_insert_subkriteria->execute();
                    $stmt_insert_subkriteria->close();
                }
            }
        }

        // Redirect ke halaman dashboard setelah berhasil
        echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=kriteria';</script>";
    } else {
        echo "Gagal memperbarui: " . $stmt_kriteria->error;
    }

    // Tutup statement
    $stmt_kriteria->close();
}

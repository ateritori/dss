<?php
session_start();
require 'config/koneksi.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script type='text/javascript'>
            alert('Anda Belum Login');
            window.location = 'index.php';
        </script>";
    exit;
}

// Ambil data dari form
$id_alternatif = $_POST['alternatif'];

// Cek apakah alternatif dipilih
if (empty($id_alternatif)) {
    echo "<script type='text/javascript'>
            alert('Nama alternatif harus dipilih');
            window.location = 'dashboard.php?url=tambahnilai';
        </script>";
    exit;
}

// Query untuk mengambil semua kriteria dan subkriteria
$sql = "SELECT k.id_kriteria, sk.id_subkriteria 
        FROM kriteria k
        LEFT JOIN subkriteria sk ON k.id_kriteria = sk.id_kriteria
        ORDER BY k.id_kriteria, sk.id_subkriteria";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_kriteria = $row['id_kriteria'];
        $id_subkriteria = $row['id_subkriteria']; // Bisa bernilai NULL

        // Cek apakah ada input untuk kriteria/subkriteria ini
        if ($id_subkriteria == NULL) {
            // Jika tidak ada subkriteria, cek input number untuk kriteria
            $nilai = $_POST['rentang_kriteria_' . $id_kriteria] ?? null;
        } else {
            // Jika ada subkriteria, cek radio button
            $nilai = $_POST['rentang_subkriteria_' . $id_subkriteria] ?? null;
        }

        // Jika ada input nilai, simpan ke tabel penilaian
        if (!empty($nilai)) {
            // Siapkan query untuk menyimpan data
            $stmt = $conn->prepare("INSERT INTO penilaian (id_alternatif, id_kriteria, id_subkriteria, nilai) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $id_alternatif, $id_kriteria, $id_subkriteria, $nilai);

            // Eksekusi query
            $stmt->execute();
        }
    }

    echo "<script type='text/javascript'>
            alert('Penilaian berhasil disimpan');
            window.location = 'dashboard.php?url=tambahnilai';
        </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Data kriteria atau subkriteria tidak ditemukan');
            window.location = 'dashboard.php?url=tambahnilai';
        </script>";
}

// Tutup koneksi
$conn->close();

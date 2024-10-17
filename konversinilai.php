<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda Belum Login']);
    exit;
}

require 'config/koneksi.php';

// Function to convert scores based on Rentang
function convertScores($conn, $id_alternatif)
{
    $result = [];

    // Query untuk mengambil nilai dari Penilaian berdasarkan id_alternatif
    $sql = "
        SELECT p.id_kriteria, p.id_subkriteria, p.nilai, k.nama_kriteria, sk.nama_subkriteria 
        FROM Penilaian p
        LEFT JOIN Kriteria k ON p.id_kriteria = k.id_kriteria
        LEFT JOIN SubKriteria sk ON p.id_subkriteria = sk.id_subkriteria
        WHERE p.id_alternatif = '$id_alternatif'
    ";

    $penilaianResult = mysqli_query($conn, $sql);

    while ($data = mysqli_fetch_array($penilaianResult)) {
        $nilai = $data['nilai'];
        $id_kriteria = $data['id_kriteria'];
        $id_subkriteria = $data['id_subkriteria'];

        // Cek nilai di tabel Rentang berdasarkan kriteria
        if (is_null($id_subkriteria)) {
            // Jika tidak ada subkriteria
            $rentangQuery = mysqli_query($conn, "SELECT * FROM Rentang WHERE id_kriteria = '$id_kriteria' AND nilai = '$nilai'");
        } else {
            // Jika ada subkriteria
            $rentangQuery = mysqli_query($conn, "SELECT * FROM Rentang WHERE id_kriteria = '$id_kriteria' AND id_subkriteria = '$id_subkriteria' AND nilai = '$nilai'");
        }

        $rentangData = mysqli_fetch_array($rentangQuery);
        if ($rentangData) {
            $result[] = [
                'id_kriteria' => $id_kriteria,
                'id_subkriteria' => $id_subkriteria,
                'nilai_asli' => $nilai,
                'nilai_konversi' => $rentangData['deskripsi'] // Assuming there's a 'deskripsi' column in Rentang table
            ];
        } else {
            // Jika tidak ada konversi, tetap gunakan nilai asli
            $result[] = [
                'id_kriteria' => $id_kriteria,
                'id_subkriteria' => $id_subkriteria,
                'nilai_asli' => $nilai,
                'nilai_konversi' => $nilai // Tetap gunakan nilai asli
            ];
        }
    }

    return $result;
}

// Cek jika ada id_alternatif yang dikirim
if (isset($_POST['id_alternatif'])) {
    $id_alternatif = $_POST['id_alternatif'];
    $scores = convertScores($conn, $id_alternatif);
    echo json_encode(['status' => 'success', 'data' => $scores]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID Alternatif tidak ditemukan']);
}

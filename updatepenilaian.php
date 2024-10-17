<?php
session_start(); // Pastikan session dimulai
require 'config/koneksi.php';

if (isset($_POST['edit'])) {
    $id_alternatif = $_POST['id_alternatif'];

    // Ambil semua kriteria
    $sql_kriteria = "SELECT id_kriteria FROM Kriteria";
    $result_kriteria = $conn->query($sql_kriteria);

    if ($result_kriteria->num_rows > 0) {
        while ($row_kriteria = $result_kriteria->fetch_assoc()) {
            $id_kriteria = $row_kriteria['id_kriteria'];

            // Cek jika ada subkriteria
            $sql_subkriteria = "SELECT id_subkriteria FROM SubKriteria WHERE id_kriteria = '$id_kriteria'";
            $result_subkriteria = $conn->query($sql_subkriteria);

            // Jika ada subkriteria, lakukan insert/update untuk masing-masing subkriteria
            if ($result_subkriteria->num_rows > 0) {
                while ($row_subkriteria = $result_subkriteria->fetch_assoc()) {
                    $id_subkriteria = $row_subkriteria['id_subkriteria'];
                    $nilai = $_POST['rentang_subkriteria_' . $id_subkriteria] ?? null;

                    if (!is_null($nilai)) {
                        // Cek apakah penilaian sudah ada
                        $sql_check = "SELECT * FROM Penilaian WHERE id_alternatif = '$id_alternatif' AND id_kriteria = '$id_kriteria' AND id_subkriteria = '$id_subkriteria'";
                        $check_result = $conn->query($sql_check);

                        if ($check_result->num_rows > 0) {
                            // Update penilaian yang ada
                            $sql_update = "UPDATE Penilaian SET nilai = '$nilai' WHERE id_alternatif = '$id_alternatif' AND id_kriteria = '$id_kriteria' AND id_subkriteria = '$id_subkriteria'";
                            $conn->query($sql_update);
                        } else {
                            // Insert penilaian baru
                            $sql_insert = "INSERT INTO Penilaian (id_alternatif, id_kriteria, id_subkriteria, nilai) VALUES ('$id_alternatif', '$id_kriteria', '$id_subkriteria', '$nilai')";
                            $conn->query($sql_insert);
                        }
                    }
                }
            } else {
                // Jika tidak ada subkriteria, lakukan hal yang sama untuk penilaian kriteria
                $nilai = $_POST['rentang_kriteria_' . $id_kriteria] ?? null;

                if (!is_null($nilai)) {
                    // Cek apakah penilaian sudah ada
                    $sql_check = "SELECT * FROM Penilaian WHERE id_alternatif = '$id_alternatif' AND id_kriteria = '$id_kriteria' AND id_subkriteria IS NULL";
                    $check_result = $conn->query($sql_check);

                    if ($check_result->num_rows > 0) {
                        // Update penilaian yang ada
                        $sql_update = "UPDATE Penilaian SET nilai = '$nilai' WHERE id_alternatif = '$id_alternatif' AND id_kriteria = '$id_kriteria' AND id_subkriteria IS NULL";
                        $conn->query($sql_update);
                    } else {
                        // Insert penilaian baru
                        $sql_insert = "INSERT INTO Penilaian (id_alternatif, id_kriteria, nilai) VALUES ('$id_alternatif', '$id_kriteria', '$nilai')";
                        $conn->query($sql_insert);
                    }
                }
            }
        }
    }

    // Redirect setelah selesai
    header("Location: dashboard.php?url=penilaian");
    exit();
} else {
    echo "Akses tidak valid.";
}

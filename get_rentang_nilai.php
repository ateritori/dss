<?php
require 'config/koneksi.php'; // Koneksi ke database

if (isset($_POST['id_kriteria'])) {
    $id_kriteria = $_POST['id_kriteria'];
    $id_sub_kriteria = isset($_POST['id_sub_kriteria']) ? $_POST['id_sub_kriteria'] : null;

    if ($id_sub_kriteria) {
        $query = "SELECT * FROM Rentang WHERE id_kriteria = '$id_kriteria' AND id_sub_kriteria = '$id_sub_kriteria'";
    } else {
        // Ambil berdasarkan kriteria jika sub-kriteria NULL
        $query = "SELECT * FROM Rentang WHERE id_kriteria = '$id_kriteria' AND id_sub_kriteria IS NULL";
    }

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<select name="nilai" class="form-control" required>';
        echo '<option value="">-- Pilih Nilai --</option>';
        while ($rentang = mysqli_fetch_array($result)) {
            echo '<option value="' . $rentang['value'] . '">' . $rentang['uraian'] . ' (' . $rentang['value'] . ')</option>';
        }
        echo '</select>';
    } else {
        // Jika tidak ada rentang, tampilkan input number
        echo '<input type="number" name="nilai" class="form-control" step="any" required placeholder="Masukkan nilai">';
    }
}

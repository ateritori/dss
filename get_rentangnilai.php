<?php
require 'config/koneksi.php';

// Debug: untuk memeriksa apakah nilai POST terkirim
if (isset($_POST['id_kriteria']) && isset($_POST['id_sub_kriteria'])) {
    $id_kriteria = $_POST['id_kriteria'];
    $id_sub_kriteria = $_POST['id_sub_kriteria'];

    // Debug: cetak nilai yang diterima
    error_log("ID Kriteria: $id_kriteria, ID Sub Kriteria: $id_sub_kriteria");

    // Query untuk memeriksa apakah ada rentang nilai untuk kriteria dan sub-kriteria
    $query = "SELECT * FROM Rentang WHERE id_kriteria = '$id_kriteria' AND id_subkriteria = '$id_sub_kriteria'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Jika ada rentang, tampilkan dropdown nilai
        echo '<select name="nilai" class="form-control" required>';
        echo '<option value="">-- Pilih Nilai --</option>';
        while ($row = mysqli_fetch_array($result)) {
            echo '<option value="' . $row['value'] . '">' . $row['uraian'] . ' (' . $row['value'] . ')</option>';
        }
        echo '</select>';
    } else {
        // Jika tidak ada rentang, tampilkan input number untuk nilai
        echo '<input type="number" name="nilai" class="form-control" step="any" required>';
    }
} else {
    // Debug: Jika POST tidak lengkap
    error_log("POST data tidak lengkap");
    echo "Error: POST data tidak lengkap.";
}

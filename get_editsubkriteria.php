<?php
require 'config/koneksi.php';

if (isset($_POST['id_kriteria']) && isset($_POST['id_rentang'])) {
    $id_kriteria = $_POST['id_kriteria'];
    $id_rentang = $_POST['id_rentang'];

    // Query untuk mendapatkan sub-kriteria berdasarkan id_kriteria
    $query = "SELECT * FROM SubKriteria WHERE id_kriteria = '$id_kriteria'";
    $result = mysqli_query($conn, $query);

    // Query untuk mendapatkan id_subkriteria dari Rentang berdasarkan id_rentang
    $rentang_query = "SELECT id_subkriteria FROM Rentang WHERE id_rentang = '$id_rentang'";
    $rentang_result = mysqli_query($conn, $rentang_query);
    $rentang_data = mysqli_fetch_array($rentang_result);
    $selected_subkriteria = $rentang_data['id_subkriteria'];

    // Tampilkan opsi untuk sub-kriteria
    echo '<option value="">-- Pilih Sub-Kriteria --</option>';
    while ($row = mysqli_fetch_array($result)) {
        $selected = ($row['id_subkriteria'] == $selected_subkriteria) ? 'selected' : '';
        echo '<option value="' . $row['id_subkriteria'] . '" ' . $selected . '>' . $row['nama_subkriteria'] . '</option>';
    }
}

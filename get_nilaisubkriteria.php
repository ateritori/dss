<?php
require 'config/koneksi.php'; // Koneksi ke database

if (isset($_POST['id_kriteria'])) {
    $id_kriteria = $_POST['id_kriteria'];

    $query = "SELECT * FROM SubKriteria WHERE id_kriteria = '$id_kriteria'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($subkriteria = mysqli_fetch_array($result)) {
            echo "<option value='{$subkriteria['id_sub_kriteria']}'>{$subkriteria['nama_sub_kriteria']}</option>";
        }
    }
}

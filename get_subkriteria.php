<?php
require 'config/koneksi.php';

if (isset($_GET['id_kriteria'])) {
    $id_kriteria = $_GET['id_kriteria'];

    // Query untuk mengambil data SubKriteria berdasarkan id_kriteria yang dipilih
    $sql = "SELECT id_subkriteria, nama_subkriteria FROM SubKriteria WHERE id_kriteria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_kriteria);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Mulai tag dropdown <select>
        echo "<select name='id_subkriteria' id='id_subkriteria' class='form-control' style='color: black'>";
        echo "<option value=''>-- Pilih Subkriteria --</option>"; // Opsi default

        // Looping hasil query dan tampilkan sebagai option di dalam <select>
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id_subkriteria'] . "'>" . $row['nama_subkriteria'] . "</option>";
        }

        // Tutup tag dropdown <select>
        echo "</select>";
    } else {
        // Jika tidak ada subkriteria yang ditemukan
        echo "<select  class='form-control' style='color: black'><option value=''>Tidak ada subkriteria tersedia</option></select>";
    }

    $stmt->close();
}

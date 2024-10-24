<?php
// Menghubungkan ke database
require('config/koneksi.php');

// Mendapatkan id_kriteria dari permintaan POST
$id_kriteria = isset($_POST['id_kriteria']) ? $_POST['id_kriteria'] : '';

// Memeriksa apakah id_kriteria valid
if (!empty($id_kriteria)) {
    // Mengambil data subkriteria berdasarkan id_kriteria
    $sqlsubkriteria = mysqli_query($conn, "SELECT * FROM subkriteria WHERE id_kriteria = '$id_kriteria'");

    // Memeriksa apakah ada subkriteria yang ditemukan
    if (mysqli_num_rows($sqlsubkriteria) > 0) {
        // Menghasilkan opsi subkriteria untuk dropdown
        $options = '';
        while ($datasubkriteria = mysqli_fetch_array($sqlsubkriteria)) {
            $options .= "<option value='" . $datasubkriteria['id_subkriteria'] . "'>" . $datasubkriteria['nama_subkriteria'] . "</option>";
        }
        echo $options; // Mengembalikan opsi subkriteria
    } else {
        echo "no_subkriteria"; // Tidak ada subkriteria ditemukan
    }
} else {
    echo "no_subkriteria"; // id_kriteria tidak valid
}

// Menutup koneksi database
mysqli_close($conn);

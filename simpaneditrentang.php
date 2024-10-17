<?php
require 'config/koneksi.php';

// Pastikan request yang diterima adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rentang = mysqli_real_escape_string($conn, $_POST['id_rentang']);
    $uraian = isset($_POST['uraian']) ? mysqli_real_escape_string($conn, $_POST['uraian']) : null;
    $nilai_rentang = isset($_POST['value']) ? mysqli_real_escape_string($conn, $_POST['value']) : null;
    $jenis_penilaian = isset($_POST['jenis_penilaian']) ? $_POST['jenis_penilaian'] : null;

    // Pastikan jenis penilaian adalah "1" atau "2"
    $jenis_penilaian = ($jenis_penilaian == "1") ? "1" : "2";

    // Lakukan validasi untuk memastikan input yang benar
    if (!empty($id_rentang)) {

        // Siapkan query update sesuai dengan pilihan jenis penilaian
        $query = "UPDATE Rentang SET jenis_penilaian='$jenis_penilaian'";

        // Jika jenis penilaian adalah skala, tambahkan nilai uraian dan value
        if ($jenis_penilaian == "1") { // 1 = Skala
            $query .= ", uraian=" . ($uraian !== null ? "'$uraian'" : "NULL") . ", nilai_rentang=" . ($nilai_rentang !== null ? "'$nilai_rentang'" : "NULL");
        } else {
            // Jika Manual, uraian dan nilai harus diset NULL
            $query .= ", uraian=NULL, nilai_rentang=NULL";
        }

        $query .= " WHERE id_rentang='$id_rentang'";

        // Jalankan query
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Data Rentang berhasil diupdate');
                    window.location.href='dashboard.php?url=rentang';
                </script>";
        } else {
            // Menampilkan pesan error dengan mysqli_error()
            $error = mysqli_error($conn);
            echo "<script>
                    alert('Gagal mengupdate data: $error');
                    window.location.href='dashboard.php?url=rentang';
                </script>";
        }
    } else {
        // Jika data tidak valid, kembalikan ke halaman edit dengan pesan
        echo "<script>
                alert('Data yang diisi tidak lengkap atau tidak valid');
                window.location.href='dashboard.php?url=rentang';
            </script>";
    }
}

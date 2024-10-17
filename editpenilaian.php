<?php
if (!isset($_SESSION['username'])) {
?>
    <script type="text/javascript">
        alert('Anda Belum Login');
        window.location = 'index.php';
    </script>
<?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>EDIT PENILAIAN</title>
    <style type="text/css">
        body {
            font-family: "Verdana";
        }

        .custom-radio {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .custom-radio input[type="radio"] {
            margin-right: 10px;
            accent-color: #167395;
            width: 20px;
            height: 20px;
        }

        .custom-radio label {
            color: #333;
            font-size: 14px;
        }

        .custom-radio input[type="radio"]:checked+label {
            color: #167395;
            font-weight: bold;
        }

        .form-control {
            margin-bottom: 15px;
            padding: 8px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
        }
    </style>
</head>

<body id="page-top">
    <br>
    <div class="card shadow" style="width: 50%;">
        <div class="card-header m-0 font-weight-bold" style="text-align: center; background-color: #167395; color: white">Edit Penilaian</div>
        <div class="card-body">
            <form action="updatepenilaian.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group cols-sm-6">
                    <?php
                    require 'config/koneksi.php';

                    // Ambil id_alternatif yang ingin diedit dari query string
                    $id_alternatif = isset($_GET['id_alternatif']) ? $_GET['id_alternatif'] : '';

                    // Query untuk mengambil data alternatif berdasarkan id
                    $sql_alternatif = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_alternatif='$id_alternatif'");
                    $alternatif = mysqli_fetch_assoc($sql_alternatif);
                    ?>

                    <!-- Tampilkan nama alternatif yang akan diedit -->
                    <div class="form-group cols-sm-6">
                        <label style="color: black"><strong>Nama:</strong></label>
                        <input type="text" class="form-control" value="<?php echo $alternatif['nama_alternatif']; ?>" readonly>
                        <input type="hidden" name="id_alternatif" value="<?php echo $alternatif['id_alternatif']; ?>">
                    </div>

                    <?php
                    // Query untuk mengambil data dari tabel penilaian berdasarkan id_alternatif
                    $sql_penilaian = "
                        SELECT p.nilai, k.nama_kriteria, sk.nama_subkriteria, k.id_kriteria, sk.id_subkriteria 
                        FROM Penilaian p
                        JOIN Kriteria k ON p.id_kriteria = k.id_kriteria
                        LEFT JOIN SubKriteria sk ON p.id_subkriteria = sk.id_subkriteria
                        WHERE p.id_alternatif = '$id_alternatif'
                        ORDER BY k.id_kriteria, sk.id_subkriteria
                    ";

                    $result_penilaian = $conn->query($sql_penilaian);

                    // Ambil semua kriteria dan subkriteria
                    $sql_kriteria = "
                        SELECT k.id_kriteria, k.nama_kriteria, sk.id_subkriteria, sk.nama_subkriteria
                        FROM Kriteria k
                        LEFT JOIN SubKriteria sk ON k.id_kriteria = sk.id_kriteria
                        ORDER BY k.id_kriteria, sk.id_subkriteria
                    ";
                    $result_kriteria = $conn->query($sql_kriteria);

                    if ($result_kriteria->num_rows > 0) {
                        $current_kriteria = ""; // Melacak kriteria saat ini
                        $current_subkriteria = ""; // Melacak subkriteria saat ini

                        while ($row_kriteria = $result_kriteria->fetch_assoc()) {
                            // Tampilkan nama kriteria hanya sekali
                            if ($current_kriteria != $row_kriteria['nama_kriteria']) {
                                echo "<label style='color: black'><strong>Kriteria: " . $row_kriteria['nama_kriteria'] . "</strong></label><br>";
                                $current_kriteria = $row_kriteria['nama_kriteria'];
                                $current_subkriteria = ""; // Reset subkriteria setiap kali kriteria berubah
                            }

                            // Jika subkriteria ada dan belum ditampilkan, tampilkan subkriteria
                            if (!is_null($row_kriteria['id_subkriteria']) && $current_subkriteria != $row_kriteria['nama_subkriteria']) {
                                echo "<label style='color: black'>&nbsp;&nbsp;&nbsp;&nbsp;SubKriteria: " . $row_kriteria['nama_subkriteria'] . "</label><br>";
                                $current_subkriteria = $row_kriteria['nama_subkriteria'];
                            }

                            // Cek apakah ada penilaian untuk kriteria/subkriteria ini
                            $nilai_penilaian = '';
                            if ($result_penilaian->num_rows > 0) {
                                // Reset pointer ke awal result penilaian
                                $result_penilaian->data_seek(0);
                                while ($row_penilaian = $result_penilaian->fetch_assoc()) {
                                    // Jika penilaian ditemukan
                                    if (
                                        $row_penilaian['id_kriteria'] == $row_kriteria['id_kriteria'] &&
                                        $row_penilaian['id_subkriteria'] == $row_kriteria['id_subkriteria']
                                    ) {
                                        $nilai_penilaian = $row_penilaian['nilai'];
                                        break;
                                    }
                                }
                            }

                            // Query untuk menampilkan rentang sesuai dengan kriteria atau subkriteria
                            $sql_rentang = "
                                SELECT uraian, nilai_rentang, jenis_penilaian 
                                FROM Rentang 
                                WHERE id_kriteria = '" . $row_kriteria['id_kriteria'] . "' 
                                " . (!is_null($row_kriteria['id_subkriteria']) ? "AND id_subkriteria = '" . $row_kriteria['id_subkriteria'] . "'" : "") . " 
                                ORDER BY nilai_rentang
                            ";
                            $rentang_result = $conn->query($sql_rentang);

                            if ($rentang_result->num_rows > 0) {
                                while ($rentang_row = $rentang_result->fetch_assoc()) {
                                    // Cek jenis penilaian untuk menentukan input
                                    if ($rentang_row['jenis_penilaian'] == 1) {
                                        // Menampilkan radio button jika jenis penilaian 1
                                        echo "<div class='custom-radio'>&nbsp;&nbsp;&nbsp;&nbsp;";
                                        $checked = ($nilai_penilaian == $rentang_row['nilai_rentang']) ? 'checked' : '';
                                        if (is_null($row_kriteria['id_subkriteria'])) {
                                            echo "<input type='radio' name='rentang_kriteria_" . $row_kriteria['id_kriteria'] . "' value='" . $rentang_row['nilai_rentang'] . "' $checked>";
                                        } else {
                                            echo "<input type='radio' name='rentang_subkriteria_" . $row_kriteria['id_subkriteria'] . "' value='" . $rentang_row['nilai_rentang'] . "' $checked>";
                                        }
                                        echo "<label>" . $rentang_row['uraian'] . "</label>";
                                        echo "</div>";
                                    } elseif ($rentang_row['jenis_penilaian'] == 2) {
                                        // Menampilkan input teks number jika jenis penilaian 2
                                        $nilai_terisi = !is_null($nilai_penilaian) ? $nilai_penilaian : '';

                                        echo "<input type='number' name='rentang_kriteria_" . $row_kriteria['id_kriteria'] . "' class='form-control' value='$nilai_terisi' placeholder='Masukkan nilai'>";
                                    }
                                }
                            }
                        }
                    } else {
                        echo "Tidak ada data kriteria.";
                    }
                    ?>

                    <div class="form-group cols-sm-6">
                        <button type="submit" name="edit" class="btn btn-secondary btn-icon-split" style="background-color: #167395;">
                            <span class="icon text-white-50">
                                <i class="fas fa-user-edit"></i>
                            </span>
                            <span class="text">Update</span>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</body>

</html>
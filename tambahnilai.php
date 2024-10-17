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

    <title>INPUT PENILAIAN</title>
</head>
<style type="text/css">
    body {
        font-family: "Verdana";
    }

    /* Gaya untuk label radio button */
    .custom-radio {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    /* Gaya untuk input radio */
    .custom-radio input[type="radio"] {
        margin-right: 10px;
        accent-color: #167395;
        width: 20px;
        height: 20px;
    }

    /* Gaya untuk label */
    .custom-radio label {
        color: #333;
        font-size: 14px;
    }

    /* Mengubah warna saat radio button terpilih */
    .custom-radio input[type="radio"]:checked+label {
        color: #167395;
        font-weight: bold;
    }

    /* Gaya untuk input number */
    .form-control {
        margin-bottom: 15px;
        padding: 8px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    /* Gaya untuk tombol */
    .btn {
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        color: white;
        cursor: pointer;
    }
</style>

<body id="page-top">
    <br>
    <div class="card shadow" style="width: 50%;">
        <div class="card-header m-0 font-weight-bold" style="text-align: center; background-color: #167395; color: white">Input Penilaian</div>
        <div class="card-body">
            <form action="simpannilai.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group cols-sm-6">
                    <?php
                    require 'config/koneksi.php';
                    // Query untuk mengambil alternatif dengan status_alternatif = 1
                    $sql = mysqli_query($conn, "SELECT * FROM alternatif WHERE status_alternatif = '1'");

                    // Query untuk mengambil alternatif yang sudah ada di tabel penilaian
                    $sql_penilaian = mysqli_query($conn, "SELECT DISTINCT id_alternatif FROM penilaian");
                    $penilaian_terdahulu = [];
                    while ($row_penilaian = mysqli_fetch_assoc($sql_penilaian)) {
                        $penilaian_terdahulu[] = $row_penilaian['id_alternatif'];
                    }
                    ?>

                    <!-- Bagian Dropdown Alternatif -->
                    <div class="form-group cols-sm-6">
                        <label style="color: black"><strong>Nama:</strong></label>
                        <select name="alternatif" class="form-control" style="color: black">
                            <option value="">Pilih Nama</option>
                            <?php
                            while ($data = mysqli_fetch_array($sql)) {
                                // Jika alternatif sudah dinilai, tambahkan atribut disabled
                                if (in_array($data['id_alternatif'], $penilaian_terdahulu)) {
                                    echo "<option value='" . $data['id_alternatif'] . "' disabled>" . $data['nama_alternatif'] . " (Sudah Dinilai)</option>";
                                } else {
                                    echo "<option value='" . $data['id_alternatif'] . "'>" . $data['nama_alternatif'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    // Query untuk mengambil data rentang, kriteria, dan subkriteria
                    $sql = "SELECT r.id_rentang, r.uraian, r.nilai_rentang, k.id_kriteria, k.nama_kriteria, 
                            sk.id_subkriteria, sk.nama_subkriteria 
                            FROM Rentang r
                            LEFT JOIN Kriteria k ON r.id_kriteria = k.id_kriteria
                            LEFT JOIN Subkriteria sk ON r.id_subkriteria = sk.id_subkriteria
                            ORDER BY k.id_kriteria, sk.id_subkriteria, r.id_rentang";
                    $result = $conn->query($sql);

                    // Membuat radio button list per kriteria atau subkriteria
                    if ($result->num_rows > 0) {
                        $current_group = ""; // Untuk melacak kriteria atau subkriteria saat ini
                        $current_subcriteria = ""; // Untuk melacak subkriteria saat ini

                        while ($row = $result->fetch_assoc()) {
                            // Cek apakah id_subkriteria bernilai NULL
                            if ($row['id_subkriteria'] == NULL) {
                                $group_name = "<strong>Kriteria: " . $row['nama_kriteria'] . "</strong>";
                                $group_id = $row['id_kriteria'];
                                $current_subcriteria = ""; // Reset subkriteria saat masuk ke kriteria baru
                            } else {
                                $group_name = "<strong>Kriteria: " . $row['nama_kriteria'] . "</strong><br>" . "&nbsp;&nbsp;&nbsp;&nbsp;Sub-Kriteria: " . $row['nama_subkriteria'];
                                $group_id = $row['id_subkriteria'];
                            }

                            // Jika grup kriteria berubah, tampilkan nama grup (kriteria)
                            if ($current_group != $group_name) {
                                // Tampilkan nama grup (kriteria)
                                echo "<label style='color: black'>" . $group_name . "</label>";
                                $current_group = $group_name; // Update grup saat ini
                                $current_subcriteria = ""; // Reset subkriteria saat masuk ke kriteria baru
                            }

                            // Jika nilai_rentang tidak NULL, tampilkan radio button dengan uraian
                            if (!is_null($row['nilai_rentang'])) {
                                echo "<div class='custom-radio'>";
                                // Jika id_subkriteria NULL, gunakan id_kriteria sebagai name
                                if ($row['id_subkriteria'] == NULL) {
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='rentang_kriteria_" . $row['id_kriteria'] . "' value='" . $row['nilai_rentang'] . "' id='rentang_" . $row['id_rentang'] . "'>";
                                } else {
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='rentang_subkriteria_" . $row['id_subkriteria'] . "' value='" . $row['nilai_rentang'] . "' id='rentang_" . $row['id_rentang'] . "'>";
                                }
                                echo "<label for='rentang_" . $row['id_rentang'] . "'>" . $row['uraian'] . "</label>";
                                echo "</div>";
                            } else {
                                // Jika nilai_rentang NULL, tampilkan input number dengan nama unik
                                echo "<input type='number' name='rentang_kriteria_" . $group_id . "' class='form-control' placeholder='Masukkan nilai'>";
                            }
                        }
                    } else {
                        echo "Tidak ada data";
                    }
                    ?>
                    <div class="form-group cols-sm-6">
                        <button type="submit" name="edit" class="btn btn-secondary btn-icon-split" style="background-color: #167395;">
                            <span class="icon text-white-50">
                                <i class="fas fa-user-edit"></i>
                            </span>
                            <span class="text">Simpan</span>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</body>

</html>
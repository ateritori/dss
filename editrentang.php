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

    <title>EDIT DATA RENTANG</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- JQuery (for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<style type="text/css">
    form {
        width: 100%;
    }

    body {
        font-family: "Verdana";
    }
</style>

<body id="page-top">
    <br>
    <div class="card shadow" style="width: 50%;">
        <div class="card-header m-0 font-weight-bold" style="text-align: center; background-color: #167395; color: white">Edit Rentang</div>
        <?php
        require 'config/koneksi.php';
        $sql = mysqli_query($conn, "SELECT * FROM Rentang WHERE id_rentang='$_GET[id]'");
        if ($data = mysqli_fetch_array($sql)) {
            // Mengambil data kriteria
            $kriteria_sql = mysqli_query($conn, "SELECT * FROM Kriteria");

            // Ambil sub-kriteria berdasarkan id_kriteria yang sudah ada di Rentang
            $id_kriteria = $data['id_kriteria'];
            $subkriteria_sql = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '$id_kriteria'");
        ?>
            <div class="card-body">
                <form action="simpaneditrentang.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group cols-sm-6">
                        <label style="color: black">ID Rentang</label>
                        <input type="text" name="id_rentang" style="color: black" value="<?php echo $data['id_rentang']; ?>" class="form-control" readonly>
                    </div>

                    <!-- Dropdown untuk Kriteria -->
                    <div class="form-group cols-sm-6">
                        <label style="color: black">Kriteria</label>
                        <select name="id_kriteria" id="id_kriteria" class="form-control" style="color: black" disabled>
                            <option value="">-- Pilih Kriteria --</option>
                            <?php
                            while ($kriteria = mysqli_fetch_array($kriteria_sql)) {
                                $selected = ($data['id_kriteria'] == $kriteria['id_kriteria']) ? 'selected' : '';
                                echo "<option value='{$kriteria['id_kriteria']}' $selected>{$kriteria['nama_kriteria']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dropdown untuk Sub-Kriteria -->
                    <div class="form-group cols-sm-6">
                        <label style="color: black">Sub-Kriteria</label>
                        <select name="id_subkriteria" id="id_subkriteria" class="form-control" style="color: black" disabled>
                            <?php
                            if (mysqli_num_rows($subkriteria_sql) > 0) {
                                while ($row = mysqli_fetch_array($subkriteria_sql)) {
                                    $selected = ($data['id_subkriteria'] == $row['id_subkriteria']) ? 'selected' : '';
                                    echo '<option value="' . $row['id_subkriteria'] . '" ' . $selected . '>' . $row['nama_subkriteria'] . '</option>';
                                }
                            } else {
                                echo '<option value="" selected>Tidak Ada Sub Kriteria</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dropdown Jenis Penilaian -->
                    <div class="form-group cols-sm-6">
                        <label style="color: black">Jenis Penilaian</label>
                        <select name="jenis_penilaian" id="jenis_penilaian" class="form-control" style="color: black">
                            <option value="">-- Pilih Jenis Penilaian --</option>
                            <option value="1" <?php echo ($data['jenis_penilaian'] == '1') ? 'selected' : ''; ?>>Skala</option>
                            <option value="2" <?php echo ($data['jenis_penilaian'] == '2') ? 'selected' : ''; ?>>Manual</option>
                        </select>
                    </div>

                    <!-- Field untuk Uraian dan Nilai (Akan disembunyikan jika "Manual" dipilih) -->
                    <div id="field_skala">
                        <div class="form-group cols-sm-6">
                            <label style="color: black">Uraian</label>
                            <input type="text" name="uraian" style="color: black" value="<?php echo $data['uraian']; ?>" class="form-control">
                        </div>

                        <div class="form-group cols-sm-6">
                            <label style="color: black">Nilai</label>
                            <input type="number" step="any" name="value" style="color: black" value="<?php echo $data['nilai_rentang']; ?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group cols-sm-6">
                        <button type="submit" name="edit" class="btn btn-secondary btn-icon-split" style="background-color: #167395;">
                            <span class="icon text-white-50">
                                <i class="fas fa-user-edit"></i>
                            </span>
                            <span class="text">Edit</span>
                        </button>
                    </div>

                </form>
            </div>
        <?php
        }
        ?>
    </div>

    <!-- Script to toggle Uraian and Value fields -->
    <script type="text/javascript">
        $(document).ready(function() {
            function toggleFields() {
                if ($('#jenis_penilaian').val() == '1') {
                    $('#field_skala').show();
                } else {
                    $('#field_skala').hide();
                }
            }

            // Inisialisasi saat halaman pertama kali dimuat
            toggleFields();

            // Perubahan jika ada perubahan pada jenis penilaian
            $('#jenis_penilaian').change(function() {
                toggleFields();
            });
        });
    </script>

</body>

</html>
<?php
// Memulai session untuk autentikasi
session_start();

if (!isset($_SESSION['username'])) {
?>
    <script type="text/javascript">
        alert('Anda Belum Login');
        window.location = 'index.php';
    </script>
<?php
    exit;
}

// Menghubungkan ke database
require 'config/koneksi.php';

$namaErr = $nilaiErr = $kriteriaErr = $subkriteriaErr = NULL;
$nama = $nilai = NULL;
$id_kriteria = $id_subkriteria = NULL; // Tambahkan variabel ini
$flag = true;

// Fungsi validasi untuk input form
function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Menangani submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi uraian (nama)
    if (isset($_POST["nama"]) && $_POST["nama"] !== "") {
        $nama = validate($_POST['nama']);  // Jika tidak kosong, lakukan validasi
    } else {
        $nama = NULL;  // Jika kosong, set ke NULL
    }

    // Validasi nilai
    if (isset($_POST["nilai"]) && $_POST["nilai"] !== "") {
        $nilai = validate($_POST['nilai']);  // Jika tidak kosong, lakukan validasi
    } else {
        $nilai = NULL;  // Jika kosong, set ke NULL
    }

    // Validasi kriteria
    if (empty($_POST["id_kriteria"])) {
        $kriteriaErr = "*Kriteria belum dipilih";
        $flag = false;
    } else {
        $id_kriteria = $_POST['id_kriteria'];
    }

    // Validasi Jenis Penilaian
    if (empty($_POST["jenis_penilaian"])) {
        $jenispenilaianErr = "*Jenis Penilaian belum dipilih";
        $flag = false;
    } else {
        $jenis_penilaian = $_POST['jenis_penilaian'];
    }

    // Validasi subkriteria, jika tidak dipilih, biarkan null
    $id_subkriteria = isset($_POST["id_subkriteria"]) && !empty($_POST["id_subkriteria"]) ? $_POST["id_subkriteria"] : NULL;

    // Jika tidak ada error, lakukan insert ke database
    if ($flag) {
        $sql = "INSERT INTO Rentang (id_kriteria, id_subkriteria, jenis_penilaian, uraian, nilai_rentang) VALUES (?, ?, ?, ?, ?)";

        // Siapkan query
        $stmt = $conn->prepare($sql);

        // Tampilkan error jika ada masalah pada persiapan statement
        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        // Jika nilai kosong (null) diubah agar disimpan sebagai NULL di database
        if ($nilai === NULL) {
            $stmt->bind_param("iisss", $id_kriteria, $id_subkriteria, $jenis_penilaian, $nama, $nilai);
        } else {
            $stmt->bind_param("iissd", $id_kriteria, $id_subkriteria, $jenis_penilaian, $nama, $nilai);
        }

        // Eksekusi statement
        if ($stmt->execute()) {
            echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=rentang';</script>";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
    }
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

    <title>TAMBAH DATA RENTANG</title>

    <script>
        function getSubKriteria(id_kriteria) {
            if (id_kriteria === "") {
                document.getElementById("subkriteria").innerHTML = "";
                return;
            }

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("subkriteria").innerHTML = this.responseText;
                    setTimeout(function() {
                        var uraianInput = document.getElementById("uraian");
                        if (uraianInput) {
                            uraianInput.focus();
                        }
                    }, 100);
                }
            };
            xmlhttp.open("GET", "get_subkriteria.php?id_kriteria=" + id_kriteria, true);
            xmlhttp.send();
        }

        function toggleInputFields() {
            var jenisPenilaian = document.getElementById('jenis_penilaian').value;
            var uraianField = document.getElementById('uraianField');
            var nilaiField = document.getElementById('nilaiField');

            if (jenisPenilaian === "1") {
                // Jika "Skala" dipilih, tampilkan field Uraian dan Nilai
                uraianField.style.display = 'block';
                nilaiField.style.display = 'block';
            } else {
                // Jika "Manual" dipilih, sembunyikan field Uraian dan Nilai
                uraianField.style.display = 'none';
                nilaiField.style.display = 'none';
            }
        }
    </script>

</head>

<body id="page-top">
    <br>
    <div class="card shadow" style="width: 50%;">
        <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Data Rentang</div>
        <div class="card-body">
            <!-- Form untuk input data Rentang -->
            <form method="POST" action="">
                <?php
                // Query untuk mengambil semua data dari tabel Kriteria
                $sql = "SELECT id_kriteria, nama_kriteria FROM Kriteria";
                $result = mysqli_query($conn, $sql);
                ?>

                <!-- Dropdown Kriteria -->
                <div class="form-group cols-sm-6">
                    <label style="color: black">Kriteria:</label>
                    <select name="id_kriteria" id="id_kriteria" onchange="getSubKriteria(this.value)" class="form-control" style="color: black">
                        <option value="">-- Pilih Kriteria --</option>
                        <?php
                        // Looping data kriteria ke dalam dropdown
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id_kriteria'] . "'>" . $row['nama_kriteria'] . "</option>";
                        }
                        ?>
                    </select>
                    <span class="error"><?= $kriteriaErr ?></span>
                </div>

                <!-- Dropdown SubKriteria -->
                <div class="form-group cols-sm-6">
                    <label style="color: black">SubKriteria:</label>
                    <div id="subkriteria">
                    </div>
                    <span class="error"><?= $subkriteriaErr ?></span>
                </div>

                <div class="form-group cols-sm-6">
                    <label style="color: black">Jenis Penilaian:</label>
                    <select name="jenis_penilaian" id="jenis_penilaian" onchange="toggleInputFields()" class="form-control" style="color: black">
                        <option value="">-- Pilih Jenis Penilaian --</option>
                        <option value="1">Skala</option>
                        <option value="2">Manual</option>
                    </select>
                    <span class="error"><?= $jenispenilaianErr ?></span>
                </div>

                <div class="form-group cols-sm-6" id="uraianField" style="display: none;">
                    <label style="color: black">Uraian: (Silakan Dikosongi Jika Jenis Penilaiannya Manual)</label>
                    <input type="text" name="nama" id="uraian" value="<?= $nama ?>" class="form-control" style="color: black">
                    <span class="error"><?= $namaErr ?></span>
                </div>

                <div class="form-group cols-sm-6" id="nilaiField" style="display: none;">
                    <label style="color: black">Value: (Silakan Dikosongi Jika Jenis Penilaiannya Manual)</label>
                    <input type="number" name="nilai" id="nilai" value="<?= $nilai ?>" class="form-control" style="color: black">
                    <span class="error"><?= $nilaiErr ?></span>
                </div>

                <div class="form-group cols-sm-6">
                    <button type="submit" class="btn btn-secondary btn-icon-split" style="background: #167395" name="submit">
                        <span class="icon text-white-50">
                            <i class="fas fa-user-check"></i>
                        </span>
                        <span class="text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
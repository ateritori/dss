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

    <title>TAMBAH DATA</title>

</head>
<style type="text/css">
    form {
        width: 100%;
    }

    body {
        font-family: "Verdana";
    }

    .error {
        font-size: 11px;
        font-weight: bold;
        position: relative;
        left: 10px;
        margin-bottom: 6px;
        color: firebrick;
    }
</style>

<?php
require 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek apakah kriteria kosong
    if (empty($_POST['subkriteria'])) {
        echo "<script>alert('Sub-Kriteria tidak boleh kosong'); window.location = 'dashboard.php?url=tambahsub&id_kriteria=$_GET[id_kriteria]'  ;</script>";
        exit();
    }
    // Mendapatkan input dari form
    $subkriteria = $_POST['subkriteria'];
    $bobotsub = isset($_POST['bobotsub']) && $_POST['bobotsub'] !== "" ? $_POST['bobotsub'] : NULL;
    $atribut = isset($_POST['atribut']) && $_POST['atribut'] !== "" ? $_POST['atribut'] : NULL;

    // Menyiapkan query dengan prepared statements
    $query = "INSERT INTO SubKriteria (id_kriteria, nama_subkriteria, bobot_subkriteria, tipe_subkriteria) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Bind parameters (isi dengan nilai-nilai yang diterima dari form)
    $stmt->bind_param("isds", $_GET['id_kriteria'], $subkriteria, $bobotsub, $atribut);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=kriteria';</script>";
    } else {
        echo "<script>alert('Gagal Menyimpan Data: " . $conn->error . "'); window.location = 'dashboard.php?url=kriteria';</script>";
    }

    // Tutup statement
    $stmt->close();
}

// Mendapatkan id_kriteria dari request (misalnya dari URL)
$id_kriteria = $_GET['id_kriteria'];

// Menyiapkan query dengan prepared statements
$query = "SELECT * FROM Kriteria WHERE id_kriteria = ?";
$stmt = $conn->prepare($query);

// Bind parameter (di sini "i" artinya integer)
$stmt->bind_param("i", $id_kriteria);

// Eksekusi query
$stmt->execute();

// Mendapatkan hasil
$result = $stmt->get_result();

// Memeriksa apakah ada data yang ditemukan
if ($result->num_rows > 0) {
    // Ambil satu baris data
    $row = $result->fetch_assoc();
    $id_kriteria = $row['id_kriteria'];
    $nama_kriteria = $row['nama_kriteria'];
} else {
    echo "Tidak ada data yang ditemukan untuk id_kriteria: $id_kriteria";
}
?>

<body id="page-top">
    <br>
    <div class="card shadow" style="width: 50%;">
        <div class=" card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Sub-Kriteria</div>
        <div class="card-body">
            <form method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group cols-sm-6">
                    <label>ID Kriteria</label>
                    <input type="text" name="id_kriteria" value="<?php echo $id_kriteria; ?>" class="form-control" readonly>
                </div>

                <div class="form-group cols-sm-6">
                    <label>Kriteria</label>
                    <input type="text" name="kriteria" value="<?php echo $nama_kriteria; ?>" class="form-control" readonly>
                </div>
                <div class="form-group cols-sm-6">
                    <label>Sub-Kriteria</label>
                    <input type="text" name="subkriteria" value="<?= $kriteria ?>" class="form-control" required autofocus>
                    <span class="error"><?= $kriteriaErr ?></span>
                </div>

                <div class="form-group cols-sm-6">
                    <label>Bobot (antara 0.00 s/d 1.00)</label>
                    <input type="number" step="any" name="bobotsub" value="<?= $bobot ?>" class="form-control">
                    <span class="error"><?= $bobotErr ?></span>
                </div>

                <div class="form-group cols-sm-6">
                    <label>Tipe Sub-Kriteria</label>
                    <select class="form-control" name="atribut" value="<?= $atribut ?>">
                        <option hidden></option>
                        <option value="cost">Cost</option>
                        <option value="benefit">Benefit</option>
                    </select>
                    <span class="error"><?= $atributErr ?></span>
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
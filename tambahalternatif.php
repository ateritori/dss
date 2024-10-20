<?php
$namaErr = NULL;
$nama = NULL;
$flag = true;

function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = validate($_POST['nama']);

    if ($flag) {
        $sql = "INSERT INTO Alternatif(nama_alternatif) VALUES('$nama')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=alternatif';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<div class="card shadow mt-3" style="width: 50%;">
    <div class=" card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Alternatif</div>
    <div class="card-body">
        <form method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group cols-sm-6">
                <label>Nama Alternatif</label>
                <input type="text" name="nama" value="<?= $nama ?>" class="form-control" required>
                <span class="error"><?= $namaErr ?></span>
            </div>
            <div class="form-group cols-sm-6">
                <button type="submit" class="btn btn-dark btn-sm">
                    Simpan
                </button>
                <button type="button" class="btn btn-dark btn-sm" onclick="history.back()">
                    Batal</i>
                </button>
            </div>
        </form>
    </div>
</div>
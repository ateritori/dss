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

    <title>EDIT DATA</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

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
        <div class="card-header m-0 font-weight-bold" style="text-align: center; background-color: #167395; color: white">Edit Sub-Kriteria</div>
        <?php
        require 'config/koneksi.php';
        $sql = mysqli_query($conn, "select * from SubKriteria where id_subkriteria='$_GET[id]' ");
        if ($data = mysqli_fetch_array($sql)) {
        ?>
            <div class="card-body">
                <form action="simpaneditsubkrit.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group cols-sm-6">
                        <label>ID Kriteria</label>
                        <input type="text" name="id_kriteria" value="<?php echo $data['id_kriteria']; ?>" class="form-control" readonly>
                    </div>
                    <div class="form-group cols-sm-6">
                        <label>ID Sub-Kriteria</label>
                        <input type="text" name="id_subkriteria" value="<?php echo $data['id_subkriteria']; ?>" class="form-control" readonly>
                    </div>
                    <div class="form-group cols-sm-6">
                        <label>Sub-Kriteria</label>
                        <input type="text" name="subkriteria" value="<?php echo $data['nama_subkriteria']; ?>" class="form-control">
                    </div>

                    <div class="form-group cols-sm-6">
                        <label>Bobot</label>
                        <input type="number" step="any" name="bobotsub" value="<?php echo $data['bobot_subkriteria']; ?>" class="form-control">
                    </div>

                    <div class="form-group cols-sm-6">
                        <label>Atribut</label>
                        <select class="form-control" name="atribut">
                            <option value="benefit" <?php if ($data['tipe_subkriteria'] == 'benefit') {
                                                        echo 'selected';
                                                    } ?>>Benefit</option>
                            <option value="cost" <?php if ($data['tipe_subkriteria'] == 'cost') {
                                                        echo 'selected';
                                                    } ?>>Cost</option>
                            <option value="" <?php if (is_null($data['tipe_subkriteria'])) {
                                                    echo 'selected';
                                                } ?>>NULL</option>
                        </select>
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
            <?php } ?>
            </div>
    </div>

</body>

</html>
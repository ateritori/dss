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

    <title>HOME PAGE</title>

</head>
<style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    }

    th,
    td {
        text-align: left;
        padding: 16px;
    }

    body {
        font-family: "Verdana";
    }

    .navbar {
        width: 100%;
        background: orange;
        overflow: auto;
        color: white;
    }
</style>


<body>
    <br>
    <div class="card shadow mb-5">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Rentang</div>

        <div class="card-body">
            <a href="dashboard.php?url=tambahrentang" class="btn btn-success btn-icon-split" style="background: #167395">
                <span class="icon text-white-50">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span class="text" style="font-weight: bold;">Tambah</span>
            </a>
            <br><br>

            <div>
                <p style="color: black; font-weight: bold">Tabel Berikut Ini Menampilkan Rentang Penilaian dari Masing-Masing Kriteria / Sub-Kriteria</p>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" style="color: black">No</th>
                                <th scope="col" style="color: black">Kriteria</th>
                                <th scope="col" style="color: black">Sub-Kriteria</th>
                                <th scope="col" style="color: black">Jenis Penilaian</th>
                                <th scope="col" style="color: black">Uraian</th>
                                <th scope="col" style="color: black">Nilai</th>
                                <th scope="col" style="color: black" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <?php
                        require 'config/koneksi.php';
                        $no = 0;
                        $sql = mysqli_query($conn, "
                    SELECT r.*, k.nama_kriteria, s.nama_subkriteria 
                    FROM Rentang r
                    LEFT JOIN Kriteria k ON r.id_kriteria = k.id_kriteria
                    LEFT JOIN SubKriteria s ON r.id_subkriteria = s.id_subkriteria
                ");
                        while ($data = mysqli_fetch_array($sql)) {
                            $id_rentang = $data['id_rentang'];
                            $no++;
                        ?>
                            <tbody>
                                <tr>
                                    <th scope="row" style="color: black"><?php echo "$no"; ?></th>
                                    <td style="color: black"><?php echo $data['nama_kriteria']; ?></td>
                                    <td style="color: black"><?php echo $data['nama_subkriteria']; ?></td>
                                    <td style="color: black">
                                        <?php
                                        if ($data['jenis_penilaian'] == 1) {
                                            echo "Skala";
                                        } else {
                                            echo "Manual";
                                        }
                                        ?></td>
                                    <td style="color: black">
                                        <?php echo $data['uraian']; ?>
                                    </td>
                                    <td style="color: black"><?php echo $data['nilai_rentang']; ?></td>
                                    <td>
                                        <a href="dashboard.php?url=editrentang&id=<?php echo $data['id_rentang']; ?>" class="btn btn-secondary btn-circle" style="background: #2b4280">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#modalDelete" data-toggle="modal" onclick="$('#modalDelete #formDelete').attr('action', 'hapusrentang.php?id=<?php echo $data['id_rentang']; ?>')" class="btn btn-danger btn-circle" style="background: #c43939">
                                            <i class="fa fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        <?php
                        }
                        ?>
                        <!-- Modal Hapus -->
                        <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: #c43939">
                                        <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold; color: white;">Ingin Hapus Data Ini?</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="color: white;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus data ini?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form id="formDelete" action="" method="POST">
                                            <button class="btn btn-danger" style="background: #c43939" type="submit">Hapus</button>
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </table>
                </div>
            </div>
        </div>

</body>

</html>
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
    background-color: orange;
    overflow: auto;
    color: white;
  }

  .total-row {
    font-weight: bold;
    background-color: #f2f2f2;
  }
</style>


<body>
  <br>
  <div class="card shadow mb-5">
    <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Kriteria dan Sub-Kriteria</div>

    <div class="card-body">
      <a href="dashboard.php?url=tambahkriteria" class="btn btn-success btn-icon-split" style="background: #167395">
        <span class="icon text-white-50">
          <i class="fas fa-user-plus"></i>
        </span>
        <span class="text" style="font-weight: bold;">Tambah Kriteria</span>
      </a>

      <br>
      <br>

      <div>
        <p style="color: black; font-weight: bold">Tabel Kriteria, Sub-Kriteria, Bobot dan Tipe Untuk Penilaian Kandidat</p>
        <div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th scope="col" style="color: black">No</th>
                  <th scope="col" style="color: black">Simbol</th>
                  <th scope="col" style="color: black">Kriteria/Sub Kriteria</th>
                  <th scope="col" style="color: black">Bobot</th>
                  <th scope="col" style="color: black">Tipe</th>
                  <th scope="col" style="color: black">Aksi</th>
                  <th scope="col" style="color: black">Sub-Kriteria</th>
                </tr>
              </thead>

              <?php
              require 'config/koneksi.php';
              $no = 0;
              $bobotkrit = 0; // Total bobot dari Kriteria
              $sql = mysqli_query($conn, "SELECT * FROM kriteria");
              while ($data = mysqli_fetch_array($sql)) {
                $bobotkrit += $data['bobot_kriteria']; // Menghitung total bobot kriteria
                $no++;
              ?>

                <tbody>
                  <tr>
                    <th scope="row" style="color: black"><?php echo "$no"; ?></th>
                    <th scope="row" style="color: black">C<sub><?php echo "$no"; ?></sub></th>
                    <td style="color: black"><?php echo $data['nama_kriteria']; ?></td>
                    <td style="color: black"><?php echo $data['bobot_kriteria']; ?></td>
                    <td style="color: black"><?php echo $data['tipe_kriteria']; ?></td>
                    <td>
                      <a href="dashboard.php?url=editkriteria&id=<?php echo $data['id_kriteria']; ?>" class="btn btn-secondary btn-circle" style="background: #2b4280">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="#modalDelete" data-toggle="modal" onclick="$('#modalDelete #formDelete').attr('action', 'hapuskriteria.php?id=<?php echo $data['id_kriteria']; ?>' )" class="btn btn-danger btn-circle" style="background: #c43939">
                        <i class="fa fa-trash-alt"></i>
                      </a>
                    </td>
                    <td>
                      <a href="dashboard.php?url=tambahsub&id_kriteria=<?php echo $data['id_kriteria']; ?>" class="btn btn-secondary btn-circle" style="background: #006400">
                        <i class="fa fa-plus"></i>
                      </a>
                    </td>
                  </tr>
                <?php
              }
                ?>
                </tbody>
                <!-- Menambahkan baris total bobot di bawah tabel -->
                <tfoot>
                  <tr class="total-row">
                    <td colspan="3" style="text-align: right; color: black;">Total Bobot:</td>
                    <td style="color: black;"><?php echo number_format($bobotkrit, 2); ?></td>
                    <td colspan="3"></td>
                  </tr>
                </tfoot>
            </table>
          </div>
        </div>

        <!-- Delete-->
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
                <form id="formDelete" action="" method="POST">
                  <button class="btn btn-danger" style="background: #c43939" type="submit">Hapus</button>
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </form>

              </div>
            </div>
          </div>
        </div>


        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<div class="card shadow mt-3">
  <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA KRITERIA</div>
  <div class="card-body">
    <a href="dashboard.php?url=tambahkriteria " class="btn btn-outline-dark" title="Tambah Kriteria">
      <i class="bi bi-plus-square-fill"></i>
    </a>
    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr class="text-center">
            <th scope="col">No</th>
            <th scope="col">Simbol</th>
            <th scope="col">Kriteria</th>
            <th scope="col">Tipe</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 0;
          $sql = mysqli_query($conn, "SELECT * FROM Kriteria");
          while ($data = mysqli_fetch_array($sql)) {
            $no++;
          ?>
            <tr>
              <th scope="row"><?php echo "$no"; ?></th>
              <th scope="row">C<sub><?php echo "$no"; ?></sub></th>
              <td><?php echo $data['nama_kriteria']; ?></td>
              <td><?php echo $data['tipe_kriteria']; ?></td>
              <td>
                <!-- Tombol Edit -->
                <a href="dashboard.php?url=editkeriteria&id=<?php echo $data['id_kriteria']; ?>"
                  class="btn btn-outline-dark btn-sm"
                  title="Edit Kriteria">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <!-- Tombol Hapus -->
                <a href="#modalDelete"
                  data-toggle="modal"
                  onclick="$('#modalDelete #formDelete').attr('action', 'hapuskriteria.php?id=<?php echo $data['id_kriteria']; ?>' )"
                  class="btn btn-outline-dark btn-sm"
                  title="Hapus Alternatif">
                  <i class="bi bi-x-square-fill"></i>
                  <!-- Tombol Sub-Kriteria -->
                  <a href="dashboard.php?url=tambahsub&id=<?php echo $data['id_kriteria']; ?>"
                    class="btn btn-outline-dark btn-sm"
                    title="Tambah Sub Kriteria">
                    <i class="bi bi-view-list"></i>
                  </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
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
<div class="card shadow mt-3">
  <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA ALTERNATIF</div>
  <div class="card-body">
    <a href="dashboard.php?url=tambahalternatif" class="btn btn-outline-dark" title="Tambah Alternatif">
      <i class="bi bi-plus-square-fill"></i>&nbsp;Alternatif
    </a>
    <a href="ubahstatussemuaalternatif.php?status=aktif" class="btn btn-outline-dark" title="Aktifkan Semua">
      <i class="bi bi-toggle-on"></i>
    </a>
    <a href="ubahstatussemuaalternatif.php?status=nonaktif" class="btn btn-outline-dark" title="Non-Aktifkan Semua">
      <i class="bi bi-toggle-off"></i>
    </a>
    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr class="text-center">
            <th scope="col">No</th>
            <th scope="col">Nama Kandidat</th>
            <th scope="col">Status Kandidat</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 0;
          $sql = mysqli_query($conn, "SELECT * FROM Alternatif");
          while ($data = mysqli_fetch_array($sql)) {
            $id_alternatif = $data['id_alternatif'];
            $no++;
            $status_alternatif = $data['status_alternatif']; // Ambil status dari database
            $status_text = ($status_alternatif == 1) ? 'Aktif' : 'Non Aktif'; // Ubah status menjadi teks
          ?>
            <tr>
              <th scope="row"><?php echo "$no"; ?></th>
              <td><?php echo $data['nama_alternatif']; ?></td>
              <td><?php echo $status_text; ?></td>
              <td>
                <!-- Tombol Edit -->
                <a href="dashboard.php?url=editalternatif&id=<?php echo $data['id_alternatif']; ?>"
                  class="btn btn-outline-dark btn-sm"
                  title="Edit Alternatif">
                  <i class="bi bi-pencil-square"></i>
                </a>

                <!-- Tombol Hapus -->
                <a href="#modalDelete"
                  data-toggle="modal"
                  onclick="$('#modalDelete #formDelete').attr('action', 'hapusalternatif.php?id=<?php echo $data['id_alternatif']; ?>' )"
                  class="btn btn-outline-dark btn-sm"
                  title="Hapus Alternatif">
                  <i class="bi bi-x-square-fill"></i>
                </a>

                <!-- Tombol Ubah Status -->
                <a href="ubahstatusalternatif.php?id=<?php echo $data['id_alternatif']; ?>&status=<?php echo $status_alternatif; ?>"
                  class="btn btn-outline-dark btn-sm"
                  title="Ubah Status Alternatif">
                  <?php if ($status_alternatif == 1): ?>
                    <i class="bi bi-toggle-on"></i> <!-- Icon untuk status aktif -->
                  <?php else: ?>
                    <i class="bi bi-toggle-off"></i> <!-- Icon untuk status non-aktif -->
                  <?php endif; ?>
                </a>
              </td>
            </tr>
        </tbody>
      <?php } ?>
      </table>
    </div>
  </div>
</div>

<!-- Delete Modal -->
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
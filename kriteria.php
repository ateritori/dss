<div class="card shadow mt-3">
  <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA KRITERIA</div>
  <div class="card-body">
    <a href="dashboard.php?url=tambahkriteria" class="btn btn-outline-dark" title="Tambah Kriteria">
      <i class="bi bi-plus-square-fill"></i>&nbsp;Kriteria
    </a>
    <div class="table-responsive mt-3">
      <table class="table table-bordered">
        <thead>
          <tr class="text-center">
            <th scope="col">No</th>
            <th scope="col">Simbol</th>
            <th scope="col">Kriteria/Sub-Kriteria</th>
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
                <a href="dashboard.php?url=editkriteria&id=<?php echo $data['id_kriteria']; ?>" class="btn btn-outline-dark btn-sm" title="Edit Kriteria">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <a href="#modalDelete"
                  data-toggle="modal"
                  onclick="$('#modalDelete #formDelete').attr('action', 'hapuskriteria.php?id=<?php echo $data['id_kriteria']; ?>' )"
                  class="btn btn-outline-dark btn-sm"
                  title="Hapus Kriteria">
                  <i class="bi bi-x-square-fill"></i>
                </a>
              </td>
            </tr>
            <?php
            // Cek jika kriteria memiliki sub-kriteria
            $subQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = " . $data['id_kriteria']);
            if (mysqli_num_rows($subQuery) > 0) {
              $sub_no = 0; // Inisialisasi urutan sub-kriteria
              // Tampilkan sub-kriteria
              while ($subData = mysqli_fetch_array($subQuery)) {
                $sub_no++; // Increment urutan sub-kriteria
                echo '<tr>';
                echo '<td></td>'; // Kosongkan untuk no
                echo '<td style="text-align: right;">SC<sub>' . $no . $sub_no . '</sub></td>'; // Simbol sub-kriteria
                echo '<td>&nbsp;&nbsp;&nbsp;&nbsp; - ' . $subData['nama_subkriteria'] . '</td>'; // Indentasi sub-kriteria
                echo '<td>' . $subData['tipe_subkriteria'] . '</td>'; // Tipe sub-kriteria
                echo '<td>';
                echo '<a href="dashboard.php?url=editkriteria&id=' . $data['id_kriteria'] . '" class="btn btn-outline-dark btn-sm" title="Edit Sub-Kriteria"><i class="bi bi-pencil-square"></i></a>';
                echo '<a href="#modalDelete" data-toggle="modal" onclick="$(\'#modalDelete #formDelete\').attr(\'action\', \'hapussubkriteria.php?id=' . $subData['id_subkriteria'] . '\')" class="btn btn-outline-dark btn-sm" title="Hapus Sub-Kriteria"><i class="bi bi-x-square-fill"></i></a>';
                echo '</td>';
                echo '</tr>';
              }
            }
            ?>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data ini?
      </div>
      <div class="modal-footer">
        <form id="formDelete" method="POST">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
  <div class="card shadow mt-3" style="width: 50%;">
    <div class="card-header font-weight-bold" style="text-align: center; background-color: #167395; color: white">Edit Data Alternatif</div>
    <?php
    $sql = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_alternatif='$_GET[id]' ");
    if ($data = mysqli_fetch_array($sql)) {
    ?>
      <div class="card-body">
        <form action="simpaneditalter.php" method="post" class="form-horizontal" enctype="multipart/form-data">
          <div class="form-group cols-sm-6">
            <label>ID Alternatif</label>
            <input type="text" name="id_alternatif" value="<?php echo $data['id_alternatif']; ?>" class="form-control" readonly>
          </div>

          <div class="form-group cols-sm-6">
            <label>Nama</label>
            <input type="text" name="nama" value="<?php echo $data['nama_alternatif']; ?>" class="form-control">
          </div>

          <div class="form-group cols-sm-6">
            <label>Status</label>
            <div>
              <label>
                <input type="radio" name="status_alternatif" value="1" <?php echo ($data['status_alternatif'] == 1) ? 'checked' : ''; ?>> Aktif
              </label>
              <label>
                <input type="radio" name="status_alternatif" value="0" <?php echo ($data['status_alternatif'] == 0) ? 'checked' : ''; ?>> Non Aktif
              </label>
            </div>
          </div>

          <div class="form-group cols-sm-6">
            <button type="submit" name="edit" class="btn btn-dark btn-sm">
              Update
            </button>
            <button type=" button" class="btn btn-dark btn-sm" onclick="history.back()">
              Reset
            </button>
          </div>

        </form>
      <?php } ?>
      </div>
  </div>
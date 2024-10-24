<?php
// Query untuk mengambil semua data dari tabel kriteria
$querykriteria = "SELECT * From Kriteria";
// Menjalankan query
$resultkriteria = mysqli_query($conn, $querykriteria);
$no = 1;
?>
<div class="card shadow mt-3">
    <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA BOBOT KRITERIA</div>
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr style="text-align: center;">
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Tipe</th>
                        <th>Pilih Kriteria</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($datakriteria = mysqli_fetch_array($resultkriteria)) { ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $datakriteria['nama_kriteria']; ?></td>
                            <td><?php echo is_null($datakriteria['tipe_kriteria']) ? "NULL" : $datakriteria['tipe_kriteria']; ?></td>
                            <td>
                                <input type="checkbox" class="checkItem" />
                            </td>
                        </tr>
                    <?php
                        $no++;
                    } ?>
                    <tr>
                        <td colspan="4" style="text-align: end;">
                            <button id="checkAllBtn"><i class="bi bi-check-square"></i></button>
                            <button id="uncheckAllBtn"><i class="bi bi-dash-square"></i></button>
                            <button><i class="bi bi-arrow-right-square-fill"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <script>
                // Ceklist semua
                document.getElementById('checkAllBtn').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItem');
                    checkboxes.forEach(checkbox => checkbox.checked = true);
                };

                // Unchecklist semua
                document.getElementById('uncheckAllBtn').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItem');
                    checkboxes.forEach(checkbox => checkbox.checked = false);
                };

                // Fitur untuk ceklist semua dengan checkbox di header
                document.getElementById('checkAll').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItem');
                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                };
            </script>
        </div>
    </div>
</div>
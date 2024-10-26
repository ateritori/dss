<?php
// Query untuk mengambil semua data dari tabel kriteria
$querykriteria = "SELECT * FROM Kriteria";
// Menjalankan query
$resultkriteria = mysqli_query($conn, $querykriteria);
$no = 1;
?>
<div class="card shadow mt-3">
    <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA BOBOT KRITERIA & SUB-KRITERIA</div>
    <div class="card-body">
        <h5>Silakan Memilih Kriteria yang Akan Digunakan</h5>
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr style="text-align: center;">
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Tipe</th>
                        <th>Memiliki Sub-Kriteria</th>
                        <th>Pilih Kriteria</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($datakriteria = mysqli_fetch_array($resultkriteria)) { ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $datakriteria['nama_kriteria']; ?></td>
                            <td><?php echo is_null($datakriteria['tipe_kriteria']) ? "NULL" : $datakriteria['tipe_kriteria']; ?></td>
                            <td><?php echo ($datakriteria['sub_kriteria'] == '1') ? "Ya" :  "Tidak"; ?></td>
                            <td>
                                <input type="checkbox" class="checkItemKriteria"
                                    value="<?php echo $datakriteria['nama_kriteria']; ?>"
                                    data-id="<?php echo $datakriteria['id_kriteria']; ?>"
                                    data-tipe="<?php echo is_null($datakriteria['tipe_kriteria']) ? 'NULL' : $datakriteria['tipe_kriteria']; ?>" />
                            </td>
                        </tr>
                    <?php $no++;
                    } ?>
                    <tr>
                        <td colspan="4"></td>
                        <td>
                            <button id="pilihsemua"><i class="bi bi-check-square"></i></button>
                            <button id="proses"><i class="bi bi-arrow-right-square-fill"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="pilihankriteria" class="d-none" style="margin-top: 20px;">
                <h5>Kriteria yang Dipilih:</h5>
                <ul id="resultListKriteria"></ul>
            </div>

            <div id="metodebobot" class="form-group row col-sm-10 d-none">
                <label class="col-form-label">Silakan Pilih Metode Pembobotan:</label>
                <div class="col-sm-6">
                    <select name="modelbobot" class="form-control mr-2">
                        <option value="">Silakan Pilih Metode Pembobotan</option>
                        <option value="1">Perbandingan Kriteria</option>
                        <option value="2">Prosentase Manual</option>
                    </select>
                </div>
            </div>
            <!-- Tambahkan elemen div untuk menampung ahpFormContainer di luar metodebobotDiv -->
            <div id="ahpContainer" class="mt-3"></div>
        </div>
    </div>
</div>
<script src="assets/js/bobot.js"></script>
<?php
//include "bobot_subkriteria.php";
?>
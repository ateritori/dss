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
        <div class="col-sm-12 mt-3">
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
                            <button id="pilihsemuakriteria"><i class="bi bi-check-square"></i></button>
                            <button id="lanjutpilihbobot"><i class="bi bi-arrow-right-square-fill"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="col-sm-12 d-none" id="pilihancontainer">
            <div class="row">
                <div class="col-sm-4">
                    <span>Kriteria Yang Dipilih:</span>
                    <ol id="pilihankriteria">

                    </ol>
                    <button class="btn-sm-secondary" id="resetpilihan">Reset Pilihan</button>
                </div>
                <div class="col-sm-4">
                    <label>Silakan Pilih Metode Pembobotan:</label>
                    <select name="metodebobot" id="metodebobot" class="form-control">
                        <option value="">--Silakan Pilih Metode Pembobotan--</option>
                        <option value="1">Bobot Semua Kriteria Sama</option>
                        <option value="2">Masukkan Bobot Dinamis</option>
                        <option value="3">Pembobotan Kriteria</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-sm-6 mt-3 d-none" id="hasilPembobotanContainer">
            <table class="table table-bordered" id="tabelPembobotan">
                <thead>
                    <tr style="text-align: center;">
                        <th>No</th>
                        <th>Pilihan Kriteria</th>
                        <th>Bobot (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi secara dinamis -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="assets/js/bobot_kriteria.js"></script>
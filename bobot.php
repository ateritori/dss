<?php
// Query untuk mengambil semua data dari tabel kriteria
$querykriteria = "SELECT * FROM Kriteria";
$resultkriteria = mysqli_query($conn, $querykriteria);
$no = 1;
?>

<div class="card shadow mt-3">
    <div class="card-header py-3 text-center" style="background-color: #167395; color: white; font-weight:bold">
        DATA BOBOT KRITERIA & SUB-KRITERIA
    </div>
    <div class="card-body">
        <span><strong>Memilih Kriteria & Sub-Kriteria yang Akan Digunakan</strong></span>
        <div class="col-sm-12 mt-3">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kriteria/Sub-Kriteria</th>
                        <th>Tipe</th>
                        <th>Memiliki Sub-Kriteria</th>
                        <th>Pilih Kriteria/Sub-Kriteria</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($datakriteria = mysqli_fetch_array($resultkriteria)) { ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $datakriteria['nama_kriteria']; ?></td>
                            <td><?php echo $datakriteria['tipe_kriteria'] ?: "NULL"; ?></td>
                            <td><?php echo $datakriteria['sub_kriteria'] == '1' ? "Ya" : "Tidak"; ?></td>
                            <td>
                                <input type="checkbox" class="checkItemKriteria"
                                    value="<?php echo $datakriteria['nama_kriteria']; ?>"
                                    data-id="<?php echo $datakriteria['id_kriteria']; ?>"
                                    data-tipe_kriteria="<?php echo $datakriteria['tipe_kriteria']; ?>" />
                            </td>
                        </tr>

                    <?php
                        // Query untuk mengambil semua data dari tabel Sub-Kriteria
                        $querysubkriteria = "SELECT * FROM subkriteria WHERE id_kriteria = {$datakriteria['id_kriteria']}";
                        $resultsubkriteria = mysqli_query($conn, $querysubkriteria);
                        $urut = 1;

                        // Loop Subkriteria
                        while ($datasubkriteria = mysqli_fetch_array($resultsubkriteria)) {
                            echo "
                            <tr>
                                <td style='text-align: right'>$no.$urut.</td>
                                <td>&nbsp;&nbsp;&nbsp;{$datasubkriteria['nama_subkriteria']}</td>
                                <td>{$datasubkriteria['tipe_subkriteria']}</td>
                                <td>Tidak</td>
                                <td>
                                    <input type='checkbox' class='checkItemSubkriteria' 
                                           value='{$datasubkriteria['nama_subkriteria']}' 
                                           data-id-kriteria='{$datakriteria['id_kriteria']}'
                                           data-tipe_subkriteria='{$datasubkriteria['tipe_subkriteria']}' 
                                           disabled />
                                </td>
                            </tr>";
                            $urut++;
                        }
                        $no++;
                    } ?>

                    <tr>
                        <td colspan="4"></td>
                        <td>
                            <button id="pilihsemuakriteria"><i class="bi bi-check-all"></i></button>
                            <button id="lanjutpilihbobot"><i class="bi bi-arrow-right-square-fill"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pilihan Kriteria dan Sub-Kriteria Terpilih -->
        <div class="col-sm-12 d-none" id="pilihancontainer">
            <div class="row">
                <div class="col-sm-4">
                    <span>Kriteria Yang Dipilih:</span>
                    <ol id="pilihankriteria"></ol>
                </div>
                <div class="col-sm-4">
                    <span>Sub-Kriteria Yang Dipilih:</span>
                    <ol id="pilihansubkriteria"></ol>
                </div>
                <div class="col-sm-4">
                    <label>Silakan Pilih Metode Pembobotan:</label>
                    <select name="metodebobot" id="metodebobot" class="form-control">
                        <option value="">--Silakan Pilih Metode Pembobotan--</option>
                        <option value="1">Bobot Semua Kriteria Sama</option>
                        <option value="2">Masukkan Bobot Dinamis</option>
                        <option value="3">Perbandingan Kriteria</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Kontainer untuk input bobot kriteria dan subkriteria -->
        <div id="bobotdinamis" class="d-none mt-3">
            <span><strong>Input Bobot Kriteria</strong></span>
            <div class="row row-cols-4 g-2" id="inputKriteriaContainer"></div>
            <br>
            <span><strong>Input Bobot Subkriteria</strong></span>
            <div class="row row-cols-4 g-2" id="inputSubKriteriaContainer"></div>
            <button class="btn-sm-secondary mt-3" id="btnbobotdinamis">Gunakan Bobot</button>
        </div>

        <!-- Hasil Pembobotan -->
        <div id="hasilPembobotanContainer" class="d-none">
            <div class="d-flex justify-content-between">
                <div class="col-5 mt-3">
                    <span><strong>Hasil Pembobotan Kriteria</strong></span>
                    <table id="tabelKriteria" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kriteria</th>
                                <th>Bobot (%)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="text-align: right;">Total Bobot Kriteria:</td>
                                <td class="font-weight-bold" id="totalBobotKriteria">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-7 mt-3">
                    <span><strong>Hasil Pembobotan Subkriteria</strong></span>
                    <table id="tabelSubkriteria" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kriteria-Subkriteria</th>
                                <th>Bobot</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="d-none">
                            <tr>
                                <td colspan="2" style="text-align: right;">Total Bobot Subkriteria:</td>
                                <td class="font-weight-bold" id="totalBobotSubkriteria">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="col-sm-12 mt-3">
                <span><strong>Hasil Gabungan Kriteria dan Subkriteria</strong></span>
                <table id="tabelGabungan" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Parameter</th>
                            <th>Tipe</th>
                            <th>Bobot (%)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;">Total Bobot:</td>
                            <td class="font-weight-bold" id="totalBobotGabungan">0</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="mt-3">
                    <button class="btn-sm-secondary" id="saveBobot">Simpan Bobot</button>
                    <button class="btn-sm-secondary" id="resetpilihan">Reset Bobot</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/inisialisasi.js"></script>
<script src="assets/js/pilihan.js"></script>
<script src="assets/js/tabel.js"></script>\
<script src="assets/js/update_tabel.js"></script>
<script src="assets/js/listener.js"></script>
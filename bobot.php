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
        <span><strong>Memilih Kriteria & Sub-Kriteria yang Akan Digunakan</strong></span>
        <div class="col-sm-12 mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr style="text-align: center;">
                        <th>No</th>
                        <th>Kriteria/Sub-Kriteria</th>
                        <th>Tipe</th>
                        <th>Memiliki Sub-Kriteria</th>
                        <th>Pilih Kriteria/ Sub-Kriteria</th>
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
                                    data-id="<?php echo $datakriteria['id_kriteria']; ?>" />
                            </td>
                        </tr>
                    <?php
                        // Query untuk mengambil semua data dari tabel Sub-Kriteria
                        $querysubkriteria = "SELECT * FROM subkriteria WHERE id_kriteria=$datakriteria[id_kriteria]";
                        // Menjalankan query
                        $resultsubkriteria = mysqli_query($conn, $querysubkriteria);
                        $urut = 1;
                        while ($datasubkriteria = mysqli_fetch_array($resultsubkriteria)) {
                            echo
                            "<tr>
                                <td style= 'text-align: right'>$no.$urut.</td>
                                <td>&nbsp;&nbsp;&nbsp;$datasubkriteria[nama_subkriteria]</td>
                                <td>$datasubkriteria[tipe_subkriteria]</td>
                                <td>Tidak</td>
                                <td><input type=checkbox class=checkItemSubkriteria
                                    value='$datasubkriteria[nama_subkriteria]';
                                    data-id-kriteria='$datakriteria[id_kriteria]';</td>
                            </tr>";
                            $urut++;
                        }
                        $no++;
                    }
                    ?>
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
                        <!-- Isian Pilihan Kriteria -->
                    </ol>
                    <button class="btn-sm-secondary" id="resetpilihan">Reset Pilihan</button>
                </div>

                <div class="col-sm-4">
                    <span>Sub-Kriteria Yang Dipilih:</span>
                    <ol id="pilihansubkriteria">
                        <!-- Isian Pilihan Sub-Kriteria -->
                    </ol>
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
            <span><strong>Input Bobot Kriteria </strong></span>
            <div class="row">
                <div id="inputKriteriaContainer" class="col-sm-12 d-flex"></div>
            </div>
            <br>
            <span><strong>Input Bobot Subkriteria</strong></span>
            <div class="row">
                <div id="inputSubKriteriaContainer" class="col-sm-12 d-flex"></div>
            </div>
            <button class="btn-sm-secondary mt-2" id="btnbobotdinamis">Gunakan Bobot</button>
        </div>

        <div id="hasilPembobotanContainer" class="d-none">
            <div class="d-flex justify-content-between">
                <!-- Tabel Kriteria -->
                <div class="col-6 mt-3">
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
                    </table>
                </div>

                <!-- Tabel Subkriteria -->
                <div class="col-6 mt-3">
                    <span><strong>Hasil Pembobotan Subkriteria</strong></span>
                    <table id="tabelSubkriteria" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Subkriteria</th>
                                <th>Bobot (%)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Gabungan Kriteria dan Subkriteria -->
            <div class="col-6 mt-3">
                <span><strong>Hasil Gabungan Kriteria dan Subkriteria</strong></span>
                <table id="tabelGabungan" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Parameter</th>
                            <th>Bobot (%)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script src="assets/js/bobot_kriteria.js"></script>
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
                        <td colspan="5" style="text-align: end;">
                            <button id="toggleCheckBtnKriteria"><i class="bi bi-check-square"></i> Pilih Semua</button>
                            <button id="getDataBtnKriteria"><i class="bi bi-arrow-right-square-fill"></i> Proses</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="checkedDataResultKriteria" style="margin-top: 20px;">
                <h5>Kriteria yang Dipilih:</h5>
                <ul id="resultListKriteria"></ul>
            </div>

            <div class="form-group row col-sm-10">
                <label class="col-form-label">Silakan Pilih Metode Pembobotan:</label>
                <div class="col-sm-6">
                    <select name="modelbobot" class="form-control mr-2" id="">
                        <option value="">Silakan Pilih Metode Pembobotan</option>
                        <option value="1">Pembobotan Kriteria</option>
                        <option value="2">Prosentase Manual</option>
                    </select>
                </div>
            </div>

            <div id="comparisonForm" style="display: none; margin-top: 20px;">
                <h5>Form Perbandingan Kriteria</h5>
                <form action="proses_ahp.php" method="POST" id="formAHPCriteria">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kriteria 1</th>
                                <th>Skala Perbandingan</th>
                                <th>Kriteria 2</th>
                            </tr>
                        </thead>
                        <tbody id="comparisonTableBody">
                            <!-- Baris perbandingan kriteria akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Simpan Perbandingan</button>
                </form>
            </div>


            <script>
                let isAllCheckedKriteria = false;

                function updateCheckAllButtonKriteria() {
                    const checkboxes = document.querySelectorAll('.checkItemKriteria');
                    isAllCheckedKriteria = Array.from(checkboxes).every(cb => cb.checked);

                    const toggleCheckBtn = document.getElementById('toggleCheckBtnKriteria');
                    toggleCheckBtn.innerHTML = isAllCheckedKriteria ?
                        '<i class="bi bi-dash-square"></i> Reset' :
                        '<i class="bi bi-check-square"></i> Pilih Semua';
                }

                document.getElementById('toggleCheckBtnKriteria').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItemKriteria');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !isAllCheckedKriteria;
                        checkbox.disabled = false;
                    });

                    isAllCheckedKriteria = !isAllCheckedKriteria;

                    if (!isAllCheckedKriteria) {
                        document.getElementById('resultListKriteria').innerHTML = '';
                    }

                    updateCheckAllButtonKriteria();
                };

                document.querySelectorAll('.checkItemKriteria').forEach(checkbox => {
                    checkbox.addEventListener('change', updateCheckAllButtonKriteria);
                });

                document.getElementById('getDataBtnKriteria').onclick = function() {
                    let resultList = document.getElementById('resultListKriteria');
                    resultList.innerHTML = '';

                    let checkboxes = document.querySelectorAll('.checkItemKriteria:checked');
                    if (checkboxes.length > 0) {
                        checkboxes.forEach(checkbox => {
                            let nama = checkbox.value;
                            let id = checkbox.getAttribute('data-id');
                            let tipe = checkbox.getAttribute('data-tipe');

                            let listItem = document.createElement('li');
                            listItem.textContent = `ID: ${id}, Nama: ${nama}, Tipe: ${tipe}`;
                            resultList.appendChild(listItem);

                            checkbox.disabled = true;
                        });
                    } else {
                        resultList.innerHTML = '<li>Tidak Ada Kriteria Yang Dipilih</li>';
                    }
                };

                // Event ketika dropdown model pembobotan berubah
                document.querySelector('select[name="modelbobot"]').onchange = function() {
                    const selectedModel = this.value;
                    const checkedItems = document.querySelectorAll('.checkItemKriteria:checked');
                    const comparisonForm = document.getElementById('comparisonForm');
                    const comparisonTableBody = document.getElementById('comparisonTableBody');

                    // Menampilkan form perbandingan hanya jika "Pembobotan Kriteria" dipilih dan ada lebih dari satu kriteria terpilih
                    if (selectedModel === '1' && checkedItems.length > 1) {
                        comparisonTableBody.innerHTML = ''; // Mengosongkan isi sebelumnya

                        // Membuat pasangan perbandingan dari setiap kriteria yang dipilih
                        for (let i = 0; i < checkedItems.length; i++) {
                            for (let j = i + 1; j < checkedItems.length; j++) {
                                const kriteria1 = checkedItems[i].value;
                                const kriteria2 = checkedItems[j].value;

                                // Membuat baris baru dengan skala perbandingan berbasis slider
                                const row = document.createElement('tr');
                                row.innerHTML = `
                    <td>${kriteria1}</td>
                    <td>
                        <input type="range" name="comparison[${kriteria1}][${kriteria2}]" 
                            min="1" max="9" value="1" class="form-range"
                            oninput="this.nextElementSibling.value = this.value">
                        <output>1</output>
                    </td>
                    <td>${kriteria2}</td>
                `;
                                comparisonTableBody.appendChild(row); // Tambahkan ke table body
                            }
                        }

                        // Tampilkan form perbandingan
                        comparisonForm.style.display = 'block';
                    } else {
                        comparisonForm.style.display = 'none';
                    }
                };
            </script>
        </div>
    </div>
</div>

<?php
//include "bobot_subkriteria.php";
?>
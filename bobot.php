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
                            <button id="toggleCheckBtnKriteria"><i class="bi bi-check-square"></i> Check All</button>
                            <button id="getDataBtnKriteria"><i class="bi bi-arrow-right-square-fill"></i> Get Checked Data</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="checkedDataResultKriteria" style="margin-top: 20px;">
                <h5>Checked Data:</h5>
                <ul id="resultListKriteria"></ul>
            </div>

            <script>
                let isAllCheckedKriteria = false;

                function updateCheckAllButtonKriteria() {
                    const checkboxes = document.querySelectorAll('.checkItemKriteria');
                    isAllCheckedKriteria = Array.from(checkboxes).every(cb => cb.checked);

                    const toggleCheckBtn = document.getElementById('toggleCheckBtnKriteria');
                    toggleCheckBtn.innerHTML = isAllCheckedKriteria ?
                        '<i class="bi bi-dash-square"></i> Uncheck All' :
                        '<i class="bi bi-check-square"></i> Check All';
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
                        resultList.innerHTML = '<li>No items checked.</li>';
                    }
                };
            </script>
        </div>
    </div>
</div>

<?php
include "bobot_subkriteria.php";
?>
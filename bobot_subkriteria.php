<?php
// Query untuk mengambil semua data dari tabel subkriteria
$querysubkriteria = "SELECT * FROM subkriteria";
// Menjalankan query
$resultsubkriteria = mysqli_query($conn, $querysubkriteria);
$no = 1;
?>
<div class="card shadow mt-3">
    <div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr style="text-align: center;">
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Sub-Kriteria</th>
                        <th>Tipe</th>
                        <th>Pilih Kriteria</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($datasubkriteria = mysqli_fetch_array($resultsubkriteria)) {
                        // Ambil nama kriteria berdasarkan id_kriteria
                        $id_kriteria = $datasubkriteria['id_kriteria'];
                        $querykriteria = "SELECT nama_kriteria FROM Kriteria WHERE id_kriteria = '$id_kriteria'";
                        $resultkriteria = mysqli_query($conn, $querykriteria);
                        $kriteria = mysqli_fetch_array($resultkriteria);
                    ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $kriteria['nama_kriteria']; ?></td>
                            <td><?php echo $datasubkriteria['nama_subkriteria']; ?></td>
                            <td><?php echo is_null($datasubkriteria['tipe_subkriteria']) ? "NULL" : $datasubkriteria['tipe_subkriteria']; ?></td>
                            <td>
                                <input type="checkbox" class="checkItemSub"
                                    value="<?php echo $datasubkriteria['nama_subkriteria']; ?>"
                                    data-id="<?php echo $datasubkriteria['id_kriteria']; ?>"
                                    data-tipe="<?php echo is_null($datasubkriteria['tipe_subkriteria']) ? 'NULL' : $datasubkriteria['tipe_subkriteria']; ?>" />
                            </td>
                        </tr>
                    <?php $no++;
                    } ?>
                    <tr>
                        <td colspan="5" style="text-align: end;">
                            <button id="toggleCheckBtnSub"><i class="bi bi-check-square"></i> Pilih Semua</button>
                            <button id="getDataBtnSub"><i class="bi bi-arrow-right-square-fill"></i> Proses</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="checkedDataResultSub" style="margin-top: 20px;">
                <h5>Kriteria Yang Dipilih:</h5>
                <ul id="resultListSub"></ul>
            </div>

            <script>
                let isAllCheckedSub = false;

                function updateCheckAllButtonSub() {
                    const checkboxes = document.querySelectorAll('.checkItemSub');
                    isAllCheckedSub = Array.from(checkboxes).every(cb => cb.checked);

                    const toggleCheckBtn = document.getElementById('toggleCheckBtnSub');
                    toggleCheckBtn.innerHTML = isAllCheckedSub ?
                        '<i class="bi bi-dash-square"></i> Reset' :
                        '<i class="bi bi-check-square"></i> Pilih Semua';
                }

                document.getElementById('toggleCheckBtnSub').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItemSub');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !isAllCheckedSub;
                        checkbox.disabled = false;
                    });

                    isAllCheckedSub = !isAllCheckedSub;

                    if (!isAllCheckedSub) {
                        document.getElementById('resultListSub').innerHTML = '';
                    }

                    updateCheckAllButtonSub();
                };

                document.querySelectorAll('.checkItemSub').forEach(checkbox => {
                    checkbox.addEventListener('change', updateCheckAllButtonSub);
                });

                document.getElementById('getDataBtnSub').onclick = function() {
                    let resultList = document.getElementById('resultListSub');
                    resultList.innerHTML = '';

                    let checkboxes = document.querySelectorAll('.checkItemSub:checked');
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
            </script>
        </div>
    </div>
</div>
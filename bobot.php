<?php
// Query untuk mengambil semua data dari tabel kriteria
$querykriteria = "SELECT * FROM Kriteria";
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
                            <td>
                                <?php
                                echo ($datakriteria['sub_kriteria'] == '1') ? "Ya" :  "Tidak";
                                ?>
                            </td>
                            <td>
                                <input type="checkbox" class="checkItem"
                                    value="<?php echo $datakriteria['nama_kriteria']; ?>"
                                    data-id="<?php echo $datakriteria['id_kriteria']; ?>"
                                    data-tipe="<?php echo is_null($datakriteria['tipe_kriteria']) ? 'NULL' : $datakriteria['tipe_kriteria']; ?>" />
                            </td>
                        </tr>
                    <?php
                        $no++;
                    } ?>
                    <tr>
                        <td colspan="5" style="text-align: end;">
                            <button id="toggleCheckBtn"><i class="bi bi-check-square"></i> Pilih Semua</button>
                            <button id="getDataBtn"><i class="bi bi-arrow-right-square-fill"></i> Proses</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Area untuk menampilkan hasil checkbox yang dicentang -->
            <div id="checkedDataResult" style="margin-top: 20px;">
                <h5>Kriteria Yang Dipilih:</h5>
                <ul id="resultList"></ul> <!-- List untuk menampilkan data yang dicentang -->
            </div>

            <script>
                let isAllChecked = false; // Untuk toggle status

                // Fungsi untuk memperbarui status tombol
                function updateCheckAllButton() {
                    const checkboxes = document.querySelectorAll('.checkItem');
                    isAllChecked = Array.from(checkboxes).every(cb => cb.checked); // Cek apakah semua checkbox tercentang

                    const toggleCheckBtn = document.getElementById('toggleCheckBtn');
                    toggleCheckBtn.innerHTML = isAllChecked ?
                        '<i class="bi bi-dash-square"></i> Reset' :
                        '<i class="bi bi-check-square"></i> Pilih Semua';
                }

                // Toggle check/uncheck semua checkbox
                document.getElementById('toggleCheckBtn').onclick = function() {
                    let checkboxes = document.querySelectorAll('.checkItem');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !isAllChecked; // Toggle checkbox
                        checkbox.disabled = false; // Pastikan checkbox tidak dinonaktifkan
                    });

                    isAllChecked = !isAllChecked; // Update status toggle

                    // Reset hasil jika uncheck
                    if (!isAllChecked) {
                        document.getElementById('resultList').innerHTML = ''; // Kosongkan list sebelumnya
                    }

                    // Perbarui status tombol
                    updateCheckAllButton();
                };

                // Menambahkan event listener ke setiap checkbox
                document.querySelectorAll('.checkItem').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        // Memperbarui status tombol
                        updateCheckAllButton();
                    });
                });

                // Ambil data checkbox yang dicentang dan disable checkbox setelah data diambil
                document.getElementById('getDataBtn').onclick = function() {
                    let resultList = document.getElementById('resultList');
                    resultList.innerHTML = ''; // Kosongkan list sebelumnya

                    let checkboxes = document.querySelectorAll('.checkItem:checked');
                    if (checkboxes.length > 0) {
                        checkboxes.forEach(checkbox => {
                            // Ambil nama, id, dan tipe dari atribut checkbox
                            let nama = checkbox.value;
                            let id = checkbox.getAttribute('data-id');
                            let tipe = checkbox.getAttribute('data-tipe');

                            // Buat list item untuk hasil
                            let listItem = document.createElement('li');
                            listItem.textContent = `ID: ${id}, Nama: ${nama}, Tipe: ${tipe}`;
                            resultList.appendChild(listItem);

                            // Disable checkbox setelah diambil datanya
                            checkbox.disabled = true; // Disable checkbox setelah diambil datanya
                        });
                    } else {
                        resultList.innerHTML = '<li>Tidak Ada Kriteria Yang Dipilih</li>';
                    }
                };
            </script>
        </div>
    </div>
</div>
<?php
// Ambil ID kriteria dari URL
$id_kriteria = $_GET['id'];

// Ambil data kriteria dari database
$sqlKriteria = mysqli_query($conn, "SELECT * FROM Kriteria WHERE id_kriteria = $id_kriteria");
$dataKriteria = mysqli_fetch_array($sqlKriteria);

// Ambil data sub-kriteria dari database
$sqlSubKriteria = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = $id_kriteria");
$subKriteria = [];
while ($row = mysqli_fetch_array($sqlSubKriteria)) {
    $subKriteria[] = $row;
}
// Ambil data rentang berdasarkan id_kriteria
$sqlRentang = mysqli_query($conn, "SELECT * FROM Rentang WHERE id_kriteria = $id_kriteria");
$rentangList = [];
$jenisPenilaian = null; // Variable untuk menyimpan jenis penilaian
while ($row = mysqli_fetch_array($sqlRentang)) {
    $rentangList[] = $row;
    $jenisPenilaian = $row['jenis_penilaian']; // Ambil jenis penilaian dari data rentang
}

// Set checked untuk radio button berdasarkan jenis penilaian
$checkedDinamis = ($jenisPenilaian == 1) ? 'checked' : '';
$checkedRentang = ($jenisPenilaian == 2) ? 'checked' : '';
?>

<div class="card shadow mt-3" style="width: 50%;">
    <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Edit Data Sub-Kriteria</div>
    <div class="card-body">
        <form action="updaterentang.php" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group cols-sm-6">
                <label>Nama Kriteria:</label>
                <input type="text" name="id_kriteria" value="<?php echo $dataKriteria['id_kriteria']; ?>" class="form-control" readonly>
                <input type="text" name="kriteria" class="form-control" value="<?php echo htmlspecialchars($dataKriteria['nama_kriteria']); ?>" readonly>
            </div>

            <div class="form-group cols-sm-6">
                <label>Model Penilaian</label>
                <div>
                    <label>
                        <input type="radio" name="model_penilaian" value="1" id="model_penilaian_1"
                            <?php echo $checkedDinamis; ?> required> Dinamis/ Input Manual
                    </label><br>
                    <label>
                        <input type="radio" name="model_penilaian" value="2" id="model_penilaian_2"
                            <?php echo $checkedRentang; ?> required> Rentang Nilai Tertentu
                    </label>
                </div>
            </div>


            <div id="rentang_penilaian_container" <?php echo ($checkedRentang == 'checked') ? '' : 'style="display:none;"'; ?>>
                <div class="form-group cols-sm-6">
                    <label>Isikan Rentang Nilai:</label>
                    <div id="rentang_field">
                        <?php if (!empty($rentangList)) : ?>
                            <?php foreach ($rentangList as $index => $rentang) : ?>
                                <div class="rentang_group" style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <input type="hidden" name="id_rentang[]" value="<?php echo $rentang['id_rentang']; ?>">
                                    <input type="text" name="uraian[]" class="form-control" placeholder="Uraian <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($rentang['uraian']); ?>" style="width: 80%; margin-right: 10px;" required>
                                    <input type="text" name="value_rentang[]" class="form-control" placeholder="Value <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($rentang['nilai_rentang']); ?>" style="flex: 1; margin-right: 10px;" required>
                                    <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;">
                                        <i class="bi bi-dash-circle"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div style="display: flex; margin-top: 5px;">
                        <button type="button" id="add_subkriteria" class="btn btn-secondary btn-sm">
                            <i class="bi bi-window-plus"></i> Tambah Rentang Nilai
                        </button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="hapus_subkriteria[]" id="hapus_subkriteria">

            <div class="form-group cols-sm-6" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn btn-dark" style="margin-right: 10px;">Update</button>
                <button type="button" class="btn btn-dark" onclick="history.back()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rentangPenilaianContainer = document.getElementById('rentang_penilaian_container');
        const modelPenilaianDinamis = document.getElementById('model_penilaian_1');
        const modelPenilaianRentang = document.getElementById('model_penilaian_2');
        const subKriteriaFields = document.getElementById('rentang_field');

        // Fungsi untuk menambah elemen baru subkriteria dengan ID unik
        function addNewSubkriteria() {
            const index = subKriteriaFields.children.length; // Menghitung jumlah sub-kriteria yang ada

            // Tambahkan elemen baru dengan ID unik untuk input uraian dan value
            const newSubkriteriaField = `
        <div class="rentang_group" style="display: flex; align-items: center; margin-bottom: 10px;">
            <input type="hidden" name="id_rentang[]" value="">
            <input type="text" id="uraian_${index}" name="uraian[]" class="form-control" placeholder="Uraian ${index + 1}" style="width: 80%; margin-right: 10px;" required>
            <input type="text" id="value_rentang_${index}" name="value_rentang[]" class="form-control" placeholder="Value ${index + 1}" style="flex: 1; margin-right: 10px;" required>
            <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;">
                <i class="bi bi-dash-circle"></i>
            </button>
        </div>
        `;

            subKriteriaFields.insertAdjacentHTML('beforeend', newSubkriteriaField);

            // Menambahkan event listener untuk tombol hapus pada sub-kriteria baru
            const removeButton = subKriteriaFields.lastElementChild.querySelector('.remove_field');
            removeButton.addEventListener('click', function() {
                removeButton.closest('.rentang_group').remove();
            });
        }

        // Tambahkan event listener ke tombol tambah subkriteria
        const addButton = document.getElementById('add_subkriteria');
        if (addButton) {
            addButton.addEventListener('click', addNewSubkriteria);
        }

        // Event listener untuk tombol hapus yang sudah ada
        document.querySelectorAll('.remove_field').forEach(button => {
            button.addEventListener('click', function() {
                const subkriteriaGroup = button.closest('.rentang_group');
                subkriteriaGroup.remove();
            });
        });

        // Tampilkan atau sembunyikan form rentang berdasarkan model penilaian yang dipilih
        function toggleRentangPenilaian() {
            if (modelPenilaianRentang.checked) {
                rentangPenilaianContainer.style.display = 'block';
            } else {
                rentangPenilaianContainer.style.display = 'none';
            }
        }

        modelPenilaianDinamis.addEventListener('change', toggleRentangPenilaian);
        modelPenilaianRentang.addEventListener('change', toggleRentangPenilaian);

        toggleRentangPenilaian(); // Panggil saat halaman dimuat
    });
</script>
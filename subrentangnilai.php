<?php
// Include koneksi database
include('config/koneksi.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil ID subkriteria dari URL
$id_subkriteria = $_GET['id'];

// Ambil data subkriteria dari database
$sqlsubKriteria = mysqli_query($conn, "SELECT * FROM subkriteria WHERE id_subkriteria = $id_subkriteria");
$datasubKriteria = mysqli_fetch_array($sqlsubKriteria);

// Ambil data jenis penilaian dari tabel Rentang berdasarkan id_subkriteria
$sqlRentang = mysqli_query($conn, "SELECT * FROM Rentang WHERE id_subkriteria = $id_subkriteria");
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
        <form action="updatesubrentang.php" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group cols-sm-6">
                <label>Nama Sub-Kriteria:</label>
                <input type="hidden" name="id_kriteria" value="<?php echo $datasubKriteria['id_kriteria']; ?>" class="form-control">
                <input type="text" name="id_subkriteria" value="<?php echo $datasubKriteria['id_subkriteria']; ?>" class="form-control" readonly>
                <input type="text" name="subkriteria" class="form-control" value="<?php echo htmlspecialchars($datasubKriteria['nama_subkriteria']); ?>" readonly>
            </div>
            <div class="form-group cols-sm-6">
                <label>Model Penilaian</label>
                <div>
                    <label>
                        <input type="radio" name="model_penilaian" value="1" id="model_penilaian_1"
                            <?php echo $checkedDinamis; ?>> Dinamis/ Input Manual
                    </label><br>
                    <label>
                        <input type="radio" name="model_penilaian" value="2" id="model_penilaian_2"
                            <?php echo $checkedRentang; ?>> Rentang Nilai Tertentu
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
                                    <input type="text" name="uraian[]" class="form-control" placeholder="Uraian <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($rentang['uraian']); ?>" style="width: 80%; margin-right: 10px;">
                                    <input type="text" name="value_rentang[]" class="form-control" placeholder="Value <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($rentang['nilai_rentang']); ?>" style="flex: 1; margin-right: 10px;">
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
            <input type="text" id="uraian_${index}" name="uraian[]" class="form-control" placeholder="Uraian ${index + 1}" style="width: 80%; margin-right: 10px;">
            <input type="text" id="value_rentang_${index}" name="value_rentang[]" class="form-control" placeholder="Value ${index + 1}" style="flex: 1; margin-right: 10px;">
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
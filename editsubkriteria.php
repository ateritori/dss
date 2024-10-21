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
?>

<div class="card shadow mt-3" style="width: 50%;">
    <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Edit Data Sub-Kriteria</div>
    <div class="card-body">
        <form action="updatesubkriteria.php" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group cols-sm-6">
                <label>Nama Kriteria:</label>
                <input type="text" name="id_kriteria" value="<?php echo $dataKriteria['id_kriteria']; ?>" class="form-control" readonly>
                <input type="text" name="kriteria" class="form-control" value="<?php echo htmlspecialchars($dataKriteria['nama_kriteria']); ?>" readonly>
                <span class="error"><?= isset($kriteriaErr) ? htmlspecialchars($kriteriaErr) : '' ?></span>
            </div>

            <div class="form-group cols-sm-6">
                <label>Status Sub-Kriteria</label>
                <div>
                    <label>
                        <input type="radio" name="status_sub_kriteria" value="0" id="status_sub_0"
                            <?php echo ($dataKriteria['sub_kriteria'] == '0') ? 'checked' : ''; ?> disabled> Tidak Memiliki Sub-Kriteria
                    </label><br>
                    <label>
                        <input type="radio" name="status_sub_kriteria" value="1" id="status_sub_1"
                            <?php echo ($dataKriteria['sub_kriteria'] == '1') ? 'checked' : ''; ?> disabled> Memiliki Sub-Kriteria
                    </label>
                </div>
            </div>

            <div id="sub_kriteria_kontainer">
                <div class="form-group cols-sm-6">
                    <label>Sub-Kriteria:</label>
                    <div id="subkriteria_fields">
                        <?php foreach ($subKriteria as $index => $subData) : ?>
                            <div class="subkriteria_group" style="display: flex; align-items: center; margin-bottom: 10px;">
                                <input type="hidden" name="id_subkriteria[]" value="<?php echo $subData['id_subkriteria']; ?>">
                                <input type="text" name="subkriteria[]" class="form-control" placeholder="Sub-Kriteria <?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($subData['nama_subkriteria']); ?>" style="flex: 1; margin-right: 10px;">
                                <div style="margin-left: 10px;">
                                    <label style="margin-right: 10px;">
                                        <input type="radio" name="jenis_subkriteria[<?php echo $index; ?>]" value="cost" <?php echo ($subData['tipe_subkriteria'] == 'cost') ? 'checked' : ''; ?>> Cost
                                    </label>
                                    <label>
                                        <input type="radio" name="jenis_subkriteria[<?php echo $index; ?>]" value="benefit" <?php echo ($subData['tipe_subkriteria'] == 'benefit') ? 'checked' : ''; ?>> Benefit
                                    </label>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="display: flex; margin-top: 5px;">
                        <button type="button" id="add_subkriteria" class="btn btn-secondary btn-sm">
                            <i class="bi bi-window-plus"></i> Tambah Sub-Kriteria
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
        // Fungsi untuk memperbarui indeks subkriteria dan placeholder
        function updateSubkriteriaIndices() {
            document.querySelectorAll('.subkriteria_group').forEach((element, index) => {
                // Perbarui placeholder dan name attribute dengan indeks yang benar
                element.querySelector('input[name="subkriteria[]"]').placeholder = `Sub-Kriteria ${index + 1}`;
                element.querySelectorAll('input[type="radio"]').forEach(radio => {
                    const name = `jenis_subkriteria[${index}]`;
                    radio.setAttribute('name', name);
                });
            });
        }

        // Fungsi untuk menambah elemen baru subkriteria
        function addNewSubkriteria() {
            const subKriteriaFields = document.getElementById('subkriteria_fields');
            const index = subKriteriaFields.children.length; // Menghitung jumlah sub-kriteria yang ada

            // Tambahkan elemen baru untuk input subkriteria
            const newSubkriteriaField = `
                <div class="subkriteria_group" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <input type="hidden" name="id_subkriteria[]" value="">
                    <input type="text" name="subkriteria[]" class="form-control" placeholder="Sub-Kriteria ${index + 1}" style="flex: 1; margin-right: 10px;">
                    <div style="margin-left: 10px;">
                        <label style="margin-right: 10px;">
                            <input type="radio" name="jenis_subkriteria[${index}]" value="cost"> Cost
                        </label>
                        <label>
                            <input type="radio" name="jenis_subkriteria[${index}]" value="benefit"> Benefit
                        </label>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            `;

            subKriteriaFields.insertAdjacentHTML('beforeend', newSubkriteriaField);

            // Perbarui indeks setelah menambah
            updateSubkriteriaIndices();

            // Menambahkan event listener untuk tombol hapus pada sub-kriteria baru
            const removeButton = subKriteriaFields.lastElementChild.querySelector('.remove_field');
            removeButton.addEventListener('click', function() {
                subKriteriaFields.removeChild(subKriteriaFields.lastElementChild);
                updateSubkriteriaIndices();
            });
        }

        // Tambahkan event listener ke tombol tambah subkriteria
        const addButton = document.getElementById('add_subkriteria');
        if (addButton) {
            addButton.addEventListener('click', addNewSubkriteria);
        }

        // Inisialisasi event listener untuk tombol hapus yang sudah ada
        document.querySelectorAll('.remove_field').forEach(button => {
            button.addEventListener('click', function() {
                const subKriteriaFields = document.getElementById('subkriteria_fields');
                const subkriteriaGroup = button.closest('.subkriteria_group');
                subKriteriaFields.removeChild(subkriteriaGroup);
                updateSubkriteriaIndices();
            });
        });
    });
</script>
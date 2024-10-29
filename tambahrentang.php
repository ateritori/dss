<?php
// Koneksi ke database
require 'config/koneksi.php';

// Ambil data kriteria dari database
$sqlkriteria = mysqli_query($conn, "SELECT * FROM kriteria");

// Ambil id_kriteria yang sudah tersimpan di tabel rentang
$sqlRentang = mysqli_query($conn, "SELECT DISTINCT id_kriteria FROM rentang");
$kriteriaTersimpan = [];
while ($row = mysqli_fetch_assoc($sqlRentang)) {
    $kriteriaTersimpan[] = $row['id_kriteria'];
}

// Buat opsi untuk dropdown kriteria
$options = "<option value=''>Pilih Kriteria</option>";
while ($datakriteria = mysqli_fetch_array($sqlkriteria)) {
    // Ambil jumlah sub-kriteria berdasarkan id_kriteria
    $id_kriteria = $datakriteria['id_kriteria'];
    $sqlSubkriteria = mysqli_query($conn, "SELECT COUNT(*) as total FROM subkriteria WHERE id_kriteria = '$id_kriteria'");
    $subkriteriaData = mysqli_fetch_assoc($sqlSubkriteria);
    $totalSubkriteria = $subkriteriaData['total'];

    // Ambil jumlah sub-kriteria yang sudah tersimpan di tabel rentang untuk id_kriteria ini
    $sqlSubkriteriaTersimpan = mysqli_query($conn, "SELECT COUNT(*) as total FROM rentang WHERE id_kriteria = '$id_kriteria'");
    $subkriteriaTersimpanData = mysqli_fetch_assoc($sqlSubkriteriaTersimpan);
    $totalSubkriteriaTersimpan = $subkriteriaTersimpanData['total'];

    // Cek apakah total subkriteria tersimpan lebih kecil dari total subkriteria
    $isDisabled = ($totalSubkriteriaTersimpan >= $totalSubkriteria) ? 'disabled' : '';
    $keterangan = ($isDisabled) ? " (Rentang Sudah Diisi)" : "";
    $options .= "<option value='" . $datakriteria['id_kriteria'] . "' $isDisabled>" . $datakriteria['nama_kriteria'] . $keterangan . "</option>";
}
?>
<div class="card shadow mt-3" style="width: 50%;">
    <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Rentang Nilai</div>
    <div class="card-body">
        <form action="simpanrentang.php" method="post" class="form-horizontal" enctype="multipart/form-data">

            <!-- Bagian Dropdown Kriteria -->
            <div class="form-group cols-sm-6" id="kriteria_container">
                <label><strong>Kriteria:</strong></label>
                <span style="color: red; font-size: small;">Jika rentang nilai kriteria sudah terisi, silakan manfaatkan menu edit.</span>
                <select name="id_kriteria" class="form-control" id="kriteria-dropdown" required>
                    <?php echo $options; // Menampilkan opsi dropdown 
                    ?>
                </select>
            </div>

            <!-- Bagian Dropdown Sub-Kriteria -->
            <div class="form-group cols-sm-6" id="subkriteria_container" style="display: none;">
                <label><strong>Sub-Kriteria:</strong></label>
                <select name="id_subkriteria" class="form-control" id="subkriteria-dropdown" required>
                    <option value="">Pilih Sub-Kriteria</option>
                </select>
            </div>

            <div class="form-group cols-sm-6" id="jenis_penilaian_container" style="display: none;">
                <label>Model Penilaian</label>
                <div>
                    <label>
                        <input type="radio" name="model_penilaian" value="1" id="model_penilaian_1" required> Dinamis/ Input Manual
                    </label><br>
                    <label>
                        <input type="radio" name="model_penilaian" value="2" id="model_penilaian_2" required> Rentang Nilai Tertentu
                    </label>
                </div>
            </div>

            <div class="form-group cols-sm-6" id="rentang_penilaian_container" style="display: none;">
                <label>Isikan Rentang Nilai:</label>
                <div id="rentang_field">
                    <!-- Rentang nilai akan ditambahkan di sini -->
                </div>
                <div style="display: flex; margin-top: 5px;">
                    <button type="button" id="add_subkriteria" class="btn btn-secondary btn-sm">
                        <i class="bi bi-window-plus"></i> Tambah Rentang Nilai
                    </button>
                </div>
            </div>

            <div class="form-group cols-sm-6" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn btn-dark" style="margin-right: 10px;">Update</button>
                <button type="button" class="btn btn-dark" onclick="history.back()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const kriteriaDropdown = document.getElementById('kriteria-dropdown');
        const subkriteriaContainer = document.getElementById('subkriteria_container');
        const subkriteriaDropdown = document.getElementById('subkriteria-dropdown');
        const jenisPenilaianContainer = document.getElementById('jenis_penilaian_container');
        const rentangPenilaianContainer = document.getElementById('rentang_penilaian_container');
        const rentangField = document.getElementById('rentang_field');
        const modelPenilaianRadios = document.querySelectorAll('input[name="model_penilaian"]');
        const addSubkriteriaButton = document.getElementById('add_subkriteria');
        let index = 1; // Memulai dengan index 1 untuk input baru

        // Saat dropdown kriteria berubah
        kriteriaDropdown.addEventListener('change', function() {
            const id_kriteria = this.value; // Ambil nilai id_kriteria yang dipilih

            if (id_kriteria !== '') {
                fetch('ambil_subkriteria.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_kriteria=' + encodeURIComponent(id_kriteria)
                    })
                    .then(response => response.text())
                    .then(responseText => {
                        subkriteriaContainer.style.display = 'block';
                        subkriteriaDropdown.innerHTML = '<option value="">Pilih Sub-Kriteria</option>'; // Reset sub-kriteria

                        if (responseText === "no_subkriteria") {
                            subkriteriaDropdown.innerHTML += '<option value="" selected>Tidak Ada Sub-Kriteria</option>'; // Opsi terseleksi
                            jenisPenilaianContainer.style.display = 'block'; // Tampilkan kontainer jenis penilaian
                        } else {
                            subkriteriaDropdown.innerHTML += responseText; // Masukkan opsi subkriteria yang diambil dari response
                            jenisPenilaianContainer.style.display = 'none'; // Sembunyikan kontainer jenis penilaian jika ada sub-kriteria
                        }
                    })
                    .catch(error => {
                        console.error("Error: " + error);
                    });
            } else {
                subkriteriaContainer.style.display = 'none';
                jenisPenilaianContainer.style.display = 'none';
                rentangPenilaianContainer.style.display = 'none'; // Sembunyikan rentang penilaian juga
            }
        });

        // Saat dropdown sub-kriteria berubah
        subkriteriaDropdown.addEventListener('change', function() {
            const id_subkriteria = this.value; // Ambil nilai id_subkriteria yang dipilih

            if (id_subkriteria !== '') {
                jenisPenilaianContainer.style.display = 'block'; // Tampilkan kontainer jenis penilaian
            } else {
                jenisPenilaianContainer.style.display = 'none';
                rentangPenilaianContainer.style.display = 'none'; // Sembunyikan rentang penilaian
            }
        });

        // Saat radio button jenis penilaian berubah
        modelPenilaianRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === "2") { // Rentang Nilai Tertentu
                    rentangPenilaianContainer.style.display = 'block'; // Tampilkan kontainer rentang penilaian
                } else { // Dinamis/ Input Manual
                    rentangPenilaianContainer.style.display = 'none'; // Sembunyikan kontainer rentang penilaian
                }
            });
        });

        // Menambahkan rentang nilai baru
        addSubkriteriaButton.addEventListener('click', function() {
            const rentangRow = document.createElement('div'); // Buat elemen div baru untuk rentang
            rentangRow.style.display = 'flex'; // Atur gaya display flex
            rentangRow.style.alignItems = 'center'; // Posisikan input agar sejajar

            // Input untuk Uraian
            const inputUraian = document.createElement('input');
            inputUraian.type = 'text';
            inputUraian.name = 'uraian_' + index; // Beri nama untuk pengolahan data
            inputUraian.placeholder = 'Uraian ' + index; // Berikan placeholder dengan index
            inputUraian.className = 'form-control';
            inputUraian.style.width = '70%'; // Set lebar input uraian
            inputUraian.style.marginRight = '5px'; // Margin kanan untuk jarak

            // Input untuk Nilai
            const inputNilai = document.createElement('input');
            inputNilai.type = 'text';
            inputNilai.name = 'nilai_' + index; // Beri nama untuk pengolahan data
            inputNilai.placeholder = 'Nilai ' + index; // Berikan placeholder dengan index
            inputNilai.className = 'form-control';
            inputNilai.style.width = '20%'; // Set lebar input nilai

            // Tombol Hapus
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger btn-sm';
            removeButton.textContent = 'Hapus';
            removeButton.style.marginLeft = '5px'; // Margin kiri untuk jarak

            // Tambahkan event listener untuk tombol hapus
            removeButton.addEventListener('click', function() {
                rentangField.removeChild(rentangRow); // Hapus rentangRow dari rentangField
            });

            // Tambahkan input ke dalam rentangRow
            rentangRow.appendChild(inputUraian);
            rentangRow.appendChild(inputNilai);
            rentangRow.appendChild(removeButton); // Tambahkan tombol hapus ke rentangRow

            // Tambahkan rentangRow ke rentangField
            rentangField.appendChild(rentangRow);
            index++; // Increment index untuk input selanjutnya
        });
    });
</script>
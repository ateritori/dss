<div class="card shadow mt-3" style="width: 50%;">
    <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Data Kriteria</div>
    <div class="card-body">
        <form action="simpankriteriadansub.php" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="form-group cols-sm-6">
                <label>Nama Kriteria:</label>
                <input type="text" name="kriteria" class="form-control" required autofocus>
                <span class="error"><?= isset($kriteriaErr) ? htmlspecialchars($kriteriaErr) : '' ?></span>
            </div>

            <div class="form-group cols-sm-6">
                <label>Status Sub-Kriteria</label>
                <div>
                    <label><input type="radio" name="status_sub_kriteria" value="0" id="status_sub_0" onclick="updateSubKriteriaDisplay()"> Tidak Memiliki Sub-Kriteria</label><br>
                    <label><input type="radio" name="status_sub_kriteria" value="1" id="status_sub_1" onclick="updateSubKriteriaDisplay()"> Memiliki Sub-Kriteria</label>
                </div>
            </div>

            <div class="form-group cols-sm-6" id="tipe_kriteria_container" style="display: none;">
                <label>Tipe Kriteria</label>
                <select class="form-control" name="atribut" id="atribut">
                    <option value="" hidden>Pilih Tipe Kriteria</option>
                    <option value="cost">Cost</option>
                    <option value="benefit">Benefit</option>
                </select>
                <span class="error"><?= isset($atributErr) ? htmlspecialchars($atributErr) : '' ?></span>
            </div>

            <div id="sub_kriteria_kontainer" style="display: none;">
                <div class="form-group cols-sm-6">
                    <label>Sub-Kriteria:</label>
                    <div id="subkriteria_fields">
                        <div class="subkriteria_group" style="display: flex; align-items: center; margin-bottom: 10px;">
                            <input type="text" name="subkriteria[]" class="form-control" placeholder="Sub-Kriteria 1" style="flex: 1; margin-right: 10px;">
                            <div style="margin-left: 10px;">
                                <label style="margin-right: 10px;"><input type="radio" name="jenis_subkriteria[0]" value="cost"> Cost</label>
                                <label><input type="radio" name="jenis_subkriteria[0]" value="benefit"> Benefit</label>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;"><i class="bi bi-dash-circle"></i></button>
                        </div>
                    </div>
                    <div style="display: flex; margin-top: 5px;">
                        <button type="button" id="add_subkriteria" class="btn btn-secondary btn-sm"><i class="bi bi-window-plus"></i> Tambah Sub-Kriteria</button>
                    </div>
                </div>
            </div>

            <div class="form-group cols-sm-6" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn btn-dark" style="margin-right: 10px;">Simpan</button>
                <button type="button" class="btn btn-dark" onclick="history.back()">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsi untuk memperbarui tampilan berdasarkan pilihan status sub-kriteria
    function updateSubKriteriaDisplay() {
        var statusSub = document.querySelector('input[name="status_sub_kriteria"]:checked');
        var tipeKriteriaContainer = document.getElementById('tipe_kriteria_container');
        var subKriteriaKontainer = document.getElementById('sub_kriteria_kontainer');

        if (statusSub) {
            if (statusSub.value === '0') {
                tipeKriteriaContainer.style.display = 'block'; // Tampilkan tipe kriteria
                subKriteriaKontainer.style.display = 'none'; // Sembunyikan sub-kriteria
            } else if (statusSub.value === '1') {
                tipeKriteriaContainer.style.display = 'none'; // Sembunyikan tipe kriteria
                subKriteriaKontainer.style.display = 'block'; // Tampilkan sub-kriteria
            }
        }
    }

    // Atur tampilan awal saat halaman dimuat
    window.onload = function() {
        // Tidak memilih radio button dan menyembunyikan div tipe kriteria secara default
        document.querySelectorAll('input[name="status_sub_kriteria"]').forEach(input => input.checked = false);
        updateSubKriteriaDisplay(); // Perbarui tampilan berdasarkan pilihan awal
    };

    // Menambah input sub-kriteria
    document.getElementById('add_subkriteria').addEventListener('click', function() {
        var subKriteriaFields = document.getElementById('subkriteria_fields');
        var index = subKriteriaFields.children.length; // Menghitung jumlah sub-kriteria yang ada

        var newSubkriteriaGroup = document.createElement('div');
        newSubkriteriaGroup.classList.add('subkriteria_group');
        newSubkriteriaGroup.style.display = 'flex';
        newSubkriteriaGroup.style.alignItems = 'center';
        newSubkriteriaGroup.style.marginBottom = '10px';

        newSubkriteriaGroup.innerHTML = `
                <input type="text" name="subkriteria[]" class="form-control" placeholder="Sub-Kriteria ${index + 1}" style="flex: 1; margin-right: 10px;">
                <div style="margin-left: 10px;">
                    <label style="margin-right: 10px;"><input type="radio" name="jenis_subkriteria[${index}]" value="cost"> Cost</label>
                    <label><input type="radio" name="jenis_subkriteria[${index}]" value="benefit"> Benefit</label>
                </div>
                <button type="button" class="btn btn-secondary btn-sm remove_field" style="margin-left: 10px;"><i class="bi bi-dash-circle"></i></button>
            `;

        subKriteriaFields.appendChild(newSubkriteriaGroup);

        // Event listener untuk menghapus sub-kriteria
        newSubkriteriaGroup.querySelector('.remove_field').addEventListener('click', function() {
            subKriteriaFields.removeChild(newSubkriteriaGroup);
        });
    });

    // Validasi sebelum form dikirim
    document.querySelector('form').addEventListener('submit', function(event) {
        var statusSub = document.querySelector('input[name="status_sub_kriteria"]:checked');

        // Pastikan status_sub dipilih
        if (!statusSub) {
            event.preventDefault(); // Mencegah pengiriman form
            alert('Silakan pilih status sub-kriteria.');
            return;
        }

        // Jika status_sub adalah '1' (memiliki sub-kriteria), maka lakukan validasi sub-kriteria
        if (statusSub.value === '1') {
            var subkriteriaInputs = document.querySelectorAll('input[name="subkriteria[]"]');
            var isSubkriteriaValid = Array.from(subkriteriaInputs).some(input => input.value.trim() !== '');

            if (!isSubkriteriaValid) {
                event.preventDefault(); // Mencegah pengiriman form jika tidak ada sub-kriteria yang diisi
                alert('Silakan masukkan setidaknya satu sub-kriteria.');
                return;
            }
        }
    });
</script>
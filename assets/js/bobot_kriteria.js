document.addEventListener('DOMContentLoaded', function () {
    // Mendefinisikan elemen-elemen yang akan digunakan
    const pilihSemuaKriteriaBtn = document.getElementById('pilihsemuakriteria');
    const checkboxesKriteria = Array.from(document.querySelectorAll('.checkItemKriteria'));
    const checkboxesSubkriteria = Array.from(document.querySelectorAll('.checkItemSubkriteria'));
    const resetPilihanBtn = document.getElementById('resetpilihan');
    const pilihanKriteriaList = document.getElementById('pilihankriteria');
    const pilihanSubkriteriaList = document.getElementById('pilihansubkriteria');
    const lanjutPilihBobotBtn = document.getElementById('lanjutpilihbobot');
    const pilihanContainer = document.getElementById('pilihancontainer');
    const metodeBobotSelect = document.getElementById('metodebobot');
    const hasilPembobotanContainer = document.getElementById('hasilPembobotanContainer');
    const tabelPembobotanBody = document.querySelector('#tabelPembobotan tbody');
    const bobotDinamisContainer = document.getElementById('bobotdinamis');
    const inputContainer = document.getElementById('inputContainer');
    const btnBobotDinamis = document.getElementById('btnbobotdinamis');

    // Fungsi untuk memperbarui status checkbox subkriteria berdasarkan status kriteria induknya
    const updateCheckboxState = () => {
        checkboxesKriteria.forEach(kriteriaCheckbox => {
            const kriteriaId = kriteriaCheckbox.getAttribute('data-id');
            checkboxesSubkriteria
                .filter(subCheckbox => subCheckbox.dataset.idKriteria === kriteriaId)
                .forEach(subCheckbox => {
                    subCheckbox.disabled = !kriteriaCheckbox.checked;
                    if (!kriteriaCheckbox.checked) subCheckbox.checked = false;
                });
        });
    };

    // Fungsi umum untuk menampilkan pilihan kriteria/subkriteria yang dicentang
    const showPilihan = (checkboxes, container) => {
        container.innerHTML = '';
        checkboxes
            .filter(checkbox => checkbox.checked)
            .forEach(checkbox => {
                const listItem = document.createElement('li');
                listItem.textContent = checkbox.value;
                container.appendChild(listItem);
            });
    };

    // Fungsi untuk memperbarui ikon tombol pilih semua kriteria
    const updatePilihSemuaKriteriaIcon = () => {
        const allChecked = checkboxesKriteria.every(checkbox => checkbox.checked);
        pilihSemuaKriteriaBtn.innerHTML = allChecked 
            ? '<i class="bi bi-dash-square"></i>' 
            : '<i class="bi bi-check-square"></i>';
    };

    // Fungsi untuk menandai atau mereset semua checkbox
    pilihSemuaKriteriaBtn.addEventListener('click', () => {
        const allChecked = checkboxesKriteria.every(checkbox => checkbox.checked);
        checkboxesKriteria.forEach(checkbox => checkbox.checked = !allChecked);

        checkboxesKriteria.forEach(checkbox => {
            const kriteriaId = checkbox.getAttribute('data-id');
            checkboxesSubkriteria
                .filter(subCheckbox => subCheckbox.dataset.idKriteria === kriteriaId)
                .forEach(subCheckbox => {
                    subCheckbox.disabled = !checkbox.checked;
                    subCheckbox.checked = checkbox.checked;
                });
        });
        updatePilihSemuaKriteriaIcon();
    });

    // Fungsi untuk mereset pilihan checkbox dan dropdown metode bobot
    const resetSelections = () => {
        [...checkboxesKriteria, ...checkboxesSubkriteria].forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = checkbox.classList.contains('checkItemSubkriteria');
        });
        pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-check-square"></i>';
        pilihSemuaKriteriaBtn.disabled = false; // Mengaktifkan kembali tombol pilih semua kriteria saat reset
        pilihanKriteriaList.innerHTML = '';
        pilihanSubkriteriaList.innerHTML = '';
        metodeBobotSelect.value = '';
        pilihanContainer.classList.add('d-none');
        hasilPembobotanContainer.classList.add('d-none');
        tabelPembobotanBody.innerHTML = '';
        bobotDinamisContainer.classList.add('d-none');
        inputContainer.innerHTML = '';
    };
    resetPilihanBtn.addEventListener('click', resetSelections);

    // Tambahkan listener untuk setiap checkbox kriteria
    checkboxesKriteria.forEach(checkbox => checkbox.addEventListener('change', () => {
        updatePilihSemuaKriteriaIcon();
        updateCheckboxState();
    }));
    checkboxesSubkriteria.forEach(checkbox => checkbox.addEventListener('change', updatePilihSemuaKriteriaIcon));

    // Event listener untuk tombol lanjut pilih bobot
    lanjutPilihBobotBtn.addEventListener('click', () => {
        showPilihan(checkboxesKriteria, pilihanKriteriaList);
        showPilihan(checkboxesSubkriteria, pilihanSubkriteriaList);
        [...checkboxesKriteria, ...checkboxesSubkriteria].forEach(checkbox => checkbox.disabled = true);
        pilihSemuaKriteriaBtn.disabled = true;
        pilihanContainer.classList.remove('d-none');
        alert("Pilihan kriteria dan subkriteria telah disimpan.");
    });

    // Inisialisasi awal
    checkboxesSubkriteria.forEach(checkbox => checkbox.disabled = true);
    updateCheckboxState();

    // Event listener untuk dropdown metode bobot
    metodeBobotSelect.addEventListener('change', () => {
        const selectedValue = metodeBobotSelect.value;
        const selectedKriteria = checkboxesKriteria.filter(checkbox => checkbox.checked);
        const selectedSubkriteria = checkboxesSubkriteria.filter(checkbox => checkbox.checked);
    
        if (selectedValue === '1') {  // Semua bobot sama
            const bobotKriteria = (100 / selectedKriteria.length).toFixed(2);
            const bobotSubkriteria = (100 / selectedSubkriteria.length).toFixed(2);
            
            const tabelKriteriaBody = document.querySelector('#tabelKriteria tbody');
            const tabelSubkriteriaBody = document.querySelector('#tabelSubkriteria tbody');
            tabelKriteriaBody.innerHTML = '';
            tabelSubkriteriaBody.innerHTML = '';
    
            // Isi tabel kriteria
            selectedKriteria.forEach((checkbox, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${checkbox.value}</td>
                    <td align="center">${bobotKriteria}%</td>
                `;
                tabelKriteriaBody.appendChild(row);
            });
    
            // Isi tabel subkriteria
            selectedSubkriteria.forEach((checkbox, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${checkbox.value}</td>
                    <td align="center">${bobotSubkriteria}%</td>
                `;
                tabelSubkriteriaBody.appendChild(row);
            });
    
            hasilPembobotanContainer.classList.remove('d-none');
            bobotDinamisContainer.classList.add('d-none');
            inputContainer.innerHTML = '';
    
        } else if (selectedValue === '2') { // Dinamis
            inputContainer.innerHTML = '';
            [...selectedKriteria, ...selectedSubkriteria].forEach(checkbox => {
                const div = document.createElement('div');
                div.className = 'col mb-3';
                div.innerHTML = `
                    <label class="form-label">${checkbox.value}</label>
                    <input type="number" class="form-control w-100" name="bobotkriteria" min="10" max="100" placeholder="Range 10% - 100%">
                `;
                inputContainer.appendChild(div);
            });
            bobotDinamisContainer.classList.remove('d-none');
            hasilPembobotanContainer.classList.add('d-none');
        } else {
            hasilPembobotanContainer.classList.add('d-none');
            bobotDinamisContainer.classList.add('d-none');
            inputContainer.innerHTML = '';
        }
    });

    // Event listener untuk tombol simpan bobot dinamis
    btnBobotDinamis.addEventListener('click', () => {
        const bobotInputs = document.querySelectorAll('input[name="bobotkriteria"]');
        const inputValues = [...bobotInputs].map(input => ({
            kriteria: input.previousElementSibling.textContent,
            bobot: parseFloat(input.value) || 0
        }));
        const totalBobot = inputValues.reduce((acc, data) => acc + data.bobot, 0);

        if (totalBobot !== 100) {
            alert("Jumlah bobot kriteria harus 100%");
            return;
        }

        tabelPembobotanBody.innerHTML = '';
        inputValues.forEach((data, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${data.kriteria}</td>
                <td align="center">${data.bobot}%</td>
            `;
            tabelPembobotanBody.appendChild(row);
        });

        hasilPembobotanContainer.classList.remove('d-none');
        bobotDinamisContainer.classList.add('d-none');
    });

    // Fungsi untuk mengisi tabel gabungan dengan hasil kombinasi bobot
    const fillGabunganTable = () => {
    const tabelGabunganBody = document.querySelector('#tabelGabungan tbody');
    tabelGabunganBody.innerHTML = '';

    let rowIndex = 1;

    checkboxesKriteria.forEach(kriteriaCheckbox => {
        const kriteriaId = kriteriaCheckbox.getAttribute('data-id');
        const kriteriaBobot = parseFloat(kriteriaCheckbox.dataset.bobot); // Ambil bobot kriteria dari atribut data

        // Cek apakah kriteria memiliki subkriteria yang dicentang
        const subkriteriaChecked = checkboxesSubkriteria.filter(subCheckbox => 
            subCheckbox.dataset.idKriteria === kriteriaId && subCheckbox.checked
        );

        // Jika memiliki subkriteria yang dicentang, tampilkan subkriteria dengan bobot gabungan
        if (subkriteriaChecked.length > 0) {
            subkriteriaChecked.forEach(subCheckbox => {
                const subkriteriaBobot = parseFloat(subCheckbox.dataset.bobot); // Ambil bobot subkriteria dari atribut data
                const bobotGabungan = (kriteriaBobot * subkriteriaBobot) / 100; // Hitung bobot gabungan

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${rowIndex++}</td>
                    <td>${subCheckbox.value}</td>
                    <td align="center">${bobotGabungan.toFixed(2)}%</td>
                `;
                tabelGabunganBody.appendChild(row);
            });
        } else if (kriteriaCheckbox.checked) {
            // Jika tidak memiliki subkriteria, tampilkan kriteria langsung dengan bobot kriteria saja
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${rowIndex++}</td>
                <td>${kriteriaCheckbox.value}</td>
                <td align="center">${kriteriaBobot.toFixed(2)}%</td>
            `;
            tabelGabunganBody.appendChild(row);
        }
    });
};

    // Panggil fungsi fillGabunganTable() setelah pembobotan selesai, misalnya di akhir event metodeBobotSelect change handler
    metodeBobotSelect.addEventListener('change', () => {
    // Logika lainnya untuk metode bobot (seperti perhitungan dan tampilan tabel Kriteria dan Subkriteria)

    // Isi tabel gabungan dengan bobot kriteria dan subkriteria
    fillGabunganTable();
    hasilPembobotanContainer.classList.remove('d-none'); // Tampilkan hasil pembobotan
});

});
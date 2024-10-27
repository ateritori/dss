document.addEventListener('DOMContentLoaded', function () {
    // Mendefinisikan elemen-elemen yang akan digunakan
    const pilihSemuaKriteriaBtn = document.getElementById('pilihsemuakriteria');
    const checkboxes = Array.from(document.querySelectorAll('.checkItemKriteria'));
    const resetPilihanBtn = document.getElementById('resetpilihan');
    const pilihanKriteriaList = document.getElementById('pilihankriteria');
    const lanjutPilihBobotBtn = document.getElementById('lanjutpilihbobot');
    const pilihanContainer = document.getElementById('pilihancontainer');
    const metodeBobotSelect = document.getElementById('metodebobot');
    const hasilPembobotanContainer = document.getElementById('hasilPembobotanContainer');
    const tabelPembobotanBody = document.querySelector('#tabelPembobotan tbody');
    const bobotDinamisContainer = document.getElementById('bobotdinamis');
    const inputContainer = document.getElementById('inputContainer'); // Container untuk input bobot dinamis
    const btnBobotDinamis = document.getElementById('btnbobotdinamis'); // Tombol simpan bobot dinamis

    // Fungsi untuk menampilkan pilihan di daftar
    const showPilihanKriteria = () => {
        pilihanKriteriaList.innerHTML = '';
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = checkbox.value;
                pilihanKriteriaList.appendChild(listItem);
            }
        });
    };

    // Fungsi untuk memperbarui ikon tombol pilih semua kriteria
    const updatePilihSemuaKriteriaIcon = () => {
        pilihSemuaKriteriaBtn.innerHTML = checkboxes.every(checkbox => checkbox.checked) 
            ? '<i class="bi bi-dash-square"></i>' 
            : '<i class="bi bi-check-square"></i>';
    };

    // Fungsi untuk menandai atau mereset semua checkbox
    pilihSemuaKriteriaBtn.addEventListener('click', () => {
        const allChecked = checkboxes.every(checkbox => checkbox.checked);
        checkboxes.forEach(checkbox => checkbox.checked = !allChecked);
        updatePilihSemuaKriteriaIcon();
    });

    // Fungsi untuk mereset pilihan checkbox dan dropdown metode bobot
    resetPilihanBtn.addEventListener('click', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });
        pilihanKriteriaList.innerHTML = '';
        pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-check-square"></i>';
        pilihSemuaKriteriaBtn.disabled = false;
        metodeBobotSelect.value = '';
        pilihanContainer.classList.add('d-none');
        hasilPembobotanContainer.classList.add('d-none');
        tabelPembobotanBody.innerHTML = '';
        bobotDinamisContainer.classList.add('d-none');
        inputContainer.innerHTML = ''; // Kosongkan container input dinamis
    });

    // Tambahkan listener untuk setiap checkbox
    checkboxes.forEach(checkbox => checkbox.addEventListener('change', updatePilihSemuaKriteriaIcon));

    // Event listener untuk tombol lanjut pilih bobot
    lanjutPilihBobotBtn.addEventListener('click', () => {
        showPilihanKriteria();
        checkboxes.forEach(checkbox => checkbox.disabled = true);
        pilihSemuaKriteriaBtn.disabled = true;
        pilihanContainer.classList.remove('d-none');
        alert("Pilihan kriteria telah disimpan.");
    });

    // Event listener untuk dropdown metode bobot
    metodeBobotSelect.addEventListener('change', () => {
        const selectedValue = metodeBobotSelect.value;

        // Jika "Bobot Semua Kriteria Sama" dipilih
        if (selectedValue === '1') {
            const selectedCheckboxes = checkboxes.filter(checkbox => checkbox.checked);
            const jumlahKriteria = selectedCheckboxes.length;

            if (jumlahKriteria > 0) {
                const bobot = (100 / jumlahKriteria).toFixed(2);
                tabelPembobotanBody.innerHTML = '';
                selectedCheckboxes.forEach((checkbox, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${checkbox.value}</td>
                        <td align="center">${bobot}%</td>
                    `;
                    tabelPembobotanBody.appendChild(row);
                });
                hasilPembobotanContainer.classList.remove('d-none');
                bobotDinamisContainer.classList.add('d-none');
                inputContainer.innerHTML = ''; // Kosongkan konten dinamis
            } else {
                hasilPembobotanContainer.classList.add('d-none');
            }
        } 
        // Jika "Masukkan Bobot Dinamis" dipilih
        else if (selectedValue === '2') {
            const selectedCheckboxes = checkboxes.filter(checkbox => checkbox.checked);
            inputContainer.innerHTML = ''; // Kosongkan konten sebelumnya

            if (selectedCheckboxes.length > 0) {
                selectedCheckboxes.forEach(checkbox => {
                    const div = document.createElement('div');
                    div.className = 'col mb-3'; // Mengatur setiap input agar masuk ke dalam satu kolom dan memberi margin bawah
            
                    // Menambahkan input dengan placeholder yang sesuai
                    div.innerHTML = `
                    <label class="form-label">${checkbox.value}</label>
                    <input type="number" class="form-control w-100" name="bobotkriteria" min="10" max="100" placeholder="Range 10% - 100%">
        `;
                    
                    inputContainer.appendChild(div); // Memasukkan setiap input ke dalam container
                });
            
                bobotDinamisContainer.classList.remove('d-none'); // Menampilkan bobot dinamis container
                hasilPembobotanContainer.classList.add('d-none'); // Menyembunyikan hasil pembobotan jika ada
            } else {
                bobotDinamisContainer.classList.add('d-none'); // Sembunyikan jika tidak ada kriteria yang dipilih
            }
        } 
        // Jika tidak ada metode bobot yang dipilih
        else {
            hasilPembobotanContainer.classList.add('d-none');
            bobotDinamisContainer.classList.add('d-none');
            inputContainer.innerHTML = ''; // Kosongkan jika pilihan berubah
        }
    });

    // Event listener untuk tombol simpan bobot dinamis
    btnBobotDinamis.addEventListener('click', () => {
        const bobotInputs = document.querySelectorAll('input[name="bobotkriteria"]');
        let totalBobot = 0;
        let inputValues = [];

        // Hitung total bobot dan simpan nilai setiap input
        bobotInputs.forEach(input => {
            const bobotValue = parseFloat(input.value) || 0;
            totalBobot += bobotValue;
            inputValues.push({ kriteria: input.previousElementSibling.textContent, bobot: bobotValue });
        });

        // Validasi total bobot harus 100%
        if (totalBobot !== 100) {
            alert("Jumlah bobot kriteria harus 100%");
            return; // Stop proses jika tidak valid
        }

        // Kosongkan tabel pembobotan sebelum menambahkan data baru
        tabelPembobotanBody.innerHTML = '';

        // Tampilkan hasil inputan bobot di tabel
        inputValues.forEach((data, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${data.kriteria}</td>
                <td align="center">${data.bobot}%</td>
            `;
            tabelPembobotanBody.appendChild(row);
        });

        // Tampilkan div hasil pembobotan dan sembunyikan bobot dinamis
        hasilPembobotanContainer.classList.remove('d-none');
        bobotDinamisContainer.classList.add('d-none');
    });
});
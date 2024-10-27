document.addEventListener('DOMContentLoaded', function () {
    const pilihSemuaKriteriaBtn = document.getElementById('pilihsemuakriteria');
    const checkboxes = document.querySelectorAll('.checkItemKriteria');
    const resetPilihanBtn = document.getElementById('resetpilihan');
    const pilihanKriteriaList = document.getElementById('pilihankriteria');
    const lanjutPilihBobotBtn = document.getElementById('lanjutpilihbobot');
    const pilihanContainer = document.getElementById('pilihancontainer');
    const metodeBobotSelect = document.getElementById('metodebobot');
    const hasilPembobotanContainer = document.getElementById('hasilPembobotanContainer');
    const tabelPembobotanBody = document.querySelector('#tabelPembobotan tbody');

    // Fungsi untuk menampilkan pilihan di daftar (dipanggil saat tombol lanjut)
    function showPilihanKriteria() {
        pilihanKriteriaList.innerHTML = ''; // Kosongkan daftar pilihan
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const listItem = document.createElement('li');
                listItem.textContent = checkbox.value; // Ambil nilai dari checkbox
                pilihanKriteriaList.appendChild(listItem); // Tambahkan item ke daftar
            }
        });
    }

    // Fungsi untuk memperbarui ikon tombol pilih semua kriteria
    function updatePilihSemuaKriteriaIcon() {
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        if (allChecked) {
            pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-dash-square"></i>'; // Ganti dengan ikon minus
        } else {
            pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-check-square"></i>'; // Ganti kembali dengan ikon check
        }
    }

    // Fungsi untuk menandai atau mereset semua checkbox
    pilihSemuaKriteriaBtn.addEventListener('click', function () {
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

        if (allChecked) {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        } else {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }
        updatePilihSemuaKriteriaIcon(); // Update ikon setelah mengubah status checkbox
    });

    // Fungsi untuk mereset pilihan checkbox dan dropdown metode bobot
    resetPilihanBtn.addEventListener('click', function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false; // Reset semua checkbox
            checkbox.disabled = false; // Aktifkan kembali checkbox
        });
        pilihanKriteriaList.innerHTML = ''; // Kosongkan daftar pilihan
        pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-check-square"></i>'; // Ganti kembali ikon check
        pilihSemuaKriteriaBtn.disabled = false; // Aktifkan kembali tombol pilih semua

        // Reset dropdown metode bobot ke pilihan awal
        metodeBobotSelect.value = ''; 

        pilihanContainer.classList.add('d-none'); // Sembunyikan pilihan container
        hasilPembobotanContainer.classList.add('d-none'); // Sembunyikan hasil pembobotan
        tabelPembobotanBody.innerHTML = ''; // Kosongkan tabel pembobotan
    });

    // Tambahkan listener untuk setiap checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            updatePilihSemuaKriteriaIcon(); // Update ikon saat checkbox berubah
        });
    });

    // Event listener untuk tombol lanjut pilih bobot
    lanjutPilihBobotBtn.addEventListener('click', function () {
        showPilihanKriteria(); // Tampilkan pilihan di daftar
        checkboxes.forEach(checkbox => {
            checkbox.disabled = true;
        });
        pilihSemuaKriteriaBtn.disabled = true;
        pilihanContainer.classList.remove('d-none'); // Tampilkan pilihan container

        alert("Pilihan kriteria telah disimpan.");
    });

    // Event listener untuk dropdown metode bobot
    metodeBobotSelect.addEventListener('change', function () {
        const selectedValue = metodeBobotSelect.value;
        if (selectedValue === '1') { // Jika "Bobot Semua Kriteria Sama" dipilih
            const selectedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
            const jumlahKriteria = selectedCheckboxes.length;

            if (jumlahKriteria > 0) {
                const bobot = (100 / jumlahKriteria).toFixed(2); // Menghitung bobot per kriteria
                tabelPembobotanBody.innerHTML = ''; // Kosongkan tabel pembobotan

                // Menambahkan data ke tabel
                selectedCheckboxes.forEach((checkbox, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${checkbox.value}</td>
                        <td align="center">${bobot}%</td>
                    `;
                    tabelPembobotanBody.appendChild(row);
                });

                hasilPembobotanContainer.classList.remove('d-none'); // Tampilkan hasil pembobotan
            } else {
                hasilPembobotanContainer.classList.add('d-none'); // Sembunyikan hasil pembobotan
                tabelPembobotanBody.innerHTML = ''; // Kosongkan tabel pembobotan
            }
        } else {
            hasilPembobotanContainer.classList.add('d-none'); // Sembunyikan hasil pembobotan
            tabelPembobotanBody.innerHTML = ''; // Kosongkan tabel pembobotan
        }
    });
});
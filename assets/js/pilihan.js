// Fungsi untuk memperbarui daftar pilihan kriteria dan subkriteria
function updatePilihanLists() {
    pilihKriteriaList.innerHTML = Array.from(checkItemKriteria)
        .filter(item => item.checked)
        .map(item => `<li>${item.value}</li>`)
        .join("");

    pilihSubKriteriaList.innerHTML = Array.from(checkItemSubkriteria)
        .filter(item => item.checked)
        .map(item => `<li>${item.value}</li>`)
        .join("");
}

// Fungsi untuk mencentang/menonaktifkan semua kriteria dan subkriteria
function toggleAllKriteria() {
    const allChecked = Array.from(checkItemKriteria).every(item => item.checked);
    checkItemKriteria.forEach(item => {
        item.checked = !allChecked;
        toggleSubkriteria(item);
    });
    checkItemSubkriteria.forEach(subItem => {
        if (!subItem.disabled) {
            subItem.checked = !allChecked;
        }
    });
    pilihSemuaKriteriaBtn.innerHTML = allChecked
        ? "<i class='bi bi-check-all'></i>"
        : "<i class='bi bi-dash-square-fill'></i>";

    updatePilihanLists();
    updateLanjutPilihBobotStatus();
}

// Fungsi untuk mengatur status subkriteria berdasarkan kriteria terkait
function toggleSubkriteria(kriteriaCheckbox) {
    const subkriteria = Array.from(checkItemSubkriteria).filter(
        subItem => subItem.dataset.idKriteria === kriteriaCheckbox.dataset.id
    );
    subkriteria.forEach(subItem => {
        subItem.checked = kriteriaCheckbox.checked;
        subItem.disabled = !kriteriaCheckbox.checked;
    });
    updatePilihanLists();
    updateLanjutPilihBobotStatus();
}

// Fungsi untuk memeriksa apakah minimal 3 kriteria telah dipilih
function checkKriteriaCount() {
    return Array.from(checkItemKriteria).filter(item => item.checked).length >= 3;
}

// Fungsi untuk memperbarui status tombol "Lanjut Pilih Bobot"
function updateLanjutPilihBobotStatus() {
    lanjutPilihBobotBtn.disabled = !checkKriteriaCount();
}
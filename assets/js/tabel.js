// Fungsi untuk mereset semua pilihan kriteria dan subkriteria
function resetSelections() {
    checkItemKriteria.forEach(item => item.checked = item.disabled = false);
    checkItemSubkriteria.forEach(item => {
        item.checked = false;
        item.disabled = true;
    });

    pilihSemuaKriteriaBtn.innerHTML = "<i class='bi bi-check-all'></i>";
    pilihKriteriaList.innerHTML = pilihSubKriteriaList.innerHTML = "";
    pilihanContainer.classList.add("d-none");
    metodeBobotSelect.value = "";
    lanjutPilihBobotBtn.disabled = true;

    resetTables();
}

// Fungsi untuk mereset tampilan tabel
function resetTables() {
    tabelKriteriaBody.innerHTML = tabelSubkriteriaBody.innerHTML = tabelGabunganBody.innerHTML = "";
    hasilPembobotanContainer.classList.add("d-none");
}
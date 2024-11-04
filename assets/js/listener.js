// Event Listener
pilihSemuaKriteriaBtn.addEventListener("click", toggleAllKriteria);
resetPilihanBtn.addEventListener("click", resetSelections);

checkItemKriteria.forEach(kriteriaCheckbox => {
    kriteriaCheckbox.addEventListener("change", () => toggleSubkriteria(kriteriaCheckbox));
});

lanjutPilihBobotBtn.addEventListener("click", () => {
    if (checkKriteriaCount()) {
        pilihanContainer.classList.remove("d-none");
        updatePilihanLists();
        metodeBobotSelect.value = "";

        [
            ...checkItemKriteria,
            ...checkItemSubkriteria,
            pilihSemuaKriteriaBtn,
            lanjutPilihBobotBtn,
        ].forEach(item => (item.disabled = true));
    } else {
        alert("Silakan pilih minimal 3 kriteria.");
    }
});

// Event Listener untuk metode bobot
metodeBobotSelect.addEventListener("change", function () {
    resetTables(); // Kosongkan tabel setiap kali metode bobot berubah

    if (this.value === "1") {
        // Tampilkan hasil tabel
        updateTables();
        inputKriteriaContainer.innerHTML = ""; // Sembunyikan input bobot
        inputSubKriteriaContainer.innerHTML = ""; // Sembunyikan input bobot
    } else if (this.value === "2") {
        // Tampilkan input bobot
        renderInputBobotKriteria();
        renderInputBobotSubkriteria();
        hasilPembobotanContainer.classList.add("d-none"); // Sembunyikan hasil tabel
    }

    function resetSelections() {
        checkItemKriteria.forEach((item) => (item.checked = item.disabled = false));
        checkItemSubkriteria.forEach((item) => {
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

    function renderBobotInputs(container, items) {
        container.innerHTML = items
            .filter((item) => item.checked)
            .map(
                (item) => `
              <div class="col">
                <label>${item.value}</label>
                <input type="number" class="form-control" placeholder="Bobot ${item.value}" min="0" max="100">
              </div>
            `
            )
            .join("");
    }
});
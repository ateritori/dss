// Fungsi untuk memperbarui tabel dengan bobot kriteria dan subkriteria yang dipilih
function updateTables() {
    hasilPembobotanContainer.classList.remove("d-none");
    tabelKriteriaBody.innerHTML = tabelSubkriteriaBody.innerHTML = tabelGabunganBody.innerHTML = "";

    let gabunganIndex = 1;
    const checkedKriteria = Array.from(checkItemKriteria).filter(kriteria => kriteria.checked);
    const jumlahKriteria = checkedKriteria.length;
    let totalBobotKriteria = 0;
    let totalBobotSubkriteria = 0;
    let totalBobotGabungan = 0;

    checkedKriteria.forEach((kriteria, kriteriaIndex) => {
        const bobotKriteria = (100 / jumlahKriteria).toFixed(2);
        tabelKriteriaBody.insertRow().innerHTML = `<td>${kriteriaIndex + 1}</td><td>${kriteria.value}</td><td>${bobotKriteria}</td>`;
        totalBobotKriteria += parseFloat(bobotKriteria);

        const subkriteriaTerkait = Array.from(checkItemSubkriteria).filter(
            subkriteria => subkriteria.dataset.idKriteria === kriteria.dataset.id && subkriteria.checked
        );

        if (!subkriteriaTerkait.length) {
            tabelGabunganBody.insertRow().innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value}</td><td>${kriteria.dataset.tipe_kriteria}</td><td>${bobotKriteria}</td>`;
            totalBobotGabungan += parseFloat(bobotKriteria);
            gabunganIndex++;
        } else {
            const bobotSubkriteria = (100 / subkriteriaTerkait.length).toFixed(2);
            subkriteriaTerkait.forEach((subkriteria, subkriteriaIndex) => {
                tabelSubkriteriaBody.insertRow().innerHTML = `<td>${subkriteriaIndex + 1}</td><td>${kriteria.value} - ${subkriteria.value}</td><td>${bobotSubkriteria}</td>`;
                const bobotGabungan = (bobotSubkriteria * (bobotKriteria / 100)).toFixed(2);
                tabelGabunganBody.insertRow().innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value} - ${subkriteria.value}</td><td>${subkriteria.dataset.tipe_subkriteria}</td><td>${bobotGabungan}</td>`;
                totalBobotGabungan += parseFloat(bobotGabungan);
                gabunganIndex++;
                totalBobotSubkriteria += parseFloat(bobotSubkriteria);
            });
        }
    });

    totalBobotKriteriaElement.textContent = `${totalBobotKriteria.toFixed(2)} %`;
    totalBobotSubkriteriaElement.textContent = `${totalBobotSubkriteria.toFixed(2)} %`;
    totalBobotGabunganElement.textContent = `${totalBobotGabungan.toFixed(2)} %`;
}
document.addEventListener("DOMContentLoaded", function () {
  const checkItemKriteria = document.querySelectorAll(".checkItemKriteria");
  const checkItemSubkriteria = document.querySelectorAll(".checkItemSubkriteria");
  const pilihSemuaKriteriaBtn = document.getElementById("pilihsemuakriteria");
  const lanjutPilihBobotBtn = document.getElementById("lanjutpilihbobot");
  const pilihanContainer = document.getElementById("pilihancontainer");
  const metodeBobotSelect = document.getElementById("metodebobot");
  const resetPilihanBtn = document.getElementById("resetpilihan");
  const pilihKriteriaList = document.getElementById("pilihankriteria");
  const pilihSubKriteriaList = document.getElementById("pilihansubkriteria");
  const tabelKriteria = document.getElementById("tabelKriteria");
  const tabelSubkriteria = document.getElementById("tabelSubkriteria");
  const tabelGabungan = document.getElementById("tabelGabungan");
  const hasilPembobotanContainer = document.getElementById("hasilPembobotanContainer");

  function updatePilihanLists() {
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";
    checkItemKriteria.forEach((item) => {
      if (item.checked) pilihKriteriaList.innerHTML += `<li>${item.value}</li>`;
    });
    checkItemSubkriteria.forEach((item) => {
      if (item.checked) pilihSubKriteriaList.innerHTML += `<li>${item.value}</li>`;
    });
  }

  function toggleAllKriteria() {
    const allChecked = Array.from(checkItemKriteria).every((item) => item.checked);

    checkItemKriteria.forEach((item) => {
      item.checked = !allChecked;
      toggleSubkriteria(item);
    });

    checkItemSubkriteria.forEach((subItem) => {
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

  function toggleSubkriteria(kriteriaCheckbox) {
    const subkriteria = Array.from(checkItemSubkriteria).filter(
      (subItem) => subItem.dataset.idKriteria === kriteriaCheckbox.dataset.id
    );

    subkriteria.forEach((subItem) => {
      subItem.checked = kriteriaCheckbox.checked;
      subItem.disabled = !kriteriaCheckbox.checked;
    });

    updatePilihanLists();
    updateLanjutPilihBobotStatus();
  }

  function checkKriteriaCount() {
    return Array.from(checkItemKriteria).filter((item) => item.checked).length >= 3;
  }

  function updateLanjutPilihBobotStatus() {
    lanjutPilihBobotBtn.disabled = !checkKriteriaCount();
  }

  function resetSelections() {
    checkItemKriteria.forEach((item) => {
      item.checked = false;
      item.disabled = false;
    });
    checkItemSubkriteria.forEach((item) => {
      item.checked = false;
      item.disabled = true;
    });

    pilihSemuaKriteriaBtn.innerHTML = "<i class='bi bi-check-all'></i>";
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";

    pilihanContainer.classList.add("d-none");
    metodeBobotSelect.value = "";

    pilihSemuaKriteriaBtn.disabled = false;
    lanjutPilihBobotBtn.disabled = true;

    updateLanjutPilihBobotStatus();
    resetTables();
  }

  pilihSemuaKriteriaBtn.addEventListener("click", toggleAllKriteria);
  resetPilihanBtn.addEventListener("click", resetSelections);

  checkItemKriteria.forEach((kriteriaCheckbox) => {
    kriteriaCheckbox.addEventListener("change", () => {
      toggleSubkriteria(kriteriaCheckbox);
    });
  });

  lanjutPilihBobotBtn.addEventListener("click", () => {
    if (checkKriteriaCount()) {
      pilihanContainer.classList.remove("d-none");
      updatePilihanLists();
      metodeBobotSelect.value = "";

      [...checkItemKriteria, ...checkItemSubkriteria, pilihSemuaKriteriaBtn, lanjutPilihBobotBtn].forEach((item) => (item.disabled = true));
    } else {
      alert("Silakan pilih minimal 3 kriteria.");
    }
  });

  updateLanjutPilihBobotStatus();

  function updateTables() {
    hasilPembobotanContainer.classList.remove("d-none");

    const tabelKriteria = document.querySelector("#tabelKriteria tbody");
    const tabelSubkriteria = document.querySelector("#tabelSubkriteria tbody");
    const tabelGabungan = document.querySelector("#tabelGabungan tbody");
    const totalBobotKriteriaElement = document.querySelector("#totalBobotKriteria");
    const totalBobotSubkriteriaElement = document.querySelector("#totalBobotSubkriteria");
    const totalBobotGabunganElement = document.querySelector("#totalBobotGabungan");

    tabelKriteria.innerHTML = "";
    tabelSubkriteria.innerHTML = "";
    tabelGabungan.innerHTML = "";

    let gabunganIndex = 1;
    const jumlahKriteria = Array.from(checkItemKriteria).filter((kriteria) => kriteria.checked).length;

    let totalBobotKriteria = 0;
    let totalBobotSubkriteria = 0;
    let totalBobotGabungan = 0;

    let kriteriaIndex = 1;
    checkItemKriteria.forEach((kriteria) => {
      if (kriteria.checked) {
        const bobotKriteria = (100 / jumlahKriteria).toFixed(2);
        const row = tabelKriteria.insertRow();
        row.innerHTML = `<td>${kriteriaIndex}</td><td>${kriteria.value}</td><td>${bobotKriteria}</td>`;
        totalBobotKriteria += parseFloat(bobotKriteria);
        kriteriaIndex++;

        const subkriteriaTerkait = Array.from(checkItemSubkriteria).filter(
          (subkriteria) => subkriteria.dataset.idKriteria === kriteria.dataset.id && subkriteria.checked
        );
        const jumlahSubkriteria = subkriteriaTerkait.length;

        if (jumlahSubkriteria === 0) {
          const gabunganRow = tabelGabungan.insertRow();
          gabunganRow.innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value}</td><td>${kriteria.dataset.tipe_kriteria}</td><td>${bobotKriteria}</td>`;
          totalBobotGabungan += parseFloat(bobotKriteria);
          gabunganIndex++;
        } else {
          const bobotSubkriteria = (100 / jumlahSubkriteria).toFixed(2);
          let subkriteriaIndex = 1;
          let totalBobotSubkriteriaPerKriteria = 0;

          subkriteriaTerkait.forEach((subkriteria) => {
            const rowSubkriteria = tabelSubkriteria.insertRow();
            rowSubkriteria.innerHTML = `<td>${subkriteriaIndex}</td><td>${kriteria.value} - ${subkriteria.value}</td><td>${bobotSubkriteria}</td>`;
            totalBobotSubkriteria += parseFloat(bobotSubkriteria);
            totalBobotSubkriteriaPerKriteria += parseFloat(bobotSubkriteria);
            subkriteriaIndex++;

            const bobotGabungan = (bobotSubkriteria * (bobotKriteria / 100)).toFixed(2);
            const gabunganRow = tabelGabungan.insertRow();
            gabunganRow.innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value} - ${subkriteria.value}</td><td>${subkriteria.dataset.tipe_subkriteria}</td><td>${bobotGabungan}</td>`;
            totalBobotGabungan += parseFloat(bobotGabungan);
            gabunganIndex++;
          });

          const rowTotalSubkriteria = tabelSubkriteria.insertRow();
          rowTotalSubkriteria.innerHTML = `<td colspan="2">Total Bobot Subkriteria: ${totalBobotSubkriteriaPerKriteria.toFixed(2)}</td><td></td>`;
        }
      }
    });

    totalBobotKriteriaElement.innerText = totalBobotKriteria.toFixed(2);
    totalBobotSubkriteriaElement.innerText = totalBobotSubkriteria.toFixed(2);
    totalBobotGabunganElement.innerText = totalBobotGabungan.toFixed(2);
  }

  function resetTables() {
    document.querySelector("#tabelKriteria tbody").innerHTML = "";
    document.querySelector("#tabelSubkriteria tbody").innerHTML = "";
    document.querySelector("#tabelGabungan tbody").innerHTML = "";
    hasilPembobotanContainer.classList.add("d-none");
    document.querySelector("#totalBobotKriteria").innerText = `0.00`;
    document.querySelector("#totalBobotSubkriteria").innerText = `0.00`;
    document.querySelector("#totalBobotGabungan").innerText = `0.00`;
  }

  metodeBobotSelect.addEventListener("change", () => {
    if (metodeBobotSelect.value === "1") {
      updateTables();
    } else {
      resetTables();
    }
  });
});

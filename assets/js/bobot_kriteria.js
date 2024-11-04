document.addEventListener("DOMContentLoaded", function () {
  const checkItemKriteria = document.querySelectorAll(".checkItemKriteria");
  const checkItemSubkriteria = document.querySelectorAll(
    ".checkItemSubkriteria"
  );
  const pilihSemuaKriteriaBtn = document.getElementById("pilihsemuakriteria");
  const lanjutPilihBobotBtn = document.getElementById("lanjutpilihbobot");
  const pilihanContainer = document.getElementById("pilihancontainer");
  const metodeBobotSelect = document.getElementById("metodebobot");
  const resetPilihanBtn = document.getElementById("resetpilihan");
  const pilihKriteriaList = document.getElementById("pilihankriteria");
  const pilihSubKriteriaList = document.getElementById("pilihansubkriteria");
  const tabelKriteriaBody = document.querySelector("#tabelKriteria tbody");
  const tabelSubkriteriaBody = document.querySelector(
    "#tabelSubkriteria tbody"
  );
  const tabelGabunganBody = document.querySelector("#tabelGabungan tbody");
  const hasilPembobotanContainer = document.getElementById(
    "hasilPembobotanContainer"
  );
  const inputKriteriaContainer = document.getElementById(
    "inputKriteriaContainer"
  );
  const inputSubKriteriaContainer = document.getElementById(
    "inputSubKriteriaContainer"
  );
  const totalBobotKriteriaElement = document.querySelector(
    "#totalBobotKriteria"
  );
  const totalBobotSubkriteriaElement = document.querySelector(
    "#totalBobotSubkriteria"
  );
  const totalBobotGabunganElement = document.querySelector(
    "#totalBobotGabungan"
  );

  function updatePilihanLists() {
    pilihKriteriaList.innerHTML = Array.from(checkItemKriteria)
      .filter((item) => item.checked)
      .map((item) => `<li>${item.value}</li>`)
      .join("");

    pilihSubKriteriaList.innerHTML = Array.from(checkItemSubkriteria)
      .filter((item) => item.checked)
      .map((item) => `<li>${item.value}</li>`)
      .join("");
  }

  function toggleAllKriteria() {
    const allChecked = Array.from(checkItemKriteria).every(
      (item) => item.checked
    );
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
    return (
      Array.from(checkItemKriteria).filter((item) => item.checked).length >= 3
    );
  }

  function updateLanjutPilihBobotStatus() {
    lanjutPilihBobotBtn.disabled = !checkKriteriaCount();
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

  pilihSemuaKriteriaBtn.addEventListener("click", toggleAllKriteria);
  resetPilihanBtn.addEventListener("click", resetSelections);

  checkItemKriteria.forEach((kriteriaCheckbox) => {
    kriteriaCheckbox.addEventListener("change", () =>
      toggleSubkriteria(kriteriaCheckbox)
    );
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
      ].forEach((item) => (item.disabled = true));
    } else {
      alert("Silakan pilih minimal 3 kriteria.");
    }
  });

  function updateTables() {
    hasilPembobotanContainer.classList.remove("d-none");
    tabelKriteriaBody.innerHTML =
      tabelSubkriteriaBody.innerHTML =
      tabelGabunganBody.innerHTML =
      "";

    let gabunganIndex = 1;
    const checkedKriteria = Array.from(checkItemKriteria).filter(
      (kriteria) => kriteria.checked
    );
    const jumlahKriteria = checkedKriteria.length;
    let totalBobotKriteria = 0;
    let totalBobotSubkriteria = 0;
    let totalBobotGabungan = 0;

    checkedKriteria.forEach((kriteria, kriteriaIndex) => {
      const bobotKriteria = (100 / jumlahKriteria).toFixed(2);
      tabelKriteriaBody.insertRow().innerHTML = `<td>${kriteriaIndex + 1
        }</td><td>${kriteria.value}</td><td>${bobotKriteria}</td>`;
      totalBobotKriteria += parseFloat(bobotKriteria);

      const subkriteriaTerkait = Array.from(checkItemSubkriteria).filter(
        (subkriteria) =>
          subkriteria.dataset.idKriteria === kriteria.dataset.id &&
          subkriteria.checked
      );

      if (!subkriteriaTerkait.length) {
        tabelGabunganBody.insertRow().innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value}</td><td>${kriteria.dataset.tipe_kriteria}</td><td>${bobotKriteria}</td>`;
        totalBobotGabungan += parseFloat(bobotKriteria);
        gabunganIndex++;
      } else {
        const bobotSubkriteria = (100 / subkriteriaTerkait.length).toFixed(2);
        subkriteriaTerkait.forEach((subkriteria, subkriteriaIndex) => {
          tabelSubkriteriaBody.insertRow().innerHTML = `<td>${subkriteriaIndex + 1
            }</td><td>${kriteria.value} - ${subkriteria.value
            }</td><td>${bobotSubkriteria}</td>`;
          const bobotGabungan = (
            bobotSubkriteria *
            (bobotKriteria / 100)
          ).toFixed(2);
          tabelGabunganBody.insertRow().innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value} - ${subkriteria.value}</td><td>${subkriteria.dataset.tipe_subkriteria}</td><td>${bobotGabungan}</td>`;
          totalBobotGabungan += parseFloat(bobotGabungan);
          gabunganIndex++;
          totalBobotSubkriteria += parseFloat(bobotSubkriteria);
        });
      }
    });

    totalBobotKriteriaElement.textContent = `${totalBobotKriteria.toFixed(
      2
    )} %`;
    totalBobotSubkriteriaElement.textContent = `${totalBobotSubkriteria.toFixed(
      2
    )} %`;
    totalBobotGabunganElement.textContent = `${totalBobotGabungan.toFixed(
      2
    )} %`;
  }

  function resetTables() {
    tabelKriteriaBody.innerHTML =
      tabelSubkriteriaBody.innerHTML =
      tabelGabunganBody.innerHTML =
      "";
    hasilPembobotanContainer.classList.add("d-none");
  }

  metodeBobotSelect.addEventListener("change", function () {
    resetTables();
    if (this.value === "1") updateTables();
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const checkItemKriteria = document.querySelectorAll(".checkItemKriteria");
  const checkItemSubkriteria = document.querySelectorAll(
    ".checkItemSubkriteria"
  );
  const pilihSemuaKriteriaBtn = document.getElementById("pilihsemuakriteria");
  const lanjutPilihBobotBtn = document.getElementById("lanjutpilihbobot");
  const pilihanContainer = document.getElementById("pilihancontainer");
  const metodeBobotSelect = document.getElementById("metodebobot");
  const bobotDinamisContainer = document.getElementById("bobotdinamis");
  const inputKriteriaContainer = document.getElementById(
    "inputKriteriaContainer"
  );
  const inputSubKriteriaContainer = document.getElementById(
    "inputSubKriteriaContainer"
  );
  const hasilPembobotanContainer = document.getElementById(
    "hasilPembobotanContainer"
  );
  const pilihKriteriaList = document.getElementById("pilihankriteria");
  const pilihSubKriteriaList = document.getElementById("pilihansubkriteria");
  const btnBobotDinamis = document.getElementById("btnbobotdinamis");

  // Fungsi untuk menampilkan kriteria dan subkriteria yang dipilih
  function updatePilihanLists() {
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";

    checkItemKriteria.forEach((item) => {
      if (item.checked) {
        const listItem = document.createElement("li");
        listItem.textContent = item.value;
        pilihKriteriaList.appendChild(listItem);
      }
    });

    checkItemSubkriteria.forEach((item) => {
      if (item.checked) {
        const listItem = document.createElement("li");
        listItem.textContent = item.value;
        pilihSubKriteriaList.appendChild(listItem);
      }
    });
  }

  // Pilih semua kriteria
  pilihSemuaKriteriaBtn.addEventListener("click", () => {
    checkItemKriteria.forEach((item) => (item.checked = true));
    checkItemSubkriteria.forEach((item) => (item.checked = true));
    updatePilihanLists();
  });

  // Lanjut pilih bobot
  lanjutPilihBobotBtn.addEventListener("click", () => {
    pilihanContainer.classList.remove("d-none");
    updatePilihanLists();
  });

  // Menampilkan atau menyembunyikan input bobot dinamis
  metodeBobotSelect.addEventListener("change", (event) => {
    const metode = event.target.value;

    // Reset tampilan sesuai pilihan metode
    if (metode === "2") {
      bobotDinamisContainer.classList.remove("d-none");
      hasilPembobotanContainer.classList.add("d-none");
      generateDynamicInputs(); // Membuat input dinamis untuk kriteria dan subkriteria
    } else {
      bobotDinamisContainer.classList.add("d-none");
      hasilPembobotanContainer.classList.remove("d-none");
    }
  });

  // Fungsi untuk membuat input dinamis untuk kriteria dan subkriteria
  function generateDynamicInputs() {
    inputKriteriaContainer.innerHTML = "";
    inputSubKriteriaContainer.innerHTML = "";

    checkItemKriteria.forEach((item) => {
      if (item.checked) {
        const inputGroup = document.createElement("div");
        inputGroup.classList.add("input-group", "mb-2");

        const label = document.createElement("label");
        label.classList.add("input-group-text");
        label.textContent = item.value;

        const input = document.createElement("input");
        input.type = "number";
        input.classList.add("form-control");
        input.placeholder = `Bobot untuk ${item.value}`;
        input.name = `bobot_kriteria_${item.dataset.id}`;
        inputGroup.appendChild(label);
        inputGroup.appendChild(input);
        inputKriteriaContainer.appendChild(inputGroup);
      }
    });

    checkItemSubkriteria.forEach((item) => {
      if (item.checked) {
        const inputGroup = document.createElement("div");
        inputGroup.classList.add("input-group", "mb-2");

        const label = document.createElement("label");
        label.classList.add("input-group-text");
        label.textContent = item.value;

        const input = document.createElement("input");
        input.type = "number";
        input.classList.add("form-control");
        input.placeholder = `Bobot untuk ${item.value}`;
        input.name = `bobot_subkriteria_${item.dataset.idKriteria}`;
        inputGroup.appendChild(label);
        inputGroup.appendChild(input);
        inputSubKriteriaContainer.appendChild(inputGroup);
      }
    });
  }

  // Gunakan bobot dari input dinamis
  btnBobotDinamis.addEventListener("click", () => {
    const kriteriaInputs = inputKriteriaContainer.querySelectorAll("input");
    const subKriteriaInputs =
      inputSubKriteriaContainer.querySelectorAll("input");

    const hasilKriteria = Array.from(kriteriaInputs).map((input, index) => ({
      no: index + 1,
      nama: input.previousElementSibling.textContent,
      bobot: input.value,
    }));

    const hasilSubKriteria = Array.from(subKriteriaInputs).map(
      (input, index) => ({
        no: index + 1,
        nama: input.previousElementSibling.textContent,
        bobot: input.value,
      })
    );

    // Tampilkan hasil pembobotan kriteria dan subkriteria
    populateTable("#tabelKriteria tbody", hasilKriteria);
    populateTable("#tabelSubkriteria tbody", hasilSubKriteria);

    hasilPembobotanContainer.classList.remove("d-none");
  });

  // Fungsi untuk memasukkan data ke tabel
  function populateTable(tableSelector, data) {
    const tableBody = document.querySelector(tableSelector);
    tableBody.innerHTML = "";

    data.forEach((row) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
                <td>${row.no}</td>
                <td>${row.nama}</td>
                <td>${row.bobot}</td>
            `;
      tableBody.appendChild(tr);
    });
  }

  // Reset pilihan
  document.getElementById("resetpilihan").addEventListener("click", () => {
    checkItemKriteria.forEach((item) => (item.checked = false));
    checkItemSubkriteria.forEach((item) => (item.checked = false));
    inputKriteriaContainer.innerHTML = "";
    inputSubKriteriaContainer.innerHTML = "";
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";
    hasilPembobotanContainer.classList.add("d-none");
    bobotDinamisContainer.classList.add("d-none");
    metodeBobotSelect.value = "";
  });
});

document.addEventListener("DOMContentLoaded", function () {
  // Mendefinisikan elemen-elemen yang akan digunakan
  const pilihSemuaKriteriaBtn = document.getElementById("pilihsemuakriteria");
  const checkboxesKriteria = Array.from(
    document.querySelectorAll(".checkItemKriteria")
  );
  const checkboxesSubkriteria = Array.from(
    document.querySelectorAll(".checkItemSubkriteria")
  );
  const resetPilihanBtn = document.getElementById("resetpilihan");
  const pilihanKriteriaList = document.getElementById("pilihankriteria");
  const pilihanSubkriteriaList = document.getElementById("pilihansubkriteria");
  const lanjutPilihBobotBtn = document.getElementById("lanjutpilihbobot");
  const pilihanContainer = document.getElementById("pilihancontainer");
  const metodeBobotSelect = document.getElementById("metodebobot");
  const hasilPembobotanContainer = document.getElementById(
    "hasilPembobotanContainer"
  );
  const tabelKriteriaBody = document.querySelector("#tabelKriteria tbody");
  const tabelSubkriteriaBody = document.querySelector(
    "#tabelSubkriteria tbody"
  );
  const tabelGabunganBody = document.querySelector("#tabelGabungan tbody");
  const bobotDinamisContainer = document.getElementById("bobotdinamis");
  const inputKriteriaContainer = document.getElementById(
    "inputKriteriaContainer"
  );
  const inputSubKriteriaContainer = document.getElementById(
    "inputSubKriteriaContainer"
  );
  const btnBobotDinamis = document.getElementById("btnbobotdinamis");

  // Fungsi untuk memperbarui status checkbox subkriteria berdasarkan status kriteria induknya
  const updateCheckboxState = () => {
    checkboxesKriteria.forEach((kriteriaCheckbox) => {
      const kriteriaId = kriteriaCheckbox.getAttribute("data-id");
      checkboxesSubkriteria
        .filter((subCheckbox) => subCheckbox.dataset.idKriteria === kriteriaId)
        .forEach((subCheckbox) => {
          subCheckbox.disabled = !kriteriaCheckbox.checked;
          if (!kriteriaCheckbox.checked) subCheckbox.checked = false;
        });
    });
  };

  // Fungsi umum untuk menampilkan pilihan kriteria/subkriteria yang dicentang
  const showPilihan = (checkboxes, container) => {
    container.innerHTML = "";
    checkboxes
      .filter((checkbox) => checkbox.checked)
      .forEach((checkbox) => {
        const listItem = document.createElement("li");
        listItem.textContent = checkbox.value;
        container.appendChild(listItem);
      });
  };

  // Fungsi untuk memperbarui ikon tombol pilih semua kriteria
  const updatePilihSemuaKriteriaIcon = () => {
    const allChecked = checkboxesKriteria.every((checkbox) => checkbox.checked);
    pilihSemuaKriteriaBtn.innerHTML = allChecked
      ? '<i class="bi bi-dash-square"></i>'
      : '<i class="bi bi-check-square"></i>';
  };

  // Fungsi untuk menandai atau mereset semua checkbox
  pilihSemuaKriteriaBtn.addEventListener("click", () => {
    const allChecked = checkboxesKriteria.every((checkbox) => checkbox.checked);
    checkboxesKriteria.forEach((checkbox) => (checkbox.checked = !allChecked));

    checkboxesKriteria.forEach((checkbox) => {
      const kriteriaId = checkbox.getAttribute("data-id");
      checkboxesSubkriteria
        .filter((subCheckbox) => subCheckbox.dataset.idKriteria === kriteriaId)
        .forEach((subCheckbox) => {
          subCheckbox.disabled = !checkbox.checked;
          subCheckbox.checked = checkbox.checked;
        });
    });
    updatePilihSemuaKriteriaIcon();
  });

  // Fungsi untuk mereset pilihan checkbox dan dropdown metode bobot
  const resetSelections = () => {
    [...checkboxesKriteria, ...checkboxesSubkriteria].forEach((checkbox) => {
      checkbox.checked = false;
      checkbox.disabled = checkbox.classList.contains("checkItemSubkriteria");
    });
    pilihSemuaKriteriaBtn.innerHTML = '<i class="bi bi-check-square"></i>';
    pilihSemuaKriteriaBtn.disabled = false;
    pilihanKriteriaList.innerHTML = "";
    pilihanSubkriteriaList.innerHTML = "";
    metodeBobotSelect.value = "";
    pilihanContainer.classList.add("d-none");
    hasilPembobotanContainer.classList.add("d-none");
    tabelKriteriaBody.innerHTML = "";
    tabelSubkriteriaBody.innerHTML = "";
    tabelGabunganBody.innerHTML = "";
    bobotDinamisContainer.classList.add("d-none");
    inputKriteriaContainer.innerHTML = "";
    inputSubKriteriaContainer.innerHTML = "";
  };
  resetPilihanBtn.addEventListener("click", resetSelections);

  checkboxesKriteria.forEach((checkbox) =>
    checkbox.addEventListener("change", () => {
      updatePilihSemuaKriteriaIcon();
      updateCheckboxState();
    })
  );
  checkboxesSubkriteria.forEach((checkbox) =>
    checkbox.addEventListener("change", updatePilihSemuaKriteriaIcon)
  );

  lanjutPilihBobotBtn.addEventListener("click", () => {
    showPilihan(checkboxesKriteria, pilihanKriteriaList);
    showPilihan(checkboxesSubkriteria, pilihanSubkriteriaList);
    [...checkboxesKriteria, ...checkboxesSubkriteria].forEach(
      (checkbox) => (checkbox.disabled = true)
    );
    pilihSemuaKriteriaBtn.disabled = true;
    pilihanContainer.classList.remove("d-none");
    alert("Pilihan kriteria dan subkriteria telah disimpan.");
  });

  // Inisialisasi awal
  checkboxesSubkriteria.forEach((checkbox) => (checkbox.disabled = true));
  updateCheckboxState();

  metodeBobotSelect.addEventListener("change", () => {
    const selectedValue = metodeBobotSelect.value;
    const selectedKriteria = checkboxesKriteria.filter(
      (checkbox) => checkbox.checked
    );
    const selectedSubkriteria = checkboxesSubkriteria.filter(
      (checkbox) => checkbox.checked
    );

    hasilPembobotanContainer.classList.add("d-none");
    bobotDinamisContainer.classList.add("d-none");

    if (selectedValue === "1") {
      const bobotKriteria = (100 / selectedKriteria.length).toFixed(2);
      const bobotSubkriteria = (100 / selectedSubkriteria.length).toFixed(2);
      tabelKriteriaBody.innerHTML = "";
      tabelSubkriteriaBody.innerHTML = "";

      selectedKriteria.forEach((checkbox, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `<td>${index + 1}</td><td>${
          checkbox.value
        }</td><td align="center">${bobotKriteria}%</td>`;
        tabelKriteriaBody.appendChild(row);
      });

      selectedSubkriteria.forEach((checkbox, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `<td>${index + 1}</td><td>${
          checkbox.value
        }</td><td align="center">${bobotSubkriteria}%</td>`;
        tabelSubkriteriaBody.appendChild(row);
      });

      hasilPembobotanContainer.classList.remove("d-none");
      bobotDinamisContainer.classList.add("d-none");
    } else if (selectedValue === "2") {
      // Reset kontainer bobot dinamis
      inputKriteriaContainer.innerHTML = "";
      inputSubKriteriaContainer.innerHTML = "";
      checkboxesKriteria
        .filter((checkbox) => checkbox.checked)
        .forEach((checkbox) => {
          const div = document.createElement("div");
          div.classList.add(
            "col-sm-4",
            "col-sm-6",
            "col-12",
            "mb-3",
            "align-items-center"
          );
          div.innerHTML = `<label class="form-label">${checkbox.value}</label>
                          <input type="number" class="form-control" name="bobotkriteria" min="0" max="100" placeholder="Range 0-100">`;
          inputKriteriaContainer.appendChild(div);
        });

      checkboxesSubkriteria
        .filter((checkbox) => checkbox.checked)
        .forEach((checkbox) => {
          const div = document.createElement("div");
          div.classList.add(
            "col-sm-4",
            "col-sm-6",
            "col-12",
            "mb-3",
            "align-items-center"
          );
          div.innerHTML = `<label class="form-label">${checkbox.value}</label>
                          <input type="number" class="form-control" name="bobotsubkriteria" min="0" max="100" placeholder="Range 0-100">`;
          inputSubKriteriaContainer.appendChild(div);
        });

      bobotDinamisContainer.classList.remove("d-none");
      hasilPembobotanContainer.classList.add("d-none");
    }
  });

  btnBobotDinamis.addEventListener("click", () => {
    const bobotKriteriaInputs = document.querySelectorAll(
      'input[name="bobotkriteria"]'
    );
    const bobotSubkriteriaInputs = document.querySelectorAll(
      'input[name="bobotsubkriteria"]'
    );
    let totalBobotKriteria = 0;
    let totalBobotSubkriteria = 0;

    bobotKriteriaInputs.forEach(
      (input) => (totalBobotKriteria += Number(input.value))
    );
    bobotSubkriteriaInputs.forEach(
      (input) => (totalBobotSubkriteria += Number(input.value))
    );

    if (totalBobotKriteria !== 100 || totalBobotSubkriteria !== 100) {
      alert("Total bobot kriteria dan subkriteria masing-masing harus 100%.");
      return;
    }

    tabelKriteriaBody.innerHTML = "";
    bobotKriteriaInputs.forEach((input, index) => {
      const row = document.createElement("tr");
      row.innerHTML = `<td>${index + 1}</td><td>${
        input.previousElementSibling.textContent
      }</td><td align="center">${input.value}%</td>`;
      tabelKriteriaBody.appendChild(row);
    });

    tabelSubkriteriaBody.innerHTML = "";
    bobotSubkriteriaInputs.forEach((input, index) => {
      const row = document.createElement("tr");
      row.innerHTML = `<td>${index + 1}</td><td>${
        input.previousElementSibling.textContent
      }</td><td align="center">${input.value}%</td>`;
      tabelSubkriteriaBody.appendChild(row);
    });

    hasilPembobotanContainer.classList.remove("d-none");
    bobotDinamisContainer.classList.add("d-none");

    // Panggil fungsi fillGabunganTable untuk mengisi tabel gabungan
    fillGabunganTable();
  });

  function fillGabunganTable() {
    const tabelGabunganBody = document.querySelector("#tabelGabungan tbody");
    tabelGabunganBody.innerHTML = "";

    let rowIndex = 1;

    // Hitung jumlah kriteria yang dicentang
    const checkedKriteria = checkboxesKriteria.filter(
      (kriteriaCheckbox) => kriteriaCheckbox.checked
    );

    // Dapatkan bobot kriteria dari input jika metode bobot adalah 2
    const kriteriaBobotValue =
      metodeBobotSelect.value === "2"
        ? Array.from(
            document.querySelectorAll('input[name="bobotkriteria"]')
          ).map((input) => parseFloat(input.value) || 0)
        : Array(checkedKriteria.length).fill(100 / checkedKriteria.length); // Default bobot

    checkedKriteria.forEach((kriteriaCheckbox, index) => {
      const kriteriaId = kriteriaCheckbox.getAttribute("data-id");
      const bobotKriteria = kriteriaBobotValue[index]; // Ambil bobot kriteria yang sesuai

      // Cek apakah kriteria memiliki subkriteria yang dicentang
      const subkriteriaChecked = checkboxesSubkriteria.filter(
        (subCheckbox) =>
          subCheckbox.dataset.idKriteria === kriteriaId && subCheckbox.checked
      );

      // Jika memiliki subkriteria yang dicentang, hitung bobot gabungan subkriteria
      if (subkriteriaChecked.length > 0) {
        const subkriteriaBobotValue =
          metodeBobotSelect.value === "2"
            ? Array.from(
                document.querySelectorAll('input[name="bobotsubkriteria"]')
              ).map((input) => parseFloat(input.value) || 0)
            : Array(checkedKriteria.length).fill(
                100 / subkriteriaChecked.length
              ); // Default bobot

        subkriteriaChecked.forEach((subCheckbox, subIndex) => {
          const bobotSubkriteria = subkriteriaBobotValue[subIndex]; // Ambil bobot subkriteria yang sesuai
          const bobotGabungan = (bobotKriteria * bobotSubkriteria) / 100;

          const row = document.createElement("tr");
          row.innerHTML = `
                    <td>${rowIndex++}</td>
                    <td>${subCheckbox.value}</td>
                    <td align="center">${bobotGabungan.toFixed(2)}%</td>
                `;
          tabelGabunganBody.appendChild(row);
        });
      } else {
        // Jika tidak memiliki subkriteria, tampilkan kriteria langsung dengan bobot kriteria saja
        const row = document.createElement("tr");
        row.innerHTML = `
                <td>${rowIndex++}</td>
                <td>${kriteriaCheckbox.value}</td>
                <td align="center">${bobotKriteria.toFixed(2)}%</td>
            `;
        tabelGabunganBody.appendChild(row);
      }
    });
  }

  // Panggil fungsi fillGabunganTable() setelah pembobotan selesai
  metodeBobotSelect.addEventListener("change", () => {
    // Logika lainnya untuk metode bobot (seperti perhitungan dan tampilan tabel Kriteria dan Subkriteria)

    // Periksa apakah metode yang dipilih adalah 1
    if (metodeBobotSelect.value === "1") {
      // Isi tabel gabungan dengan bobot kriteria dan subkriteria
      fillGabunganTable();
      hasilPembobotanContainer.classList.remove("d-none"); // Tampilkan hasil pembobotan
    } else {
      // Jika metode bukan 1, sembunyikan hasil pembobotan
      hasilPembobotanContainer.classList.add("d-none");
    }
  });
});

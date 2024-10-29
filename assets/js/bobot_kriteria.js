document.addEventListener("DOMContentLoaded", function () {
  // Mendapatkan elemen checkbox dan tombol dari DOM
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
  const tabelKriteria = document.getElementById("tabelKriteria");
  const tabelSubkriteria = document.getElementById("tabelSubkriteria");
  const tabelGabungan = document.getElementById("tabelGabungan");
  const hasilPembobotanContainer = document.getElementById(
    "hasilPembobotanContainer"
  );

  // Memperbarui daftar pilihan kriteria dan subkriteria yang dicentang
  function updatePilihanLists() {
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";
    checkItemKriteria.forEach((item) => {
      if (item.checked) pilihKriteriaList.innerHTML += `<li>${item.value}</li>`;
    });
    checkItemSubkriteria.forEach((item) => {
      if (item.checked)
        pilihSubKriteriaList.innerHTML += `<li>${item.value}</li>`;
    });
  }

  // Mengaktifkan atau menonaktifkan semua checkbox kriteria dan subkriteria
  function toggleAllKriteria() {
    const allChecked = Array.from(checkItemKriteria).every(
      (item) => item.checked
    );

    // Toggle centang semua kriteria dan subkriteria terkait
    checkItemKriteria.forEach((item) => {
      item.checked = !allChecked;
      toggleSubkriteria(item); // Aktifkan subkriteria saat kriteria dicentang
    });

    // Pastikan subkriteria yang terbuka mengikuti status centang
    checkItemSubkriteria.forEach((subItem) => {
      if (!subItem.disabled) {
        subItem.checked = !allChecked;
      }
    });

    // Perbarui ikon pada tombol "Pilih Semua Kriteria"
    pilihSemuaKriteriaBtn.innerHTML = allChecked
      ? "<i class='bi bi-check-all'></i>"
      : "<i class='bi bi-dash-square-fill'></i>";

    updatePilihanLists(); // Perbarui daftar pilihan
    updateLanjutPilihBobotStatus(); // Perbarui status tombol "Lanjut Pilih Bobot"
  }

  // Mengaktifkan/menonaktifkan subkriteria berdasarkan status kriteria terkait
  function toggleSubkriteria(kriteriaCheckbox) {
    const subkriteria = Array.from(checkItemSubkriteria).filter(
      (subItem) => subItem.dataset.idKriteria === kriteriaCheckbox.dataset.id
    );

    subkriteria.forEach((subItem) => {
      subItem.checked = kriteriaCheckbox.checked;
      subItem.disabled = !kriteriaCheckbox.checked;
    });

    updatePilihanLists(); // Perbarui daftar pilihan
    updateLanjutPilihBobotStatus(); // Perbarui status tombol "Lanjut Pilih Bobot"
  }

  // Memeriksa apakah minimal 3 kriteria telah dicentang
  function checkKriteriaCount() {
    return (
      Array.from(checkItemKriteria).filter((item) => item.checked).length >= 3
    );
  }

  // Mengaktifkan/menonaktifkan tombol "Lanjut Pilih Bobot" berdasarkan jumlah kriteria yang dicentang
  function updateLanjutPilihBobotStatus() {
    lanjutPilihBobotBtn.disabled = !checkKriteriaCount();
  }

  // Fungsi untuk mereset semua pilihan ke keadaan awal
  function resetSelections() {
    // Mengatur ulang semua checkbox kriteria dan subkriteria
    checkItemKriteria.forEach((item) => {
      item.checked = false;
      item.disabled = false;
    });
    checkItemSubkriteria.forEach((item) => {
      item.checked = false;
      item.disabled = true;
    });

    // Reset tampilan tombol "Pilih Semua Kriteria"
    pilihSemuaKriteriaBtn.innerHTML = "<i class='bi bi-check-all'></i>";

    // Kosongkan daftar pilihan
    pilihKriteriaList.innerHTML = "";
    pilihSubKriteriaList.innerHTML = "";

    // Sembunyikan kontainer pilihan dan reset dropdown metode bobot
    pilihanContainer.classList.add("d-none");
    metodeBobotSelect.value = "";

    // Mengaktifkan kembali tombol "Pilih Semua Kriteria" dan "Lanjut Pilih Bobot"
    pilihSemuaKriteriaBtn.disabled = false;
    lanjutPilihBobotBtn.disabled = true;

    // Perbarui ikon dan status tombol "Lanjut Pilih Bobot"
    updateLanjutPilihBobotStatus();
  }

  // Event listener untuk tombol "Pilih Semua Kriteria"
  pilihSemuaKriteriaBtn.addEventListener("click", toggleAllKriteria);

  // Event listener untuk tombol "Reset Pilihan"
  resetPilihanBtn.addEventListener("click", resetSelections);

  // Event listener untuk setiap checkbox kriteria untuk mengelola subkriteria
  checkItemKriteria.forEach((kriteriaCheckbox) => {
    kriteriaCheckbox.addEventListener("change", () => {
      toggleSubkriteria(kriteriaCheckbox);
    });
  });

  // Event listener untuk tombol "Lanjut Pilih Bobot"
  lanjutPilihBobotBtn.addEventListener("click", () => {
    if (checkKriteriaCount()) {
      pilihanContainer.classList.remove("d-none");
      updatePilihanLists();
      metodeBobotSelect.value = "";

      // Nonaktifkan tombol dan checkbox setelah tombol diklik
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

  // Perbarui status tombol "Lanjut Pilih Bobot" saat halaman dimuat
  updateLanjutPilihBobotStatus();

  function updateTables() {
    // Tampilkan div hasilPembobotanContainer
    hasilPembobotanContainer.classList.remove("d-none");

    // Kosongkan tabel tanpa mengubah judul (header)
    const tabelKriteria = document.querySelector("#tabelKriteria tbody");
    const tabelSubkriteria = document.querySelector("#tabelSubkriteria tbody");
    const tabelGabungan = document.querySelector("#tabelGabungan tbody");
    const totalBobotKriteriaElement = document.querySelector(
      "#totalBobotKriteria"
    );
    const totalBobotSubkriteriaElement = document.querySelector(
      "#totalBobotSubkriteria"
    );
    const totalBobotGabunganElement = document.querySelector(
      "#totalBobotGabungan"
    );

    tabelKriteria.innerHTML = "";
    tabelSubkriteria.innerHTML = "";
    tabelGabungan.innerHTML = "";

    // Nomor urut untuk tabel gabungan
    let gabunganIndex = 1;

    // Hitung jumlah kriteria yang terpilih
    const jumlahKriteria = Array.from(checkItemKriteria).filter(
      (kriteria) => kriteria.checked
    ).length;

    // Inisialisasi total bobot
    let totalBobotKriteria = 0;
    let totalBobotSubkriteria = 0;
    let totalBobotGabungan = 0;

    // Menghitung bobot untuk kriteria dan menyimpan subkriteria untuk setiap kriteria
    let kriteriaIndex = 1;
    checkItemKriteria.forEach((kriteria) => {
      if (kriteria.checked) {
        // Tambah ke tabel Kriteria
        const bobotKriteria = (100 / jumlahKriteria).toFixed(2); // Hitung bobot kriteria
        const row = tabelKriteria.insertRow();
        row.innerHTML = `<td>${kriteriaIndex}</td><td>${kriteria.value}</td><td>${bobotKriteria}</td>`;
        totalBobotKriteria += parseFloat(bobotKriteria); // Tambahkan bobot ke total
        kriteriaIndex++;

        // Cek jumlah subkriteria untuk kriteria ini
        const subkriteriaTerkait = Array.from(checkItemSubkriteria).filter(
          (subkriteria) =>
            subkriteria.dataset.idKriteria === kriteria.dataset.id &&
            subkriteria.checked
        );
        const jumlahSubkriteria = subkriteriaTerkait.length;

        // Tambahkan ke tabel Gabungan jika tidak memiliki subkriteria
        if (jumlahSubkriteria === 0) {
          const gabunganRow = tabelGabungan.insertRow();
          gabunganRow.innerHTML = `<td>${gabunganIndex}</td><td>${kriteria.value}</td><td>${kriteria.dataset.tipe_kriteria}</td><td>${bobotKriteria}</td>`;
          totalBobotGabungan += parseFloat(bobotKriteria); // Tambahkan bobot ke total
          gabunganIndex++;
        } else {
          // Hitung bobot untuk setiap subkriteria
          const bobotSubkriteria = (100 / jumlahSubkriteria).toFixed(2);
          let subkriteriaIndex = 1;

          // Reset total bobot subkriteria untuk kriteria ini
          let totalBobotSubkriteriaPerKriteria = 0;

          subkriteriaTerkait.forEach((subkriteria) => {
            // Tambah ke tabel Subkriteria
            const rowSubkriteria = tabelSubkriteria.insertRow();
            rowSubkriteria.innerHTML = `<td>${subkriteriaIndex}</td><td>${subkriteria.value}</td><td>${bobotSubkriteria}</td>`;
            totalBobotSubkriteria += parseFloat(bobotSubkriteria); // Tambahkan bobot ke total subkriteria umum
            totalBobotSubkriteriaPerKriteria += parseFloat(bobotSubkriteria); // Tambahkan bobot ke total subkriteria per kriteria
            subkriteriaIndex++;

            // Tambah ke tabel Gabungan dengan rumus bobot subkriteria dikali bobot kriteria
            const bobotGabungan = (
              bobotSubkriteria *
              (bobotKriteria / 100)
            ).toFixed(2);
            const gabunganRow = tabelGabungan.insertRow();
            gabunganRow.innerHTML = `<td>${gabunganIndex}</td><td>${subkriteria.value}</td><td>${subkriteria.dataset.tipe_subkriteria}</td><td>${bobotGabungan}</td>`;
            totalBobotGabungan += parseFloat(bobotGabungan); // Tambahkan bobot ke total
            gabunganIndex++;
          });

          // Tambahkan baris total bobot subkriteria untuk kriteria ini
          const rowTotalSubkriteria = tabelSubkriteria.insertRow();
          rowTotalSubkriteria.innerHTML = `<td colspan="2">Total Bobot Subkriteria: ${totalBobotSubkriteriaPerKriteria.toFixed(
            2
          )}</td><td></td>`;
        }
      }
    });

    // Tampilkan total bobot
    totalBobotKriteriaElement.innerText = totalBobotKriteria.toFixed(2);
    totalBobotSubkriteriaElement.innerText = totalBobotSubkriteria.toFixed(2);
    totalBobotGabunganElement.innerText = totalBobotGabungan.toFixed(2);
  }

  // Fungsi untuk mereset tabel ketika tombol reset atau dropdown diubah
  function resetTables() {
    document.querySelector("#tabelKriteria tbody").innerHTML = "";
    document.querySelector("#tabelSubkriteria tbody").innerHTML = "";
    document.querySelector("#tabelGabungan tbody").innerHTML = "";

    // Sembunyikan div hasilPembobotanContainer
    hasilPembobotanContainer.classList.add("d-none");

    // Reset total bobot
    document.querySelector("#totalBobotKriteria").innerText = `0`;
    document.querySelector("#totalBobotSubkriteria").innerText = `0`;
    document.querySelector("#totalBobotGabungan").innerText = `0`;
  }

  // Event listener untuk perubahan pada dropdown metodeBobot
  metodeBobotSelect.addEventListener("change", function () {
    if (metodeBobotSelect.value === "1") {
      updateTables();
    } else {
      resetTables();
    }
  });

  // Event listener untuk tombol resetPilihan
  resetPilihanBtn.addEventListener("click", resetTables);
});

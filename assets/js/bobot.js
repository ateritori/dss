document.addEventListener("DOMContentLoaded", function() {
    const checkboxes = document.querySelectorAll(".checkItemKriteria");
    const pilihSemuaBtn = document.getElementById("pilihsemua");
    const prosesBtn = document.getElementById("proses");
    const metodebobotDiv = document.getElementById("metodebobot");
    const modelBobotSelect = document.querySelector("select[name='modelbobot']");
    const resultList = document.getElementById("resultListKriteria");
    const pilihankriteria = [];
    let allChecked = false;

    // Nonaktifkan tombol Pilih Semua di awal
    pilihSemuaBtn.disabled = false;

    // Button untuk reset pemilihan kriteria
    const pilihUlangBtn = document.createElement("button");
    pilihUlangBtn.id = "pilihUlang";
    pilihUlangBtn.className = "btn btn-secondary mt-3";
    pilihUlangBtn.textContent = "Pilih Ulang Kriteria";
    pilihUlangBtn.classList.add("d-none"); // Hide by default

    // Fungsi untuk memperbarui kriteria yang dipilih
    function updateSelectedKriteria() {
        resultList.innerHTML = ""; // Reset the list
        pilihankriteria.length = 0; // Clear the array

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                pilihankriteria.push({
                    id: checkbox.getAttribute("data-id"),
                    nama: checkbox.value
                });
                const li = document.createElement("li");
                li.textContent = checkbox.value;
                resultList.appendChild(li);
            }
        });

        // Show or hide the selected criteria section based on selection
        document.getElementById("pilihankriteria").classList.toggle("d-none", pilihankriteria.length === 0);
    }

    // Event listener tombol Proses
    prosesBtn.addEventListener("click", function() {
        if (pilihankriteria.length < 3) {
            alert("Pembobotan kriteria hanya bisa dilakukan minimal 3 kriteria.");
            return; // Stop if not enough criteria
        }
        metodebobotDiv.classList.remove("d-none");
        pilihUlangBtn.classList.remove("d-none");

        // Nonaktifkan semua checkbox dan tombol
        checkboxes.forEach(checkbox => {
            checkbox.disabled = true; // Disable all checkboxes
        });
        pilihSemuaBtn.disabled = true; // Disable 'Pilih Semua' button
    });

    // Event listener tombol Pilih Semua
    pilihSemuaBtn.addEventListener("click", function() {
        allChecked = !allChecked; // Toggle selection state
        checkboxes.forEach(checkbox => {
            checkbox.checked = allChecked; // Check/uncheck all
        });
        updateSelectedKriteria();

        // Update button icon based on current state
        pilihSemuaBtn.innerHTML = allChecked ? '<i class="bi bi-x-square"></i>' : '<i class="bi bi-check-square"></i>';
    });

    // Event listener untuk checkbox individual
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            updateSelectedKriteria();

            // Update allChecked status
            allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            pilihSemuaBtn.innerHTML = allChecked ? '<i class="bi bi-x-square"></i>' : '<i class="bi bi-check-square"></i>';
        });
    });

    modelBobotSelect.addEventListener("change", function() {
        if (this.value === "1") {
            if (pilihankriteria.length >= 3) {
                loadAHPForm(pilihankriteria);
                metodebobotDiv.appendChild(pilihUlangBtn);
            } else {
                alert("Pembobotan kriteria hanya bisa dilakukan minimal 3 kriteria.");
                this.value = ""; // Reset dropdown
                resetChecklist(); // Reset checkboxes
            }
        } else {
            clearAHPForm();
        }
    });

    pilihUlangBtn.addEventListener("click", function() {
        resetChecklist();
        modelBobotSelect.value = ""; // Reset dropdown
        updateSelectedKriteria();
        clearAHPForm();
        pilihUlangBtn.classList.add("d-none"); // Hide the button
        metodebobotDiv.classList.add("d-none"); // Hide the method selection
    });

    // Fungsi reset checklist
    function resetChecklist() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false; // Uncheck all checkboxes
            checkbox.disabled = false; // Enable checkboxes for new selection
        });
        allChecked = false;
        pilihSemuaBtn.innerHTML = '<i class="bi bi-check-square"></i>'; // Reset button icon
        pilihSemuaBtn.disabled = false; // Enable 'Pilih Semua' button
    }

    function loadAHPForm(kriteriaList) {
        clearAHPForm(); 

        const ahpFormContainer = document.createElement("div");
        ahpFormContainer.className = "ahp-form-container mt-3";

        for (let i = 0; i < kriteriaList.length; i++) {
            for (let j = i + 1; j < kriteriaList.length; j++) {
                const pairContainer = document.createElement("div");
                pairContainer.className = "form-group row align-items-center";

                const labelLeft = document.createElement("label");
                labelLeft.className = "col-sm-3 col-form-label text-right";
                labelLeft.textContent = kriteriaList[i].nama;

                const inputContainer = document.createElement("div");
                inputContainer.className = "col-sm-6";

                const input = document.createElement("input");
                input.type = "range";
                input.min = "1";
                input.max = "9";
                input.value = "5";
                input.className = "form-range";
                input.dataset.kriteria1 = kriteriaList[i].id;
                input.dataset.kriteria2 = kriteriaList[j].id;

                inputContainer.appendChild(input);

                const labelRight = document.createElement("label");
                labelRight.className = "col-sm-3 col-form-label text-left";
                labelRight.textContent = kriteriaList[j].nama;

                pairContainer.appendChild(labelLeft);
                pairContainer.appendChild(inputContainer);
                pairContainer.appendChild(labelRight);

                ahpFormContainer.appendChild(pairContainer);
            }
        }
        metodebobotDiv.appendChild(ahpFormContainer);
    }

    function clearAHPForm() {
        const existingForm = document.querySelector(".ahp-form-container");
        if (existingForm) {
            existingForm.remove();
        }
    }
});
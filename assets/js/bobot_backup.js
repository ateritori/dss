document.addEventListener("DOMContentLoaded", function() {
    const checkboxes = document.querySelectorAll(".checkItemKriteria");
    const pilihSemuaBtn = document.getElementById("pilihsemua");
    const prosesBtn = document.getElementById("proses");
    const metodebobotDiv = document.getElementById("metodebobot");
    const modelBobotSelect = document.querySelector("select[name='modelbobot']");
    const resultList = document.getElementById("resultListKriteria");
    const ahpContainer = document.getElementById("ahpContainer");
    const pilihankriteria = [];
    let allChecked = false;

    pilihSemuaBtn.disabled = false;

    // Membuat kontainer untuk tombol
    const buttonContainer = document.createElement("div");
    buttonContainer.className = "mt-3"; // Menambahkan margin atas

    // Tombol pilih ulang
    const pilihUlangBtn = document.createElement("button");
    pilihUlangBtn.id = "pilihUlang";
    pilihUlangBtn.className = "btn btn-secondary me-2 d-none"; // Sembunyikan dari awal
    pilihUlangBtn.textContent = "Pilih Ulang Kriteria";

    // Tombol simpan perbandingan
    const simpanPerbandinganBtn = document.createElement("button");
    simpanPerbandinganBtn.id = "simpanPerbandingan";
    simpanPerbandinganBtn.className = "btn btn-primary d-none"; // Sembunyikan dari awal
    simpanPerbandinganBtn.textContent = "Simpan Perbandingan";

    buttonContainer.appendChild(pilihUlangBtn);
    buttonContainer.appendChild(simpanPerbandinganBtn);

    ahpContainer.appendChild(buttonContainer); // Menambahkan container tombol ke ahpContainer

    function updateSelectedKriteria() {
        resultList.innerHTML = "";
        pilihankriteria.length = 0;

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

        document.getElementById("pilihankriteria").classList.toggle("d-none", pilihankriteria.length === 0);
    }

    prosesBtn.addEventListener("click", function() {
        metodebobotDiv.classList.remove("d-none");

        checkboxes.forEach(checkbox => checkbox.disabled = true);
        pilihSemuaBtn.disabled = true;
    });

    pilihSemuaBtn.addEventListener("click", function() {
        allChecked = !allChecked;
        checkboxes.forEach(checkbox => checkbox.checked = allChecked);
        updateSelectedKriteria();

        pilihSemuaBtn.innerHTML = allChecked ? '<i class="bi bi-x-square"></i>' : '<i class="bi bi-check-square"></i>';
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            updateSelectedKriteria();

            allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            pilihSemuaBtn.innerHTML = allChecked ? '<i class="bi bi-x-square"></i>' : '<i class="bi bi-check-square"></i>';
        });
    });

    modelBobotSelect.addEventListener("change", function() {
        if (this.value === "1") {
            if (pilihankriteria.length >= 3) {
                loadAHPForm(pilihankriteria);

                // Tampilkan tombol pilih ulang dan simpan perbandingan
                pilihUlangBtn.classList.remove("d-none");
                simpanPerbandinganBtn.classList.remove("d-none");
            } else {
                alert("Metode Perbandingan Kriteria Hanya Bisa Dilakukan dengan Memilih Minimal 3 Kriteria.");
                this.value = "";
                resetChecklist();
                clearAHPForm();
                document.getElementById("pilihankriteria").classList.add("d-none");
                metodebobotDiv.classList.add("d-none");
            }
        } else {
            clearAHPForm();
            // Sembunyikan tombol jika metode bukan "1"
            pilihUlangBtn.classList.add("d-none");
            simpanPerbandinganBtn.classList.add("d-none");
        }
    });

    pilihUlangBtn.addEventListener("click", function() {
        resetChecklist();
        modelBobotSelect.value = "";
        updateSelectedKriteria();
        clearAHPForm();
        pilihUlangBtn.classList.add("d-none");
        metodebobotDiv.classList.add("d-none");
        simpanPerbandinganBtn.classList.add("d-none"); // Sembunyikan tombol saat memilih ulang
    });

    simpanPerbandinganBtn.addEventListener("click", function() {
        const inputRanges = document.querySelectorAll('input[type="range"]');
        const comparisons = [];

        inputRanges.forEach(input => {
            const kriteria1 = input.dataset.kriteria1;
            const kriteria2 = input.dataset.kriteria2;
            const value = parseInt(input.value, 10);
            comparisons.push({ kriteria1, kriteria2, value });
        });

        const consistencyResult = checkConsistency(comparisons);
        const matrices = createMatrices(comparisons);

        // Membuat kontainer untuk hasil
        const resultContainer = document.createElement("div");
        resultContainer.id = "resultContainer"; // ID untuk kontainer hasil
        resultContainer.className = "result-container mt-3"; // Tambahkan margin atas

        ahpContainer.appendChild(resultContainer); // Tambahkan kontainer hasil ke ahpContainer

        // Menampilkan hasil
        displayResults(resultContainer, matrices.kriteriaMatrix, matrices.normalizedMatrix, consistencyResult);
    });

    function resetChecklist() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });
        allChecked = false;
        pilihSemuaBtn.innerHTML = '<i class="bi bi-check-square"></i>';
        pilihSemuaBtn.disabled = false;
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
    
                input.style.width = "100%";
    
                inputContainer.appendChild(input);
    
                const scaleContainer = document.createElement("div");
                scaleContainer.className = "scale-container";
                scaleContainer.style.position = "relative";
    
                for (let scaleValue = 1; scaleValue <= 9; scaleValue++) {
                    const scaleLabel = document.createElement("span");
                    scaleLabel.textContent = scaleValue;
                    scaleLabel.className = "scale-label";
                    scaleLabel.style.position = "absolute";
                    scaleLabel.style.left = `${(scaleValue - 1) * (100 / 8)}%`;
                    scaleLabel.style.transform = 'translateX(-50%)';
                    scaleContainer.appendChild(scaleLabel);
                }
    
                inputContainer.appendChild(scaleContainer);
                pairContainer.appendChild(labelLeft);
                pairContainer.appendChild(inputContainer);
                pairContainer.appendChild(document.createElement("label").appendChild(document.createTextNode(kriteriaList[j].nama)));
    
                // Menambahkan pairContainer ke ahpFormContainer
                ahpFormContainer.appendChild(pairContainer);
            }
        }
    
        // Pindahkan buttonContainer di sini, di luar loop
        const buttonContainer = document.createElement("div");
        buttonContainer.className = "mt-3"; // Menambahkan margin atas
    
        const pilihUlangBtn = document.createElement("button");
        pilihUlangBtn.id = "pilihUlang";
        pilihUlangBtn.className = "btn btn-secondary me-2 d-none"; // Sembunyikan dari awal
        pilihUlangBtn.textContent = "Pilih Ulang Kriteria";
    
        const simpanPerbandinganBtn = document.createElement("button");
        simpanPerbandinganBtn.id = "simpanPerbandingan";
        simpanPerbandinganBtn.className = "btn btn-primary d-none"; // Sembunyikan dari awal
        simpanPerbandinganBtn.textContent = "Simpan Perbandingan";
    
        buttonContainer.appendChild(pilihUlangBtn);
        buttonContainer.appendChild(simpanPerbandinganBtn);
    
        // Menambahkan tombol ke bawah semua pairContainer
        ahpFormContainer.appendChild(buttonContainer);
        ahpContainer.appendChild(ahpFormContainer); // Tambahkan AHP form container ke ahpContainer
    }

    function clearAHPForm() {
        const ahpFormContainer = document.querySelector(".ahp-form-container");
        if (ahpFormContainer) {
            ahpFormContainer.remove();
        }
    }

    function checkConsistency(comparisons) {
        // Implement consistency check logic
        return true; // Placeholder for consistency check result
    }

    function createMatrices(comparisons) {
        const size = Math.max(...comparisons.map(c => Math.max(c.kriteria1, c.kriteria2))) + 1;
        const kriteriaMatrix = Array.from({ length: size }, () => Array(size).fill(1));

        comparisons.forEach(({ kriteria1, kriteria2, value }) => {
            kriteriaMatrix[kriteria1][kriteria2] = value;
            kriteriaMatrix[kriteria2][kriteria1] = 1 / value;
        });

        const normalizedMatrix = kriteriaMatrix.map(row => {
            const rowSum = row.reduce((acc, val) => acc + val, 0);
            return row.map(val => (val / rowSum).toFixed(2)); // Normalisasi
        });

        return { kriteriaMatrix, normalizedMatrix };
    }

    function displayResults(resultContainer, kriteriaMatrix, normalizedMatrix, consistencyResult) {
        resultContainer.innerHTML = ""; // Kosongkan kontainer sebelum menampilkan hasil

        // Menampilkan matriks kriteria
        const kriteriaMatrixHeader = document.createElement("h5");
        kriteriaMatrixHeader.textContent = "Matriks Kriteria";
        resultContainer.appendChild(kriteriaMatrixHeader);
        const kriteriaMatrixTable = document.createElement("table");
        kriteriaMatrixTable.className = "table"; // Tambahkan kelas untuk styling
        kriteriaMatrix.forEach(row => {
            const tableRow = document.createElement("tr");
            row.forEach(value => {
                const tableData = document.createElement("td");
                tableData.textContent = value;
                tableRow.appendChild(tableData);
            });
            kriteriaMatrixTable.appendChild(tableRow);
        });
        resultContainer.appendChild(kriteriaMatrixTable);

        // Menampilkan matriks normalisasi
        const normalizedMatrixHeader = document.createElement("h5");
        normalizedMatrixHeader.textContent = "Matriks Normalisasi";
        resultContainer.appendChild(normalizedMatrixHeader);
        const normalizedMatrixTable = document.createElement("table");
        normalizedMatrixTable.className = "table"; // Tambahkan kelas untuk styling
        normalizedMatrix.forEach(row => {
            const tableRow = document.createElement("tr");
            row.forEach(value => {
                const tableData = document.createElement("td");
                tableData.textContent = value;
                tableRow.appendChild(tableData);
            });
            normalizedMatrixTable.appendChild(tableRow);
        });
        resultContainer.appendChild(normalizedMatrixTable);

        // Tampilkan hasil konsistensi
        const consistencyHeader = document.createElement("h5");
        consistencyHeader.textContent = "Hasil Konsistensi: " + (consistencyResult ? "Valid" : "Tidak Valid");
        resultContainer.appendChild(consistencyHeader);
    }
});
<?php
session_start();
if (!isset($_SESSION['username'])) {
?>
    <script type="text/javascript">
        alert('Anda Belum Login');
        window.location = 'index.php';
    </script>
<?php
}
// Koneksi ke database
require 'config/koneksi.php';

// Ambil data alternatif yang sudah dinilai
$queryAlternatif = "SELECT DISTINCT a.id_alternatif, a.nama_alternatif
                    FROM Alternatif a
                    JOIN Penilaian p ON a.id_alternatif = p.id_alternatif
                    WHERE a.status_alternatif = '1'";
$resultAlternatif = $conn->query($queryAlternatif);
$alternatif = [];
while ($row = $resultAlternatif->fetch_assoc()) {
    $alternatif[$row['id_alternatif']] = $row['nama_alternatif'];
}

// Ambil data kriteria dan subkriteria (termasuk tipe dan bobot)
$queryKriteria = "SELECT * FROM Kriteria";
$resultKriteria = $conn->query($queryKriteria);
$kriteria = [];
while ($row = $resultKriteria->fetch_assoc()) {
    $kriteria[$row['id_kriteria']] = [
        'nama' => $row['nama_kriteria'],
        'bobot' => $row['bobot_kriteria'],
        'tipe' => $row['tipe_kriteria'], // benefit atau cost
        'punyasub' => $row['punyasub']
    ];
}

// Ambil data subkriteria (jika ada)
$querySubKriteria = "SELECT * FROM SubKriteria";
$resultSubKriteria = $conn->query($querySubKriteria);
$subkriteria = [];
while ($row = $resultSubKriteria->fetch_assoc()) {
    $subkriteria[$row['id_subkriteria']] = [
        'id_kriteria' => $row['id_kriteria'],
        'nama' => $row['nama_subkriteria'],
        'bobot' => $row['bobot_subkriteria'],
        'tipe' => $row['tipe_subkriteria'] // benefit atau cost
    ];
}

// Ambil data penilaian
$queryPenilaian = "SELECT * FROM Penilaian";
$resultPenilaian = $conn->query($queryPenilaian);
$penilaian = [];
while ($row = $resultPenilaian->fetch_assoc()) {
    $penilaian[] = [
        'id_alternatif' => $row['id_alternatif'],
        'id_kriteria' => $row['id_kriteria'],
        'id_subkriteria' => $row['id_subkriteria'],
        'nilai' => $row['nilai']
    ];
}

// Step 1: Hitung akar kuadrat jumlah kuadrat nilai untuk setiap kombinasi kriteria/subkriteria
$sqrtSum = [];
foreach ($penilaian as $data) {
    $idKriteria = $data['id_kriteria'];
    $idSubKriteria = $data['id_subkriteria'];
    $key = $idKriteria . '-' . ($idSubKriteria ?? '0'); // Gunakan kombinasi id_kriteria dan id_subkriteria

    // Abaikan kriteria utama yang memiliki subkriteria
    if ($kriteria[$idKriteria]['punyasub'] == '1' && !isset($subkriteria[$idSubKriteria])) {
        continue; // Lewati kriteria utama jika ada subkriteria
    }

    // Hitung jumlah kuadrat nilai
    if (!isset($sqrtSum[$key])) {
        $sqrtSum[$key] = 0;
    }
    $sqrtSum[$key] += pow($data['nilai'], 2);
}

// Ambil akar kuadrat dari jumlah kuadrat nilai
foreach ($sqrtSum as $key => $value) {
    $sqrtSum[$key] = sqrt($value);
}

// Step 2: Proses normalisasi menggunakan metode vektor
$normalisasi = [];
foreach ($penilaian as $data) {
    $idKriteria = $data['id_kriteria'];
    $idSubKriteria = $data['id_subkriteria'];
    $nilai = $data['nilai'];
    $key = $idKriteria . '-' . ($idSubKriteria ?? '0'); // Kombinasi unik id_kriteria dan id_subkriteria

    // Abaikan kriteria utama yang memiliki subkriteria
    if ($kriteria[$idKriteria]['punyasub'] == '1' && !isset($subkriteria[$idSubKriteria])) {
        continue; // Lewati normalisasi untuk kriteria utama jika ada subkriteria
    }

    // Normalisasi nilai menggunakan metode vektor: nilai / sqrt(jumlah kuadrat)
    $nilaiNormalisasi = $nilai / $sqrtSum[$key];

    // Masukkan hasil normalisasi ke array
    $normalisasi[] = [
        'id_alternatif' => $data['id_alternatif'],
        'id_kriteria' => $idKriteria,
        'id_subkriteria' => $idSubKriteria,
        'nilai' => $nilaiNormalisasi
    ];
}

// Step 3: Hitung solusi ideal positif dan negatif
$solusiIdealPositif = [];
$solusiIdealNegatif = [];

// Inisialisasi solusi ideal dengan nilai ekstrem
foreach ($kriteria as $id_kriteria => $k) {
    if ($k['tipe'] === 'benefit') {
        $solusiIdealPositif[$id_kriteria] = PHP_FLOAT_MIN; // Nilai maksimum untuk benefit
        $solusiIdealNegatif[$id_kriteria] = PHP_FLOAT_MAX; // Nilai minimum untuk benefit
    } else {
        $solusiIdealPositif[$id_kriteria] = PHP_FLOAT_MAX; // Nilai minimum untuk cost
        $solusiIdealNegatif[$id_kriteria] = PHP_FLOAT_MIN; // Nilai maksimum untuk cost
    }
}

// Proses untuk menghitung solusi ideal
foreach ($normalisasi as $n) {
    $idKriteria = $n['id_kriteria'];
    $nilai = $n['nilai'];

    if ($kriteria[$idKriteria]['tipe'] === 'benefit') {
        // Kriteria Benefit
        if ($nilai > $solusiIdealPositif[$idKriteria]) {
            $solusiIdealPositif[$idKriteria] = $nilai;
        }
        if ($nilai < $solusiIdealNegatif[$idKriteria]) {
            $solusiIdealNegatif[$idKriteria] = $nilai;
        }
    } else {
        // Kriteria Cost
        if ($nilai < $solusiIdealPositif[$idKriteria]) {
            $solusiIdealPositif[$idKriteria] = $nilai;
        }
        if ($nilai > $solusiIdealNegatif[$idKriteria]) {
            $solusiIdealNegatif[$idKriteria] = $nilai;
        }
    }
}

// Inisialisasi array untuk menyimpan jarak
$jarakIdealPositif = [];
$jarakIdealNegatif = [];

// Hitung jarak untuk setiap alternatif
foreach ($alternatif as $id_alternatif => $nama_alternatif) {
    $jarakPositif = 0;
    $jarakNegatif = 0;

    foreach ($kriteria as $id_kriteria => $k) {
        // Cek jika kriteria memiliki subkriteria
        if ($k['punyasub'] == '1') {
            foreach ($subkriteria as $id_subkriteria => $sub) {
                if ($sub['id_kriteria'] == $id_kriteria) {
                    // Hitung jarak ke solusi ideal positif
                    $jarakPositif += pow($normalisasi[$id_alternatif][$id_kriteria][$id_subkriteria] - $solusiIdealPositif[$id_kriteria], 2);
                    // Hitung jarak ke solusi ideal negatif
                    $jarakNegatif += pow($normalisasi[$id_alternatif][$id_kriteria][$id_subkriteria] - $solusiIdealNegatif[$id_kriteria], 2);
                }
            }
        } else {
            // Hitung jarak ke solusi ideal positif
            $jarakPositif += pow($normalisasi[$id_alternatif][$id_kriteria][0] - $solusiIdealPositif[$id_kriteria], 2);
            // Hitung jarak ke solusi ideal negatif
            $jarakNegatif += pow($normalisasi[$id_alternatif][$id_kriteria][0] - $solusiIdealNegatif[$id_kriteria], 2);
        }
    }

    // Ambil akar kuadrat dari jumlah jarak
    $jarakIdealPositif[$id_alternatif] = sqrt($jarakPositif);
    $jarakIdealNegatif[$id_alternatif] = sqrt($jarakNegatif);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>HOME PAGE</title>
    <style>
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
        }

        th,
        td {
            text-align: left;
            padding: 16px;
        }

        body {
            font-family: "Verdana";
        }

        .navbar {
            width: 100%;
            background: orange;
            overflow: auto;
            color: white;
        }

        .btn-edit {
            background-color: #167395;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <br>
    <div class="card shadow">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Solusi Ideal Positif</div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                echo "<table border='1'>
                        <tr>
                            <th>Kriteria</th>
                            <th>Subkriteria</th>
                            <th>Nilai</th>
                        </tr>";
                foreach ($kriteria as $id_kriteria => $k) {
                    // Jika kriteria memiliki subkriteria
                    if ($k['punyasub'] == '1') {
                        // Tampilkan semua subkriteria
                        foreach ($subkriteria as $id_subkriteria => $sub) {
                            if ($sub['id_kriteria'] == $id_kriteria) {
                                echo "<tr>
                                        <td>{$k['nama']}</td>
                                        <td>{$sub['nama']}</td>
                                        <td>" . round($solusiIdealPositif[$id_kriteria], 4) . "</td>
                                      </tr>";
                            }
                        }
                    } else {
                        // Jika tidak ada subkriteria, tampilkan kriteria utama
                        echo "<tr>
                                <td>{$k['nama']}</td>
                                <td>-</td>
                                <td>" . round($solusiIdealPositif[$id_kriteria], 4) . "</td>
                              </tr>";
                    }
                }
                echo "</table>";
                ?>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Solusi Ideal Negatif</div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                echo "<table border='1'>
                                        <tr>
                                            <th>Kriteria</th>
                                            <th>Subkriteria</th>
                                            <th>Nilai</th>
                                        </tr>";

                foreach ($kriteria as $id_kriteria => $k) {
                    // Jika kriteria memiliki subkriteria
                    if ($k['punyasub'] == '1') {
                        // Tampilkan semua subkriteria
                        foreach ($subkriteria as $id_subkriteria => $sub) {
                            if ($sub['id_kriteria'] == $id_kriteria) {
                                echo "<tr>
                                                        <td>{$k['nama']}</td>
                                                        <td>{$sub['nama']}</td>
                                                        <td>" . round($solusiIdealNegatif[$id_kriteria], 4) . "</td>
                                                      </tr>";
                            }
                        }
                    } else {
                        // Jika tidak ada subkriteria, tampilkan kriteria utama
                        echo "<tr>
                                                <td>{$k['nama']}</td>
                                                <td>-</td>
                                                <td>" . round($solusiIdealNegatif[$id_kriteria], 4) . "</td>
                                              </tr>";
                    }
                }
                echo "</table>";
                // Tampilkan jarak ke solusi ideal positif
                ?>

            </div>
        </div>
    </div>
</body>
<?php
$conn->close();
?>
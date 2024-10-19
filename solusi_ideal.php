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

// Step 3: Membentuk Matriks Keputusan Ternormalisasi
$keputusanTernormalisasi = [];
foreach ($normalisasi as $data) {
    $idKriteria = $data['id_kriteria'];
    $idSubKriteria = $data['id_subkriteria'];
    $bobotKriteria = $kriteria[$idKriteria]['bobot'];

    // Hitung nilai keputusan ternormalisasi
    if ($idSubKriteria) { // Jika ada subkriteria
        $bobotSubkriteria = $subkriteria[$idSubKriteria]['bobot'];
        $nilaiTernormalisasi = $data['nilai'] * $bobotSubkriteria; // Gunakan bobot subkriteria
    } else { // Jika tidak ada subkriteria
        $nilaiTernormalisasi = $data['nilai'] * $bobotKriteria; // Gunakan bobot kriteria
    }

    // Masukkan hasil keputusan ternormalisasi ke dalam array
    $keputusanTernormalisasi[] = [
        'id_alternatif' => $data['id_alternatif'],
        'id_kriteria' => $idKriteria,
        'id_subkriteria' => $idSubKriteria,
        'nilai_normalisasi' => $data['nilai'],
        'bobot' => $idSubKriteria ? $bobotSubkriteria : $bobotKriteria,
        'nilai' => $nilaiTernormalisasi
    ];
}

// Step 4: Hitung Solusi Ideal Positif dan Negatif
$solusiIdeal = [
    'positif' => [],
    'negatif' => []
];

// Menghitung solusi ideal positif dan negatif
foreach ($kriteria as $id_kriteria => $k) {
    if ($k['punyasub'] == '0') { // Kriteria tanpa subkriteria
        $nilai = [];
        foreach ($keputusanTernormalisasi as $kt) {
            if ($kt['id_kriteria'] == $id_kriteria) {
                $nilai[] = $kt['nilai'];
            }
        }

        // Tentukan solusi ideal berdasarkan tipe kriteria
        if (!empty($nilai)) {
            if ($k['tipe'] == 'benefit') {
                $solusiIdeal['positif'][$id_kriteria] = max($nilai);
                $solusiIdeal['negatif'][$id_kriteria] = min($nilai);
            } elseif ($k['tipe'] == 'cost') {
                $solusiIdeal['positif'][$id_kriteria] = min($nilai);
                $solusiIdeal['negatif'][$id_kriteria] = max($nilai);
            }
        }
    } elseif ($k['punyasub'] == '1') { // Kriteria dengan subkriteria
        foreach ($subkriteria as $id_subkriteria => $sub) {
            if ($sub['id_kriteria'] == $id_kriteria) {
                $nilai = [];
                foreach ($keputusanTernormalisasi as $kt) {
                    if ($kt['id_kriteria'] == $id_kriteria && $kt['id_subkriteria'] == $id_subkriteria) {
                        $nilai[] = $kt['nilai'];
                    }
                }

                // Tentukan solusi ideal berdasarkan tipe subkriteria
                if (!empty($nilai)) {
                    if ($sub['tipe'] == 'benefit') {
                        $solusiIdeal['positif'][$id_subkriteria] = max($nilai);
                        $solusiIdeal['negatif'][$id_subkriteria] = min($nilai);
                    } elseif ($sub['tipe'] == 'cost') {
                        $solusiIdeal['positif'][$id_subkriteria] = min($nilai);
                        $solusiIdeal['negatif'][$id_subkriteria] = max($nilai);
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Solusi Ideal</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Solusi Ideal Positif</h2>
    <table>
        <thead>
            <tr>
                <th>Kriteria/Subkriteria</th>
                <th>Solusi Ideal Positif</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Menampilkan solusi ideal positif untuk kriteria tanpa subkriteria
            foreach ($solusiIdeal['positif'] as $id_kriteria => $nilai) {
                if (isset($kriteria[$id_kriteria]) && $kriteria[$id_kriteria]['punyasub'] == '0') {
                    $namaKriteria = $kriteria[$id_kriteria]['nama'];
                    echo "<tr>";
                    echo "<td>{$namaKriteria}</td>";
                    echo "<td>" . number_format($nilai, 4) . "</td>";
                    echo "</tr>";
                }
            }

            // Menampilkan subkriteria untuk kriteria yang memiliki subkriteria
            foreach ($subkriteria as $id_subkriteria => $sub) {
                if (isset($solusiIdeal['positif'][$id_subkriteria])) {
                    $nilai = $solusiIdeal['positif'][$id_subkriteria];
                    echo "<tr>";
                    echo "<td>{$sub['nama']}</td>";
                    echo "<td>" . number_format($nilai, 4) . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>

    <h2>Solusi Ideal Negatif</h2>
    <table>
        <thead>
            <tr>
                <th>Kriteria/Subkriteria</th>
                <th>Solusi Ideal Negatif</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Menampilkan solusi ideal negatif untuk kriteria tanpa subkriteria
            foreach ($solusiIdeal['negatif'] as $id_kriteria => $nilai) {
                if (isset($kriteria[$id_kriteria]) && $kriteria[$id_kriteria]['punyasub'] == '0') {
                    $namaKriteria = $kriteria[$id_kriteria]['nama'];
                    echo "<tr>";
                    echo "<td>{$namaKriteria}</td>";
                    echo "<td>" . number_format($nilai, 4) . "</td>";
                    echo "</tr>";
                }
            }

            // Menampilkan subkriteria untuk kriteria yang memiliki subkriteria
            foreach ($subkriteria as $id_subkriteria => $sub) {
                if (isset($solusiIdeal['negatif'][$id_subkriteria])) {
                    $nilai = $solusiIdeal['negatif'][$id_subkriteria];
                    echo "<tr>";
                    echo "<td>{$sub['nama']}</td>";
                    echo "<td>" . number_format($nilai, 4) . "</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>

</html>
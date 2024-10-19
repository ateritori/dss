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
    <div class="card shadow mb-5">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Matriks Penilaian Ternormalisasi (TOPSIS)</div>
        <div class="card-body">
            <div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <th style='text-align: center'>Nama Alternatif</th>
                            <?php
                            // Tampilkan kriteria utama yang tidak punya subkriteria dan subkriteria yang ada
                            foreach ($kriteria as $id_kriteria => $k) {
                                if ($k['punyasub'] == '0') {
                                    echo "<th style='text-align: center'>{$k['nama']}</th>";
                                } elseif ($k['punyasub'] == '1') {
                                    foreach ($subkriteria as $id_subkriteria => $sub) {
                                        if ($sub['id_kriteria'] == $id_kriteria) {
                                            echo "<th style='text-align: center'>{$sub['nama']}</th>";
                                        }
                                    }
                                }
                            }

                            echo "
      </tr>";

                            foreach ($alternatif as $id_alternatif => $nama_alternatif) {
                                echo "<tr>
        <td>{$nama_alternatif}</td>";
                                foreach ($kriteria as $id_kriteria => $k) {
                                    // Abaikan kriteria utama yang punya subkriteria
                                    if ($k['punyasub'] == '0') {
                                        // Cek kriteria utama
                                        $nilaiNormalisasi = 0;
                                        foreach ($normalisasi as $n) {
                                            if ($n['id_alternatif'] == $id_alternatif && $n['id_kriteria'] == $id_kriteria && !$n['id_subkriteria']) {
                                                $nilaiNormalisasi = $n['nilai'];
                                                break;
                                            }
                                        }
                                        echo "<td style='text-align: center'>" . round($nilaiNormalisasi, 4) . "</td>";
                                    }

                                    // Tampilkan subkriteria
                                    if ($k['punyasub'] == '1') {
                                        foreach ($subkriteria as $id_subkriteria => $sub) {
                                            if ($sub['id_kriteria'] == $id_kriteria) {
                                                $nilaiNormalisasi = 0;
                                                foreach ($normalisasi as $n) {
                                                    if ($n['id_alternatif'] == $id_alternatif && $n['id_kriteria'] == $id_kriteria && $n['id_subkriteria'] == $id_subkriteria) {
                                                        $nilaiNormalisasi = $n['nilai'];
                                                        break;
                                                    }
                                                }
                                                echo "<td  style='text-align: center'>" . round($nilaiNormalisasi, 4) . "</td>";
                                            }
                                        }
                                    }
                                }
                                echo "
      </tr>";
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow mb-5">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Matriks Keputusan Ternormalisasi (TOPSIS)</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style='text-align: center'>Nama Alternatif</th>
                            <?php
                            // Menampilkan Kriteria/Subkriteria sebagai kolom
                            foreach ($kriteria as $id_kriteria => $k) {
                                if ($k['punyasub'] == '0') {
                                    // Jika kriteria tidak memiliki subkriteria
                                    echo "<th style='text-align: center'>{$k['nama']}</th>";
                                } elseif ($k['punyasub'] == '1') {
                                    // Jika kriteria memiliki subkriteria
                                    foreach ($subkriteria as $id_subkriteria => $sub) {
                                        if ($sub['id_kriteria'] == $id_kriteria) {
                                            echo "<th style='text-align: center'>{$sub['nama']}</th>";
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
                        <?php
                        // Tampilkan setiap alternatif dengan nilai keputusan ternormalisasi
                        foreach ($alternatif as $id_alternatif => $nama_alternatif) {
                            echo "<tr>
            <td>{$nama_alternatif}</td>";
                            // Tampilkan nilai keputusan ternormalisasi untuk setiap kriteria/subkriteria
                            foreach ($kriteria as $id_kriteria => $k) {
                                // Kriteria tanpa subkriteria
                                if ($k['punyasub'] == '0') {
                                    $nilaiTernormalisasi = 0;
                                    foreach ($keputusanTernormalisasi as $kt) {
                                        if ($kt['id_alternatif'] == $id_alternatif && $kt['id_kriteria'] == $id_kriteria && !$kt['id_subkriteria']) {
                                            $nilaiTernormalisasi = round($kt['nilai'] * $k['bobot'], 4); // Kalikan dengan bobot kriteria
                                            break;
                                        }
                                    }
                                    echo "<td style='text-align: center'>{$nilaiTernormalisasi}</td>";
                                }

                                // Kriteria dengan subkriteria
                                if ($k['punyasub'] == '1') {
                                    foreach ($subkriteria as $id_subkriteria => $sub) {
                                        if ($sub['id_kriteria'] == $id_kriteria) {
                                            $nilaiTernormalisasi = 0;
                                            foreach ($keputusanTernormalisasi as $kt) {
                                                if ($kt['id_alternatif'] == $id_alternatif && $kt['id_kriteria'] == $id_kriteria && $kt['id_subkriteria'] == $id_subkriteria) {
                                                    $nilaiTernormalisasi = round($kt['nilai'], 4);
                                                    break;
                                                }
                                            }
                                            echo "<td style='text-align: center'>{$nilaiTernormalisasi}</td>";
                                        }
                                    }
                                }
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
<?php
$conn->close();
?>
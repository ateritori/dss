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

// Ambil data kriteria dan bobotnya
$queryKriteria = "SELECT * FROM Kriteria";
$resultKriteria = $conn->query($queryKriteria);
$kriteria = [];
while ($row = $resultKriteria->fetch_assoc()) {
    $kriteria[$row['id_kriteria']] = [
        'nama' => $row['nama_kriteria'],
        'bobot' => $row['bobot_kriteria'],
        'punyasub' => $row['punyasub']
    ];
}

// Ambil data subkriteria dan bobotnya (jika ada)
$querySubKriteria = "SELECT * FROM SubKriteria";
$resultSubKriteria = $conn->query($querySubKriteria);
$subkriteria = [];
while ($row = $resultSubKriteria->fetch_assoc()) {
    $subkriteria[$row['id_subkriteria']] = [
        'id_kriteria' => $row['id_kriteria'],
        'nama' => $row['nama_subkriteria'],
        'bobot' => $row['bobot_subkriteria']
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

// Proses perhitungan nilai preferensi
$nilaiPreferensi = [];
foreach ($alternatif as $id_alternatif => $nama_alternatif) {
    $totalPreferensi = 0;

    foreach ($kriteria as $id_kriteria => $k) {
        // Hitung nilai maksimum dan minimum untuk normalisasi
        $nilaiMaksimum = PHP_INT_MIN;
        $nilaiMinimum = PHP_INT_MAX;

        foreach ($penilaian as $n) {
            if ($n['id_kriteria'] == $id_kriteria) {
                if ($n['nilai'] > $nilaiMaksimum) {
                    $nilaiMaksimum = $n['nilai'];
                }
                if ($n['nilai'] < $nilaiMinimum) {
                    $nilaiMinimum = $n['nilai'];
                }
            }
        }

        // Normalisasi nilai
        if ($k['punyasub'] == '0') {
            foreach ($penilaian as $n) {
                if ($n['id_alternatif'] == $id_alternatif && $n['id_kriteria'] == $id_kriteria && is_null($n['id_subkriteria'])) {
                    // Normalisasi untuk kriteria tanpa subkriteria
                    $nilaiNormalisasi = ($nilaiMaksimum == 0) ? 0 : $n['nilai'] / $nilaiMaksimum;
                    $totalPreferensi += $nilaiNormalisasi * $k['bobot'];
                    break;
                }
            }
        } elseif ($k['punyasub'] == '1') {
            foreach ($subkriteria as $id_subkriteria => $sub) {
                if ($sub['id_kriteria'] == $id_kriteria) {
                    foreach ($penilaian as $n) {
                        if ($n['id_alternatif'] == $id_alternatif && $n['id_kriteria'] == $id_kriteria && $n['id_subkriteria'] == $id_subkriteria) {
                            // Normalisasi untuk kriteria dengan subkriteria
                            $nilaiNormalisasi = ($nilaiMaksimum == 0) ? 0 : $n['nilai'] / $nilaiMaksimum;
                            $totalPreferensi += $nilaiNormalisasi * $sub['bobot'];
                            break;
                        }
                    }
                }
            }
        }
    }

    // Simpan nilai preferensi untuk alternatif ini
    $nilaiPreferensi[$id_alternatif] = $totalPreferensi;
}

// Urutkan alternatif berdasarkan nilai preferensi (dari tinggi ke rendah)
arsort($nilaiPreferensi);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Hasil Perhitungan SAW</title>
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
    <div class="card shadow mb-5">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Hasil Perankingan SAW</div>
        <div class="card-body">
            <div>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style='text-align: center'>Nama Alternatif</th>
                                    <th style='text-align: center'>Nilai Preferensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Tampilkan hasil perhitungan SAW
                                foreach ($nilaiPreferensi as $id_alternatif => $preferensi) {
                                    echo "<tr>
                    <td>{$alternatif[$id_alternatif]}</td>
                    <td style='text-align: center'>" . round($preferensi, 2) . "</td>
                  </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php
$conn->close();
?>
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

        .hasil-wp {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .normalisasi {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="card shadow">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Penilaian</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" style="color: black; text-align: center">Nama Alternatif</th>
                            <?php
                            require 'config/koneksi.php';
                            $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");

                            while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                                if ($kriteria['punyasub'] == 0) {
                                    echo "<th scope='col' style='color: black; text-align: center'>" . $kriteria['nama_kriteria'] . "</th>";
                                } else {
                                    $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                                    while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                        echo "<th scope='col' style='color: black; text-align: center'>" . $subKriteria['nama_subkriteria'] . "</th>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "
                SELECT 
                    a.id_alternatif,
                    a.nama_alternatif,
                    k.id_kriteria,
                    k.nama_kriteria,
                    sk.id_subkriteria,
                    sk.nama_subkriteria,
                    p.nilai
                FROM 
                    Penilaian p
                JOIN 
                    Alternatif a ON p.id_alternatif = a.id_alternatif
                JOIN 
                    Kriteria k ON p.id_kriteria = k.id_kriteria
                LEFT JOIN 
                    SubKriteria sk ON p.id_subkriteria = sk.id_subkriteria
                WHERE 
                    a.status_alternatif = '1'
                ORDER BY 
                    a.id_alternatif, k.id_kriteria, sk.id_subkriteria;
            ";

                        $result = mysqli_query($conn, $sql);
                        $dataByAlternatif = [];

                        while ($data = mysqli_fetch_array($result)) {
                            $dataByAlternatif[$data['id_alternatif']]['nama_alternatif'] = $data['nama_alternatif'];
                            if (!is_null($data['nama_subkriteria'])) {
                                $dataByAlternatif[$data['id_alternatif']]['penilaian'][$data['nama_subkriteria']] = $data['nilai'];
                            } else {
                                $dataByAlternatif[$data['id_alternatif']]['penilaian'][$data['nama_kriteria']] = $data['nilai'];
                            }
                        }

                        $resultWP = [];
                        foreach ($dataByAlternatif as $id_alternatif => $dataAlternatif) {
                            $nilaiWP = 1; // Inisialisasi nilai WP
                            echo "<tr>";
                            echo "<td style='color: black'>" . $dataAlternatif['nama_alternatif'] . "</td>";

                            // Ambil semua kriteria untuk menentukan bobot
                            $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");
                            while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                                if ($kriteria['punyasub'] == 0) {
                                    // Ambil bobot kriteria
                                    $bobot = $kriteria['bobot_kriteria'];
                                    // Ambil nilai kriteria
                                    $nilai = isset($dataAlternatif['penilaian'][$kriteria['nama_kriteria']]) ? $dataAlternatif['penilaian'][$kriteria['nama_kriteria']] : 0;
                                    $nilaiWP *= pow($nilai, $bobot);
                                    echo "<td style='color: black; text-align: center'>$nilai</td>";
                                } else {
                                    // Jika kriteria punya subkriteria, ambil subkriteria terkait
                                    $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                                    while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                        $bobot = $subKriteria['bobot_subkriteria'];
                                        $nilaiSubKriteria = isset($dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']]) ? $dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']] : 0;
                                        $nilaiWP *= pow($nilaiSubKriteria, $bobot);
                                        echo "<td style='color: black; text-align: center'>$nilaiSubKriteria</td>";
                                    }
                                }
                            }
                            $resultWP[$id_alternatif] = $nilaiWP;
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Penilaian Ternormalisasi</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" style="color: black; text-align: center">Nama Alternatif</th>
                        <?php
                        // Tampilkan header untuk normalisasi
                        $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");
                        while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                            if ($kriteria['punyasub'] == 0) {
                                echo "<th scope='col' style='color: black; text-align: center'>" . $kriteria['nama_kriteria'] . " (Normalisasi)</th>";
                            } else {
                                $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                                while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                    echo "<th scope='col' style='color: black; text-align: center'>" . $subKriteria['nama_subkriteria'] . " (Normalisasi)</th>";
                                }
                            }
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataByAlternatif as $id_alternatif => $dataAlternatif) {
                        echo "<tr>";
                        echo "<td style='color: black'>" . $dataAlternatif['nama_alternatif'] . "</td>";

                        // Ambil semua kriteria untuk menghitung normalisasi
                        $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");
                        while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                            if ($kriteria['punyasub'] == 0) {
                                // Ambil nilai kriteria
                                $nilai = isset($dataAlternatif['penilaian'][$kriteria['nama_kriteria']]) ? $dataAlternatif['penilaian'][$kriteria['nama_kriteria']] : 0;
                                // Hitung normalisasi
                                $normalisasi = $nilai / 100; // Misalnya normalisasi terhadap nilai maksimum 100
                                echo "<td style='color: black; text-align: center'>" . number_format($normalisasi, 4) . "</td>";
                            } else {
                                // Jika kriteria punya subkriteria, ambil subkriteria terkait
                                $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                                while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                    $nilaiSubKriteria = isset($dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']]) ? $dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']] : 0;
                                    // Hitung normalisasi
                                    $normalisasiSubKriteria = $nilaiSubKriteria / 100; // Misalnya normalisasi terhadap nilai maksimum 100
                                    echo "<td style='color: black; text-align: center'>" . number_format($normalisasiSubKriteria, 4) . "</td>";
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
    <div class="card shadow">
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Hasil Metode Weighted Product</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" style="color: black; text-align: center">Nama Alternatif</th>
                        <th scope="col" style="color: black; text-align: center">Nilai WP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Urutkan hasil WP dari yang tertinggi
                    arsort($resultWP);
                    foreach ($resultWP as $id_alternatif => $nilaiWP) {
                        echo "<tr>";
                        echo "<td style='color: black'>" . $dataByAlternatif[$id_alternatif]['nama_alternatif'] . "</td>";
                        echo "<td style='color: black; text-align: center'>" . number_format($nilaiWP, 4) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
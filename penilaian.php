<?php
require 'config/koneksi.php';

// Ambil semua kriteria
$kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");
$kriteriaData = [];
while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
    $kriteriaData[$kriteria['id_kriteria']] = [
        'nama_kriteria' => $kriteria['nama_kriteria'],
        'punyasub' => $kriteria['punyasub']
    ];
}

// Ambil semua alternatif dan penilaian
$sql = "
    SELECT 
        a.id_alternatif,
        a.nama_alternatif,
        k.id_kriteria,
        k.nama_kriteria,
        sk.id_subkriteria,
        sk.nama_subkriteria,
        p.nilai,
        r.uraian
    FROM 
        Alternatif a
    LEFT JOIN 
        Penilaian p ON a.id_alternatif = p.id_alternatif
    LEFT JOIN 
        Kriteria k ON p.id_kriteria = k.id_kriteria
    LEFT JOIN 
        SubKriteria sk ON p.id_subkriteria = sk.id_subkriteria
    LEFT JOIN 
        Rentang r ON p.nilai = r.nilai_rentang 
        AND r.id_kriteria = p.id_kriteria 
        AND (r.id_subkriteria = sk.id_subkriteria OR r.id_subkriteria IS NULL)
    WHERE 
        a.status_alternatif = '1'
    ORDER BY 
        a.id_alternatif, k.id_kriteria, sk.id_subkriteria;
";

$result = mysqli_query($conn, $sql);
$dataGrouped = [];

// Mengelompokkan data berdasarkan alternatif dan kriteria
while ($data = mysqli_fetch_array($result)) {
    $id_alternatif = $data['id_alternatif'];
    $id_kriteria = $data['id_kriteria'];
    $id_subkriteria = $data['id_subkriteria'];

    $dataGrouped[$id_alternatif]['nama_alternatif'] = $data['nama_alternatif'];

    if ($kriteriaData[$id_kriteria]['punyasub'] == 1) {
        // Simpan uraian berdasarkan subkriteria jika ada
        $dataGrouped[$id_alternatif]['nilai'][$id_kriteria]['subkriteria'][$id_subkriteria] = $data['uraian'] ?? $data['nilai'] ?? '-';
    } else {
        // Simpan uraian untuk kriteria tanpa subkriteria
        $dataGrouped[$id_alternatif]['nilai'][$id_kriteria]['uraian'] = $data['uraian'] ?? $data['nilai'] ?? '-';
    }
}

// Tampilkan tabel
?>

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
        <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Penilaian</div>
        <div class="card-body">
            <a href="dashboard.php?url=tambahnilai" class="btn btn-success btn-icon-split" style="background: #167395">
                <span class="icon text-white-50">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span class="text" style="font-weight: bold;">Isi Penilaian</span>
            </a>

            <div>
                <p style="color: black; font-weight: bold">Tabel Berikut Ini Menampilkan Data Hasil Penilaian dari Masing-Masing Kandidat</p>
                <div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama Alternatif</th>
                                    <?php
                                    // Menampilkan semua kriteria dan subkriteria sebagai kolom
                                    foreach ($kriteriaData as $id_kriteria => $info) {
                                        if ($info['punyasub'] == 0) {
                                            // Tampilkan kriteria yang tidak memiliki subkriteria
                                            echo "<th scope='col'>{$info['nama_kriteria']}</th>";
                                        } else {
                                            // Tampilkan subkriteria sebagai kolom
                                            $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '$id_kriteria'");
                                            while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                                echo "<th scope='col'>{$subKriteria['nama_subkriteria']}</th>";
                                            }
                                        }
                                    }
                                    ?>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                foreach ($dataGrouped as $id_alternatif => $dataAlternatif) {
                                    $no++;
                                    echo "<tr>";
                                    echo "<th scope='row'>$no</th>";
                                    echo "<td>" . $dataAlternatif['nama_alternatif'] . "</td>";

                                    // Menampilkan uraian untuk setiap kriteria dan subkriteria
                                    foreach ($kriteriaData as $id_kriteria => $info) {
                                        if ($info['punyasub'] == 0) {
                                            // Jika kriteria tidak punya subkriteria, tampilkan uraian
                                            if (isset($dataAlternatif['nilai'][$id_kriteria]['uraian'])) {
                                                echo "<td>" . $dataAlternatif['nilai'][$id_kriteria]['uraian'] . "</td>";
                                            } else {
                                                echo "<td>-</td>"; // Tampilkan '-' jika tidak ada penilaian untuk kriteria ini
                                            }
                                        } else {
                                            // Jika kriteria punya subkriteria, tampilkan uraian subkriteria
                                            $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '$id_kriteria'");
                                            while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                                                $id_subkriteria = $subKriteria['id_subkriteria'];
                                                // Tampilkan uraian yang sesuai dengan subkriteria
                                                if (isset($dataAlternatif['nilai'][$id_kriteria]['subkriteria'][$id_subkriteria])) {
                                                    echo "<td>" . $dataAlternatif['nilai'][$id_kriteria]['subkriteria'][$id_subkriteria] . "</td>";
                                                } else {
                                                    echo "<td>-</td>"; // Tampilkan '-' jika tidak ada penilaian untuk subkriteria ini
                                                }
                                            }
                                        }
                                    }

                                    // Tambahkan tombol edit
                                    echo "<td><a href='dashboard.php?url=editpenilaian&id_alternatif={$id_alternatif}' class='btn-edit'><i class='fa fa-edit'></i></a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
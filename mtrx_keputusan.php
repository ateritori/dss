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
  </style>
</head>

<body>
  <br>
  <div class="card shadow mb-5">
    <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">Data Penilaian</div>

    <div class="card-body">
      <div>
        <div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th scope="col" style="color: black; text-align: center">Nama Alternatif</th>
                  <?php
                  // Ambil semua kriteria
                  require 'config/koneksi.php';
                  $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");

                  while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                    // Jika kriteria tidak punya subkriteria (punyasub = 0)
                    if ($kriteria['punyasub'] == 0) {
                      // Tampilkan kriteria sebagai kolom
                      echo "<th scope='col' style='color: black; text-align: center'>" . $kriteria['nama_kriteria'] . "</th>";
                    } else {
                      // Jika kriteria punya subkriteria (punyasub = 1), ambil subkriteria terkait
                      $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                      // Tampilkan setiap subkriteria sebagai kolom
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

                // Ambil semua alternatif
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
                    Penilaian p
                JOIN 
                    Alternatif a ON p.id_alternatif = a.id_alternatif
                JOIN 
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
                $dataByAlternatif = [];

                while ($data = mysqli_fetch_array($result)) {
                  $dataByAlternatif[$data['id_alternatif']]['nama_alternatif'] = $data['nama_alternatif'];
                  if (!is_null($data['nama_subkriteria'])) {
                    $dataByAlternatif[$data['id_alternatif']]['penilaian'][$data['nama_subkriteria']] = $data['nilai'];
                  } else {
                    $dataByAlternatif[$data['id_alternatif']]['penilaian'][$data['nama_kriteria']] = $data['nilai'];
                  }
                }

                foreach ($dataByAlternatif as $id_alternatif => $dataAlternatif) {
                  echo "<tr>";
                  echo "<td style='color: black'>" . $dataAlternatif['nama_alternatif'] . "</td>";

                  // Ambil semua kriteria lagi untuk menampilkan nilai
                  $kriteriaQuery = mysqli_query($conn, "SELECT * FROM Kriteria");
                  while ($kriteria = mysqli_fetch_array($kriteriaQuery)) {
                    // Jika kriteria tidak punya subkriteria (punyasub = 0)
                    if ($kriteria['punyasub'] == 0) {
                      // Tampilkan nilai untuk kriteria
                      $nilai = isset($dataAlternatif['penilaian'][$kriteria['nama_kriteria']]) ? $dataAlternatif['penilaian'][$kriteria['nama_kriteria']] : '-';
                      echo "<td style='color: black; text-align: center'>$nilai</td>";
                    } else {
                      // Jika kriteria punya subkriteria, ambil subkriteria terkait
                      $subKriteriaQuery = mysqli_query($conn, "SELECT * FROM SubKriteria WHERE id_kriteria = '" . $kriteria['id_kriteria'] . "'");
                      while ($subKriteria = mysqli_fetch_array($subKriteriaQuery)) {
                        $nilaiSubKriteria = isset($dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']]) ? $dataAlternatif['penilaian'][$subKriteria['nama_subkriteria']] : '-';
                        echo "<td style='color: black; text-align: center'>$nilaiSubKriteria</td>";
                      }
                    }
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
</body>

</html>
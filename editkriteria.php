<?php
require 'config/koneksi.php';

// Mendapatkan ID kriteria dari parameter URL
$id_kriteria = $_GET['id'] ?? null;

if ($id_kriteria === null) {
  echo "<script>alert('ID Kriteria tidak ditemukan'); window.location = 'dashboard.php?url=kriteria';</script>";
  exit();
}

// Query untuk mendapatkan data kriteria berdasarkan ID
$query_kriteria = "SELECT * FROM Kriteria WHERE id_kriteria = ?";
$stmt = $conn->prepare($query_kriteria);
$stmt->bind_param("i", $id_kriteria);
$stmt->execute();
$result = $stmt->get_result();
$kriteria_data = $result->fetch_assoc();

if (!$kriteria_data) {
  echo "<script>alert('Data Kriteria tidak ditemukan'); window.location = 'dashboard.php?url=kriteria';</script>";
  exit();
}

// Mendapatkan bobot kriteria saat ini
$bobot_sebelumnya = $kriteria_data['bobot_kriteria'];

// Query untuk mendapatkan total bobot kriteria yang ada
$query_total_bobot = "SELECT SUM(bobot_kriteria) AS total_bobot FROM Kriteria";
$result_total = $conn->query($query_total_bobot);
$row_total = $result_total->fetch_assoc();
$total_bobot = $row_total['total_bobot'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Cek apakah kriteria kosong
  if (empty($_POST['kriteria'])) {
    echo "<script>alert('Kriteria tidak boleh kosong'); window.location = 'dashboard.php?url=editkriteria&id=$id_kriteria';</script>";
    exit();
  }

  // Mendapatkan input dari form
  $kriteria = $_POST['kriteria'];
  $bobot = isset($_POST['bobot']) && $_POST['bobot'] !== "" ? $_POST['bobot'] : NULL;
  $atribut = isset($_POST['atribut']) && $_POST['atribut'] !== "" ? $_POST['atribut'] : NULL;
  $status_sub = $_POST['status_sub'];

  // Validasi apakah bobot diisi
  if ($bobot === NULL) {
    echo "<script>alert('Bobot kriteria harus diisi'); window.location = 'dashboard.php?url=editkriteria&id=$id_kriteria';</script>";
    exit();
  }

  // Menghitung total bobot setelah pembaruan
  $total_bobot_baru = $total_bobot;

  // Cek apakah total bobot akan lebih dari 1
  if ($total_bobot_baru > 1) {
    echo "<script>alert('Total bobot kriteria tidak boleh lebih dari 1. Total bobot saat ini: " . number_format($total_bobot, 2) . "'); window.location = 'dashboard.php?url=editkriteria&id=$id_kriteria';</script>";
    exit();
  }

  // Menyiapkan query untuk mengupdate data kriteria
  $query_update = "UPDATE Kriteria SET nama_kriteria = ?, bobot_kriteria = ?, tipe_kriteria = ?, punyasub = ? WHERE id_kriteria = ?";
  $stmt_update = $conn->prepare($query_update);
  $stmt_update->bind_param("sdssi", $kriteria, $bobot, $atribut, $status_sub, $id_kriteria);

  if ($stmt_update->execute()) {
    echo "<script>alert('Data Berhasil Diupdate'); window.location = 'dashboard.php?url=kriteria';</script>";
  } else {
    echo "<script>alert('Gagal Mengupdate Data: " . $conn->error . "'); window.location = 'dashboard.php?url=editkriteria&id=$id_kriteria';</script>";
  }

  // Tutup statement
  $stmt_update->close();
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
  <title>EDIT DATA</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style type="text/css">
    form {
      width: 100%;
    }

    body {
      font-family: "Verdana";
    }

    .error {
      font-size: 11px;
      font-weight: bold;
      position: relative;
      left: 10px;
      margin-bottom: 6px;
      color: firebrick;
    }

    #tipe_kriteria_container {
      display: none;
    }
  </style>
</head>

<body id="page-top">
  <br>
  <div class="card shadow" style="width: 50%;">
    <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Edit Data Kriteria</div>
    <div class="card-body">
      <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group cols-sm-6">
          <label>Kriteria</label>
          <input type="text" name="kriteria" value="<?= $kriteria_data['nama_kriteria'] ?>" class="form-control" required autofocus>
          <span class="error"><?= $kriteriaErr ?? '' ?></span>
        </div>

        <div class="form-group cols-sm-6">
          <label>Status Sub-Kriteria</label>
          <select class="form-control" name="status_sub" id="status_sub">
            <option value="0" <?= $kriteria_data['punyasub'] == '0' ? 'selected' : '' ?>>Tidak Memiliki Sub-Kriteria</option>
            <option value="1" <?= $kriteria_data['punyasub'] == '1' ? 'selected' : '' ?>>Memiliki Sub-Kriteria</option>
          </select>
        </div>

        <div class="form-group cols-sm-6">
          <label>Tipe Kriteria</label>
          <select class="form-control" name="atribut" id="atribut">
            <option value="" hidden>Pilih Tipe Kriteria</option>
            <option value="cost" <?= $kriteria_data['tipe_kriteria'] == 'cost' ? 'selected' : '' ?>>Cost</option>
            <option value="benefit" <?= $kriteria_data['tipe_kriteria'] == 'benefit' ? 'selected' : '' ?>>Benefit</option>
          </select>
          <span class="error"><?= $atributErr ?? '' ?></span>
        </div>

        <div class="form-group cols-sm-6">
          <label>Bobot (antara 0.00 s/d 1.00)</label>
          <input type="number" step="any" name="bobot" value="<?= $kriteria_data['bobot_kriteria'] ?>" class="form-control" required>
          <label style="color: black; font-weight: bold;">Total Bobot Sementara: <?= number_format($total_bobot, 2) ?></label>
          <span class="error"><?= $bobotErr ?? '' ?></span>
        </div>

        <div class="form-group cols-sm-6">
          <button type="submit" class="btn btn-secondary btn-icon-split" style="background: #167395" name="submit">
            <span class="icon text-white-50">
              <i class="fas fa-user-check"></i>
            </span>
            <span class="text">Update</span>
          </button>
          <button type="button" class="btn btn-secondary btn-icon-split" style="background: #167395" onclick="history.back()">
            <span class="icon text-white-50">
              <i class="fas fa-user-check"></i>
            </span>
            <span class="text">Kembali</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
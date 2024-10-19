    <?php
    require 'config/koneksi.php';

    // Query untuk mendapatkan total bobot kriteria yang sudah tersimpan
    $query_total_bobot = "SELECT SUM(bobot_kriteria) AS total_bobot FROM Kriteria";
    $result = $conn->query($query_total_bobot);
    $row = $result->fetch_assoc();
    $total_bobot = $row['total_bobot'] ?? 0; // Jika total bobot kosong, anggap sebagai 0

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Cek apakah kriteria kosong
        if (empty($_POST['kriteria'])) {
            echo "<script>alert('Kriteria tidak boleh kosong'); window.location = 'dashboard.php?url=tambahkriteria';</script>";
            exit();
        }

        // Mendapatkan input dari form
        $kriteria = $_POST['kriteria'];
        $bobot = isset($_POST['bobot']) && $_POST['bobot'] !== "" ? $_POST['bobot'] : NULL;
        $atribut = isset($_POST['atribut']) && $_POST['atribut'] !== "" ? $_POST['atribut'] : NULL; // Ini bisa NULL jika tidak dipilih
        $status_sub = $_POST['status_sub']; // Mendapatkan status sub-kriteria dari dropdown

        // Validasi apakah bobot_kriteria diisi
        if ($bobot === NULL) {
            echo "<script>alert('Bobot kriteria harus diisi'); window.location = 'dashboard.php?url=tambahkriteria';</script>";
            exit();
        }

        // Cek apakah bobot yang baru akan membuat total bobot lebih dari 1
        if ($total_bobot + $bobot > 1) {
            echo "<script>alert('Total bobot kriteria tidak boleh lebih dari 1. Total bobot saat ini: $total_bobot'); window.location = 'dashboard.php?url=tambahkriteria';</script>";
            exit();
        }

        // Menyiapkan query dengan prepared statements untuk menyimpan data ke tabel Kriteria
        $query = "INSERT INTO Kriteria (nama_kriteria, bobot_kriteria, tipe_kriteria, punyasub) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Bind parameters. Atribut di-bind sebagai nullable
        $stmt->bind_param("sdss", $kriteria, $bobot, $atribut, $status_sub);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "<script>alert('Data Berhasil Disimpan'); window.location = 'dashboard.php?url=kriteria';</script>";
        } else {
            echo "<script>alert('Gagal Menyimpan Data: " . $conn->error . "'); window.location = 'dashboard.php?url=kriteria';</script>";
        }

        // Tutup statement
        $stmt->close();
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
        <title>TAMBAH DATA</title>
        <!-- Tambahkan jQuery -->
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
                /* Sembunyikan tipe kriteria pada awal */
            }
        </style>
    </head>

    <body id="page-top">
        <br>
        <div class="card shadow" style="width: 50%;">
            <div class="card-header m-0 font-weight-bold" style="text-align:center; background-color: #167395; color: white">Tambah Data Kriteria</div>
            <div class="card-body">
                <form method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="form-group cols-sm-6">
                        <label style="color: black">Kriteria</label>
                        <input type="text" name="kriteria" value="<?= $kriteria ?>" class="form-control" style="color: black" required autofocus>
                        <span class="error"><?= $kriteriaErr ?? '' ?></span>
                    </div>

                    <div class="form-group cols-sm-6">
                        <label style="color: black">Status Sub-Kriteria</label>
                        <select class="form-control" name="status_sub" id="status_sub" style="color: black">
                            <option value="0">Tidak Memiliki Sub-Kriteria</option>
                            <option value="1">Memiliki Sub-Kriteria</option>
                        </select>
                    </div>

                    <div class="form-group cols-sm-6" id="tipe_kriteria_container">
                        <label style="color: black">Tipe Kriteria</label>
                        <select class="form-control" name="atribut" id="atribut" style="color: black">
                            <option value="" selected hidden>Pilih Tipe Kriteria</option>
                            <option value="cost">Cost</option>
                            <option value="benefit">Benefit</option>
                        </select>
                        <span class="error"><?= $atributErr ?? '' ?></span>
                    </div>

                    <div class="form-group cols-sm-6">
                        <label style="color: black">Bobot (antara 0.00 s/d 1.00)</label>
                        <input type="number" step="any" name="bobot" value="<?= isset($bobot) ? $bobot : '' ?>" class="form-control" style="color: black" required>
                        <label style="color: black; font-weight: bold;">Total Bobot Sementara: <?= number_format($total_bobot, 2) ?></label>
                        <span class="error"><?= isset($bobotErr) ? $bobotErr : '' ?></span>
                    </div>

                    <div class="form-group cols-sm-6">
                        <button type="submit" class="btn btn-secondary btn-icon-split" style="background: #167395" name="submit">
                            <span class="icon text-white-50">
                                <i class="fas fa-user-check"></i>
                            </span>
                            <span class="text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Menjalankan fungsi untuk menyembunyikan/menampilkan Tipe Kriteria
                toggleTipeKriteria();

                // Event listener untuk dropdown Status Sub-Kriteria
                $('#status_sub').change(function() {
                    toggleTipeKriteria();
                });

                function toggleTipeKriteria() {
                    if ($('#status_sub').val() == '0') { // Jika tidak memiliki sub-kriteria
                        $('#tipe_kriteria_container').hide();
                        $('#atribut').val(''); // Mengosongkan pilihan Tipe Kriteria
                    } else {
                        $('#tipe_kriteria_container').show();
                    }
                }
            });
        </script>
    </body>

    </html>
<div class="card shadow mt-3">
    <div class="card-header py-3" style="text-align: center; background-color: #167395; color: white; font-weight:bold">DATA RENTANG PENILAIAN</div>
    <div class="card-body">
        <div class="table-responsive mt-3">
            <?php
            // Menghubungkan ke database
            require('config/koneksi.php');

            // Query untuk mengambil semua data dari tabel rentang
            $query = "
                SELECT 
                    r.id_rentang,
                    r.id_kriteria,
                    k.nama_kriteria,
                    k.sub_kriteria,
                    s.nama_subkriteria,
                    r.jenis_penilaian,
                    r.uraian,
                    r.nilai_rentang
                FROM 
                    rentang r
                LEFT JOIN 
                    kriteria k ON r.id_kriteria = k.id_kriteria
                LEFT JOIN 
                    subkriteria s ON r.id_subkriteria = s.id_subkriteria
                ORDER BY 
                    r.id_kriteria, r.id_subkriteria
            ";

            // Menjalankan query
            $result = mysqli_query($conn, $query);

            // Memeriksa apakah query berhasil
            if (mysqli_num_rows($result) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr style='text-align: center'>";
                echo "<th>Nomor Urut</th>";
                echo "<th>Nama Kriteria / Sub-Kriteria</th>";
                echo "<th>Jenis Penilaian</th>";
                echo "<th>Uraian</th>";
                echo "<th>Value</th>";
                echo "<th>Aksi</th>";
                echo "</tr>";

                $nomor_urut = 1;
                $previous_kriteria = '';

                // Array untuk menyimpan data sementara
                $rows = [];

                // Simpan semua data dalam array
                while ($row = mysqli_fetch_assoc($result)) {
                    $rows[] = $row;
                }

                // Loop untuk menampilkan data dengan rowspan
                for ($i = 0; $i < count($rows); $i++) {
                    $row = $rows[$i];

                    // Cek apakah kriteria/sub-kriteria sudah berubah
                    $current_kriteria = ($row['sub_kriteria'] == 0) ? $row['nama_kriteria'] : $row['nama_subkriteria'];

                    if ($previous_kriteria !== $current_kriteria) {
                        // Hitung berapa banyak baris detail untuk kriteria/sub-kriteria ini
                        $count = 1; // reset hitungan untuk kriteria/sub-kriteria baru

                        for ($j = $i + 1; $j < count($rows); $j++) {
                            $next_row = $rows[$j];
                            $next_kriteria = ($next_row['sub_kriteria'] == 0) ? $next_row['nama_kriteria'] : $next_row['nama_subkriteria'];

                            if ($current_kriteria === $next_kriteria) {
                                $count++;
                            } else {
                                break;
                            }
                        }

                        // Tampilkan kriteria/sub-kriteria dan jenis penilaian pada baris baru
                        echo "<tr>";
                        echo "<td rowspan='" . $count . "'>" . $nomor_urut . "</td>";
                        echo "<td rowspan='" . $count . "'>" . $current_kriteria . "</td>";

                        // Mengubah jenis penilaian
                        $jenis_penilaian = ($row['jenis_penilaian'] == 1) ? "Dinamis/ Input Manual" : "Rentang Nilai";
                        echo "<td rowspan='" . $count . "'>" . $jenis_penilaian . "</td>";

                        // Tampilkan detail yang lain pada baris ini
                        echo "<td>$row[uraian]</td>";
                        echo "<td>$row[nilai_rentang]</td>";
            ?>
                        <td>
                            <!-- Tombol Edit -->
                            <a href="dashboard.php?url=editnilairentang&id=<?php echo $row['id_rentang']; ?>"
                                class="btn btn-outline-dark btn-sm"
                                title="Edit Alternatif">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- Tombol Hapus -->
                            <a href="#modalDelete"
                                data-toggle="modal"
                                onclick="$('#modalDelete #formDelete').attr('action', 'hapusnilairentang.php?id=<?php echo $row['id_rentang']; ?>' )"
                                class="btn btn-outline-dark btn-sm"
                                title="Hapus Alternatif">
                                <i class="bi bi-x-square-fill"></i>
                            </a>
                        </td>
                    <?php
                        echo "</tr>";

                        $previous_kriteria = $current_kriteria; // Update kriteria
                        $nomor_urut++; // Increment nomor urut
                    } else {
                        // Tampilkan detail yang lain pada baris berikutnya
                        echo "<tr>";
                        echo "<td>$row[uraian]</td>";
                        echo "<td>$row[nilai_rentang]</td>";
                    ?>
                        <td>
                            <!-- Tombol Edit -->
                            <a href="dashboard.php?url=editnilairentang&id=<?php echo $row['id_rentang']; ?>"
                                class="btn btn-outline-dark btn-sm"
                                title="Edit Alternatif">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <!-- Tombol Hapus -->
                            <a href="#modalDelete"
                                data-toggle="modal"
                                onclick="$('#modalDelete #formDelete').attr('action', 'hapusnilairentang.php?id=<?php echo $row['id_rentang']; ?>' )"
                                class="btn btn-outline-dark btn-sm"
                                title="Hapus Alternatif">
                                <i class="bi bi-x-square-fill"></i>
                            </a>
                        </td>
            <?php
                        echo "</tr>";
                    }
                }

                echo "</table>";
            } else {
                echo "Tidak ada data yang ditemukan.";
            }

            // Menutup koneksi database
            mysqli_close($conn);
            ?>
        </div>
    </div>
</div>
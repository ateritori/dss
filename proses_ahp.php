<?php
// proses_ahp.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comparisons = $_POST['comparison'];
    $criteria = array_keys($comparisons);

    // Menyiapkan matriks perbandingan kriteria
    $n = count($criteria);
    $comparisonMatrix = array_fill(0, $n, array_fill(0, $n, 1));

    // Isi matriks perbandingan berdasarkan input pengguna
    foreach ($comparisons as $kriteria1 => $pair) {
        foreach ($pair as $kriteria2 => $value) {
            $i = array_search($kriteria1, $criteria);
            $j = array_search($kriteria2, $criteria);
            $comparisonMatrix[$i][$j] = $value;
            $comparisonMatrix[$j][$i] = 1 / $value;
        }
    }

    function calculateCR($matrix)
    {
        $n = count($matrix);

        // Jika ukuran matriks kurang dari 3, CR tidak dapat dihitung
        if ($n < 3) {
            return null; // CR tidak relevan untuk matriks yang lebih kecil dari 3x3
        }

        $sumColumn = array_fill(0, $n, 0);
        foreach ($matrix as $i => $row) {
            foreach ($row as $j => $value) {
                $sumColumn[$j] += $value;
            }
        }

        $normalizedMatrix = [];
        foreach ($matrix as $i => $row) {
            foreach ($row as $j => $value) {
                $normalizedMatrix[$i][$j] = $value / $sumColumn[$j];
            }
        }

        $priorityVector = array_fill(0, $n, 0);
        foreach ($normalizedMatrix as $i => $row) {
            $priorityVector[$i] = array_sum($row) / $n;
        }

        // Menghitung λ max
        $lambdaMax = 0;
        foreach ($priorityVector as $i => $weight) {
            $lambdaMax += $sumColumn[$i] * $weight;
        }

        // Menghitung Consistency Index (CI)
        $CI = ($lambdaMax - $n) / ($n - 1);

        // Menyediakan nilai Random Consistency Index (RI) untuk berbagai ukuran matriks
        $RI_values = [0, 0, 0.58, 0.9, 1.12, 1.24, 1.32, 1.41, 1.45];
        $RI = $RI_values[$n - 1] ?? 1.5; // Set default jika ukuran matriks lebih besar dari yang didukung

        if ($RI == 0) {
            return null; // Hindari pembagian nol
        }

        // Menghitung Consistency Ratio (CR)
        $CR = $CI / $RI;
        return $CR;
    }

    $CR = calculateCR($comparisonMatrix);

    if ($CR === null) {
        echo "<p style='color: red;'>Perbandingan tidak dapat dihitung karena ukuran matriks terlalu kecil atau terjadi kesalahan nilai RI.</p>";
    } elseif ($CR > 0.1) {
        echo "<p style='color: red;'>Matriks perbandingan tidak konsisten (CR = " . round($CR, 3) . "). Harap perbaiki nilai perbandingan.</p>";
        echo "<button onclick=\"window.location.href='?url=bobot.php'\">Kembali ke Halaman Bobot</button>";
    } else {
        echo "<p style='color: green;'>Matriks perbandingan konsisten (CR = " . round($CR, 3) . ").</p>";
        // Lanjutkan ke proses berikutnya
    }
}

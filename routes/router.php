<?php
if (isset($_GET['url'])) {
	$url = $_GET['url'];

	switch ($url) {
		case 'alternatif';
			include 'alternatif.php';
			break;

		case 'tambahalternatif';
			include 'tambahalternatif.php';
			break;

		case 'editalternatif';
			include 'editalternatif.php';
			break;

		case 'kriteria';
			include 'kriteria.php';
			break;

		case 'tambahkriteria';
			include 'tambahkriteria.php';
			break;

		case 'editkriteria';
			include 'editkriteria.php';
			break;

		case 'mtrxkeputusan';
			include 'mtrx_keputusan.php';
			break;

		case 'tambahnilai';
			include 'tambahnilai.php';
			break;

		case 'mtrxternormalisasi';
			include 'mtrx_ternormalisasi.php';
			break;

		case 'tambahsub';
			include 'tambahsub.php';
			break;

		case 'editsubkriteria';
			include 'editsubkriteria.php';
			break;

		case 'data_rentang';
			include 'data_rentang.php';
			break;

		case 'rentangnilai';
			include 'rentangnilai.php';
			break;

		case 'subrentangnilai';
			include 'subrentangnilai.php';
			break;

		case 'tambahrentang';
			include 'tambahrentang.php';
			break;

		case 'editrentang';
			include 'editrentang.php';
			break;

		case 'hapusrentang';
			include 'hapusrentang.php';
			break;

		case 'bobot';
			include 'bobot.php';
			break;

		case 'simpannilai';
			include 'simpannilai.php';
			break;

		case 'penilaian';
			include 'penilaian.php';
			break;

		case 'editpenilaian';
			include 'editpenilaian.php';
			break;

		case 'agregasi';
			include 'agregasi.php';
			break;

		case 'preferensi';
			include 'preferensi.php';
			break;

		case 'perankingan';
			include 'perangkingan.php';
			break;

		case 'rinciansaw';
			include 'rinciansaw.php';
			break;

		case 'tertimbang';
			include 'topsis_matriks_tertimbang.php';
			break;

		case 'solusi';
			include 'solusi_ideal.php';
			break;

		case 'jaraksolusi';
			include 'jarak_solusi.php';
			break;

		case 'preftopsis';
			include 'preferensi_topsis.php';
			break;

		case 'modelwp';
			include 'modelwp.php';
			break;
	}
} else {
?>
	<br>
	<div class="h3 mb-0 font-weight-bold" style="color: black;">
		SISTEM PENDUKUNG PEMBUATAN KEPUTUSAN DENGAN 3 METODE PENYELESAIAN <br>
		<hr>
		Selamat Datang <?php echo $_SESSION['username']; ?> !
	</div>
	<!-- <img src ="assets/img/gov.jpg"> -->
	<div class="container_dash">
		<img src="assets/img/gov.png" alt="government" style="width: 100%;">
	</div>

<?php
}
?>
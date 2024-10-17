<?php
// Informasi koneksi database
$servername = "localhost";   // Nama server
$username = "root";          // Username MySQL
$password = "";              // Password MySQL
$dbname = "spksaw";          // Nama database

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
	die("Koneksi gagal: " . $conn->connect_error);
}

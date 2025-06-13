<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "rekam_medis_klinik"; // GANTI DENGAN NAMA DATABASE ANDA!

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
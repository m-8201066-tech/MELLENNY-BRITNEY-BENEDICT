<?php
session_start();
include 'config.php';

// pastikan pengguna login
if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id = $_SESSION['id'];

// check jika tiada fail dipilih
if (!isset($_FILES["gambar"]) || $_FILES["gambar"]["error"] !== 0) {
    header("Location: profail.php?upload=fail");
    exit();
}

// lokasi simpan gambar
$folder = "uploads/";

// cipta folder jika belum wujud
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// validasi format fail
$ext = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
$benar = ["jpg", "jpeg", "png"];

if (!in_array($ext, $benar)) {
    header("Location: profail.php?upload=format_salah");
    exit();
}

// nama fail yang unik
$namaFail = time() . "_" . rand(1000, 9999) . "." . $ext;

// pindah fail ke folder upload
if (!move_uploaded_file($_FILES["gambar"]["tmp_name"], $folder . $namaFail)) {
    header("Location: profail.php?upload=fail");
    exit();
}

// update ke database
$sql = $conn->prepare("UPDATE pengguna SET gambar = ? WHERE id = ?");
$sql->bind_param("si", $namaFail, $id);
$sql->execute();

// selesai → kembali ke profil
header("Location: profail.php?upload=berjaya");
exit();
?>

<?php
session_start();
include "config.php";

if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id = $_SESSION['id'];

if ($_FILES['gambar']['name'] != "") {
    $namaFail = time() . "_" . $_FILES['gambar']['name'];
    $lokasi = "uploads/" . $namaFail;
    move_uploaded_file($_FILES['gambar']['tmp_name'], $lokasi);

    $sql = $conn->prepare("UPDATE pengguna SET gambar = ? WHERE id = ?");
    $sql->bind_param("si", $namaFail, $id);
    $sql->execute();
}

header("Location: p-profail.php");
exit();


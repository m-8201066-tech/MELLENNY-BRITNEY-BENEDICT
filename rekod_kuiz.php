<?php
session_start();
// Pastikan fail 'config.php' wujud dan mengandungi sambungan $conn
include 'config.php'; 

if (!isset($_SESSION['id'])) {
    // Log masuk semula jika tiada ID sesi
    echo "Sila log masuk!";
    exit();
}

// ---------------------------------------
// 1. TETAPKAN PEMBOLEH UBAH
// ---------------------------------------
$pelajar_id = $_SESSION['id'];
$bab = "Bab 1"; // Ubah mengikut bab kuiz semasa

// Markah mesti dihantar ke skrip ini melalui kaedah POST.
// Jika tiada POST['markah'], ia akan ditetapkan kepada 0.
$markah = isset($_POST['markah']) ? intval($_POST['markah']) : 0; 

// Dapatkan tarikh dan masa semasa yang betul dalam format MySQL
$tarikh = date("Y-m-d H:i:s"); 

// ---------------------------------------
// 2. KIRA NOMBOR PERCUBAAN PELAJAR
// ---------------------------------------
$sql = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM rekod_kuiz 
    WHERE pelajar_id = ? AND bab = ?
");
$sql->bind_param("is", $pelajar_id, $bab);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

// Nombor percubaan adalah jumlah rekod sedia ada + 1
$percubaan = intval($result['total']) + 1;

// ---------------------------------------
// 3. MASUKKAN REKOD BARU KE DALAM PANGKALAN DATA
// ---------------------------------------
$insert = $conn->prepare("
    INSERT INTO rekod_kuiz (pelajar_id, bab, markah, tarikh_jawab, percubaan)
    VALUES (?, ?, ?, ?, ?)
");
// Jenis pemboleh ubah: i (integer), s (string), i (integer), s (string), i (integer)
$insert->bind_param("isisi", $pelajar_id, $bab, $markah, $tarikh, $percubaan);

if ($insert->execute()) {
    echo "Rekod kuiz berjaya disimpan! Markah yang dicatatkan: " . $markah;
} else {
    // Paparkan ralat jika ada masalah dengan INSERT
    echo "Ralat: Gagal menyimpan rekod kuiz. " . $insert->error;
}

// Tutup pernyataan (statement) dan sambungan (connection)
$insert->close();
$sql->close();
$conn->close();
?>
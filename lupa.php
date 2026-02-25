<?php
// Tambah ini untuk melihat ralat jika skrin putih muncul lagi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include dirname(__FILE__) . '/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $newPass  = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($newPass !== $confirm) {
        $message = "<span style='color:red;'>Kata laluan tidak sama!</span>";
    } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        
        // Gunakan backtick (`) untuk nama kolum yang mempunyai jarak
        $stmt = $conn->prepare("UPDATE pengguna SET `Kata laluan`=? WHERE username=?");
        $stmt->bind_param("ss", $hash, $username);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "<span style='color:green;'>Berjaya! Sila log masuk dengan kata laluan baru.</span>";
            } else {
                $message = "<span style='color:orange;'>Nama pengguna tidak wujud atau tiada perubahan dibuat.</span>";
            }
        } else {
            $message = "<span style='color:red;'>Ralat sistem: " . $conn->error . "</span>";
        }
        $stmt->close();
    }
}
?>

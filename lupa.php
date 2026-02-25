<?php
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
        
        // Debug: Semak jika username wujud dahulu
        $check = $conn->prepare("SELECT id FROM pengguna WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            $message = "<span style='color:red;'>Ralat: Nama pengguna '$username' tidak dijumpai dalam sistem.</span>";
        } else {
            // Proses kemas kini
            $stmt = $conn->prepare("UPDATE pengguna SET `Kata laluan`=? WHERE username=?");
            $stmt->bind_param("ss", $hash, $username);
            
            if ($stmt->execute()) {
                // Gunakan affected_rows >= 0 untuk kes di mana data mungkin sama
                $message = "<span style='color:green;'>Berjaya! Sila log masuk dengan kata laluan baru.</span>";
            } else {
                $message = "<span style='color:red;'>Ralat SQL: " . $conn->error . "</span>";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

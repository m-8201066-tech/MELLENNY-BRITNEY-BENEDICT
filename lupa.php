<?php
// Menggunakan path mutlak untuk mengelakkan ralat "No such file or directory"
include dirname(__FILE__) . '/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $newPass  = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($newPass !== $confirm) {
        $message = "<span style='color:red;'>Kata laluan tidak sama!</span>";
    } else {
        // Keselamatan: password_hash
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        
        // Pastikan nama kolum 'Kata laluan' ada simbol backtick (`) jika ada ruang
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
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Laluan | Sains KuVocC</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: url('bg.png') no-repeat center center fixed; 
            background-size: cover;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }

        /* Overlay gelap sedikit supaya bg.png tidak terlalu terang */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .box { 
            width: 100%;
            max-width: 380px; 
            background: var(--glass); 
            padding: 40px; 
            border-radius: 24px; 
            text-align: center; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        h2 { font-weight: 800; color: #2d3436; margin-bottom: 10px; }
        p { font-size: 0.9rem; margin-bottom: 20px; }

        input { 
            width: 100%; 
            padding: 14px; 
            border-radius: 12px; 
            border: 1px solid #ddd; 
            margin-top: 15px; 
            box-sizing: border-box; 
            outline: none;
            transition: 0.3s;
        }

        input:focus { border-color: var(--primary); box-shadow: 0 0 8px rgba(108, 92, 231, 0.2); }

        button { 
            width: 100%; 
            padding: 14px; 
            border-radius: 12px; 
            background: var(--primary); 
            color: white; 
            font-weight: 700; 
            border: none; 
            margin-top: 25px; 
            cursor: pointer; 
            transition: 0.3s; 
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
        }

        button:hover { transform: translateY(-2px); background: #5b4cc4; }

        .footer-link { margin-top: 20px; display: block; text-decoration: none; color: #636e72; font-size: 0.85rem; font-weight: 600; }
        .footer-link:hover { color: var(--primary); }
    </style>
</head>
<body>

<div class="box">
    <h2>Reset Password</h2>
    <p>Masukkan maklumat untuk menukar kata laluan anda.</p>
    
    <?php if(!empty($message)) echo "<p><b>$message</b></p>"; ?>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Nama pengguna" required>
        <input type="password" name="password" placeholder="Kata laluan baru" required>
        <input type="password" name="confirm" placeholder="Sahkan kata laluan baru" required>
        <button type="submit">Hantar</button>
    </form>

    <a href="logmasuk.php" class="footer-link">← Kembali ke Log Masuk</a>
</div>

</body>
</html>

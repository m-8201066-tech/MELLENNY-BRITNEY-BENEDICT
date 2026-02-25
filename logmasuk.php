<?php
session_start();
include 'config.php'; 

// Paparan ralat
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Cari berdasarkan username sahaja dahulu
    $sql = $conn->prepare("SELECT id, username, password, peranan, emel, svm, semester FROM pengguna WHERE username = ?");

    if ($sql === false) {
         echo "<script>alert('Ralat SQL! Sila semak query: " . $conn->error . "'); window.history.back();</script>";
         exit();
    }
    
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();
    
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        
        // Semak password menggunakan verify (kerana kita guna hash di lupa.php)
        if (password_verify($password, $data['password'])) {
            
            $peranan_db = strtolower($data['peranan']);

            $_SESSION['id']       = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['peranan']  = $peranan_db;
            $_SESSION['kursus']   = ''; 

            if ($peranan_db == "pensyarah") { 
                $sql_kursus = $conn->prepare("SELECT k.kod_kursus FROM kursus k JOIN pengajaran_kursus pk ON k.id = pk.kursus_id WHERE pk.pensyarah_id = ?");
                $sql_kursus->bind_param("i", $data['id']);
                $sql_kursus->execute();
                $result_kursus = $sql_kursus->get_result();
                
                $kursus_list = [];
                while($kursus_row = $result_kursus->fetch_assoc()){
                     $kursus_list[] = $kursus_row['kod_kursus'];
                }
                $_SESSION['kursus'] = implode(",", $kursus_list); 
                header("Location: pensyarah.php");
                exit();

            } else if ($peranan_db == "pelajar") { 
                $sql_kursus = $conn->prepare("SELECT k.kod_kursus FROM kursus k JOIN pendaftaran_kursus pdk ON k.id = pdk.kursus_id WHERE pdk.pelajar_id = ?");
                $sql_kursus->bind_param("i", $data['id']);
                $sql_kursus->execute();
                $result_kursus = $sql_kursus->get_result();
                
                if ($kursus_row = $result_kursus->fetch_assoc()){
                     $_SESSION['kursus'] = $kursus_row['kod_kursus'];
                }
                header("Location: index.php");
                exit();
            }
        } else {
            echo "<script>alert('Username atau password salah!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Username atau password salah!'); window.history.back();</script>";
        exit();
    }
    $sql->close(); 
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log Masuk | Sains KuVocC</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root { --primary-soft: #6c5ce7; --secondary-soft: #a29bfe; --text-dark: #2d3436; }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }
    body { min-height: 100vh; display: flex; flex-direction: column; background: url('bg.png') no-repeat center center fixed; background-size: cover; justify-content: center; align-items: center; }
    .login-wrapper { width: 100%; max-width: 450px; padding: 20px; z-index: 10; }
    .login-container { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); padding: 60px 40px; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); text-align: center; border: 1px solid rgba(255, 255, 255, 0.5); }
    h2 { color: var(--text-dark); margin-bottom: 10px; font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
    p.subtitle { color: #636e72; margin-bottom: 40px; font-size: 1rem; }
    .input-group { text-align: left; margin-bottom: 25px; position: relative; }
    .input-group label { font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 8px; font-size: 0.9rem; margin-left: 5px; }
    .input-group input { width: 100%; padding: 15px 20px; border: 2px solid transparent; border-radius: 15px; font-size: 1rem; background: white; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
    .input-group input:focus { border-color: var(--primary-soft); outline: none; }
    .toggle-password { position: absolute; right: 18px; top: 45px; cursor: pointer; color: var(--primary-soft); font-weight: 800; font-size: 0.8rem; text-transform: uppercase; }
    .btn-login { width: 100%; background: var(--primary-soft); color: white; border: none; padding: 16px; border-radius: 18px; font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: 0.4s; box-shadow: 0 10px 20px rgba(108, 92, 231, 0.3); margin-top: 10px; }
    .btn-login:hover { background: #5b4bc4; transform: translateY(-3px); }
    
    /* Perubahan di sini: justify-content diketengah */
    .links-container { display: flex; justify-content: center; margin-top: 25px; font-size: 0.85rem; }
    .links-container a { color: var(--primary-soft); text-decoration: none; font-weight: 700; }
    
    .back-link { display: inline-block; margin-top: 30px; color: #636e72; text-decoration: none; font-weight: 700; font-size: 0.9rem; }
    footer { position: fixed; bottom: 0; text-align: center; width: 100%; color: var(--text-dark); font-size: 0.85rem; padding: 20px; font-weight: 600; }
  </style>
</head>
<body>
<div class="login-wrapper" data-aos="zoom-in">
    <div class="login-container">
        <h2>Selamat Datang</h2>
        <p class="subtitle">Sila log masuk ke akaun anda</p>
        <form action="" method="POST">
            <div class="input-group">
                <label>Nama Pengguna</label>
                <input type="text" name="username" placeholder="Nama pengguna" required>
            </div>
            <div class="input-group">
                <label>Kata Laluan</label>
                <input type="password" id="password" name="password" placeholder="Kata laluan" required>
                <span class="toggle-password" id="togglePassword">Lihat</span>
            </div>
            <button type="submit" class="btn-login">Log Masuk Sekarang</button>
            <div class="links-container">
                <a href="daftar.php">Daftar Akaun Baru</a>
            </div>
        </form>
        <a href="index.php" class="back-link">← Kembali ke Utama</a>
    </div>
</div>
<footer>
     &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: true });
  const passwordInput = document.getElementById('password');
  const togglePassword = document.getElementById('togglePassword');
  togglePassword.addEventListener('click', () => {
      if(passwordInput.type === 'password'){
          passwordInput.type = 'text';
          togglePassword.textContent = 'Sembunyi';
      } else {
          passwordInput.type = 'password';
          togglePassword.textContent = 'Lihat';
      }
  });
</script>
</body>
</html>

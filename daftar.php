<?php
session_start();
include 'config.php'; 

error_reporting(E_ALL); 
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm  = isset($_POST['confirm']) ? $_POST['confirm'] : '';
    $peranan  = isset($_POST['peranan']) ? strtolower($_POST['peranan']) : ''; 
    $emel     = isset($_POST['emel']) ? trim($_POST['emel']) : '';
    $nama     = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $svm      = isset($_POST['svm']) ? $_POST['svm'] : '';
    $semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    $kursus_pelajar_kod = isset($_POST['kursus_single']) ? trim($_POST['kursus_single']) : ''; 

    // 1. Semak semua medan wajib
    if ($username === '' || $password === '' || $confirm === '' || $peranan === '' || $emel === '' || $nama === '' || $svm === '' || $semester === '') {
        echo "<script>alert('Sila lengkapkan semua medan yang wajib.'); window.history.back();</script>";
        exit();
    }

    // 2. Semak Kata Laluan & Username
    if ($password !== $confirm) {
        echo "<script>alert('Kata laluan dan sahkan kata laluan tidak sama!'); window.history.back();</script>";
        exit();
    }

    $check = $conn->prepare("SELECT id FROM pengguna WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
        exit();
    }
    $check->close();

    // 3. PENGESAHAN EMEL PENSYARAH
    if ($peranan == "pensyarah") {
        $required_domain = '@moe-dl.edu.my';
        $required_start = 'g-';
        $email_start = substr($emel, 0, 2);
        $email_domain = substr($emel, -strlen($required_domain));

        if ($email_start !== $required_start || $email_domain !== $required_domain) {
            echo "<script>alert('Pendaftaran Pensyarah memerlukan emel yang bermula dengan \"g-\" dan menggunakan domain $required_domain.'); window.history.back();</script>";
            exit();
        }
    }
    
    // PEMBAIKAN: Menambah 'kod_kursus' dan 'gambar' ke dalam INSERT
    $val_kosong = "";
    $sql_pengguna = $conn->prepare("INSERT INTO pengguna (username, password, peranan, emel, svm, semester, gambar, kod_kursus) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($sql_pengguna === false) {
         echo "<script>alert('Ralat SQL! Sila semak semula struktur jadual `pengguna`. Ralat: " . $conn->error . "'); window.history.back();</script>";
         exit();
    }
    
    // Bind 8 parameter (8 kali "s")
    $sql_pengguna->bind_param("ssssssss", $username, $password, $peranan, $emel, $svm, $semester, $val_kosong, $val_kosong);

    if ($sql_pengguna->execute()) {
        $pengguna_id = $conn->insert_id; 
        
        if ($peranan == "pensyarah") { 
            $kursus_dipilih_kod = isset($_POST['kursus']) ? (array)$_POST['kursus'] : [];

            if (count($kursus_dipilih_kod) == 0) {
                 $conn->query("DELETE FROM pengguna WHERE id = $pengguna_id");
                 echo "<script>alert('Sila pilih sekurang-kurangnya satu kursus untuk Pensyarah.'); window.history.back();</script>";
                 exit();
            }

            $placeholders = implode(',', array_fill(0, count($kursus_dipilih_kod), '?'));
            $sql_dapatkan_kursus_id = "SELECT id FROM kursus WHERE kod_kursus IN ($placeholders)";
            $stmt_kursus_id = $conn->prepare($sql_dapatkan_kursus_id);
            
            $types = str_repeat('s', count($kursus_dipilih_kod));
            $stmt_kursus_id->bind_param($types, ...$kursus_dipilih_kod);
            
            $stmt_kursus_id->execute();
            $result_kursus_id = $stmt_kursus_id->get_result();
            $kursus_id_array = $result_kursus_id->fetch_all(MYSQLI_ASSOC);
            $stmt_kursus_id->close();

            $sql_insert_ajar = $conn->prepare("INSERT INTO pengajaran_kursus (pensyarah_id, kursus_id) VALUES (?, ?)");
            
            foreach ($kursus_id_array as $kursus) {
                $kursus_id = $kursus['id'];
                $sql_insert_ajar->bind_param("ii", $pengguna_id, $kursus_id);
                $sql_insert_ajar->execute();
            }
            $sql_insert_ajar->close();

        } else {
            if (empty($kursus_pelajar_kod)) {
                 $conn->query("DELETE FROM pengguna WHERE id = $pengguna_id");
                 echo "<script>alert('Sila pilih kursus untuk Pelajar.'); window.history.back();</script>";
                 exit();
            }
            
            $stmt_kursus_id_pelajar = $conn->prepare("SELECT id FROM kursus WHERE kod_kursus = ?");
            $stmt_kursus_id_pelajar->bind_param('s', $kursus_pelajar_kod);
            $stmt_kursus_id_pelajar->execute();
            $result_kursus_id_pelajar = $stmt_kursus_id_pelajar->get_result();
            $kursus_data = $result_kursus_id_pelajar->fetch_assoc();
            $kursus_id_pelajar = $kursus_data['id'] ?? null;
            $stmt_kursus_id_pelajar->close();

            if ($kursus_id_pelajar) {
                 $sql_insert_daftar = $conn->prepare("INSERT INTO pendaftaran_kursus (pelajar_id, kursus_id) VALUES (?, ?)");
                 $sql_insert_daftar->bind_param("ii", $pengguna_id, $kursus_id_pelajar);
                 $sql_insert_daftar->execute();
                 $sql_insert_daftar->close();
            } else {
                 $conn->query("DELETE FROM pengguna WHERE id = $pengguna_id");
                 echo "<script>alert('Kursus tidak sah.'); window.history.back();</script>";
                 exit();
            }
        }

        echo "<script>alert('Pendaftaran berjaya!'); window.location='logmasuk.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal! Ralat: " . $conn->error . "'); window.history.back();</script>";
    }
    $sql_pengguna->close();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar|Sains KuVocC</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <style>
    :root {
      --primary-soft: #6c5ce7;
      --secondary-soft: #a29bfe;
      --text-dark: #2d3436;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }

    body { 
      min-height: 100vh; 
      display: flex; 
      flex-direction: column;
      background: url('bg.png') no-repeat center center fixed; 
      background-size: cover;
      justify-content: center;
      align-items: center;
      padding: 50px 0;
    }

    .login-wrapper {
      width: 100%;
      max-width: 500px;
      padding: 20px;
      z-index: 10;
    }

    .login-container { 
      background: rgba(255, 255, 255, 0.85); 
      backdrop-filter: blur(15px);
      padding: 50px 40px; 
      border-radius: 30px; 
      box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
      text-align: center; 
      border: 1px solid rgba(255, 255, 255, 0.5);
    }

    h2 { 
      color: var(--text-dark); 
      margin-bottom: 10px; 
      font-size: 2.2rem; 
      font-weight: 800;
      letter-spacing: -1px;
    }

    p.subtitle {
      color: #636e72;
      margin-bottom: 30px;
      font-size: 1rem;
    }

    .input-group { 
      text-align: left; 
      margin-bottom: 20px; 
      position: relative; 
    }

    .input-group label { 
      font-weight: 700; 
      color: var(--text-dark); 
      display: block; 
      margin-bottom: 8px; 
      font-size: 0.9rem;
      margin-left: 5px;
    }

    .input-group input, .input-group select { 
      width: 100%; 
      padding: 15px 20px; 
      border: 2px solid transparent; 
      border-radius: 15px; 
      font-size: 1rem; 
      background: white;
      transition: 0.3s; 
      box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .input-group input:focus, .input-group select:focus { 
      border-color: var(--primary-soft); 
      outline: none; 
      background: #fff;
    }

    .kursus-box {
      border: 2px solid #eee; 
      padding: 20px; 
      border-radius: 15px; 
      background: white; 
      margin-top: 10px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      text-align: left;
    }
    .kursus-item {
      display: flex; 
      align-items: center; 
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
    }
    .kursus-item input[type="checkbox"] {
      width: auto;
      margin-right: 10px;
      transform: scale(1.2);
    }

    .toggle-password { 
      position: absolute; 
      right: 18px; 
      top: 45px; 
      cursor: pointer; 
      color: var(--primary-soft); 
      font-weight: 800; 
      font-size: 0.8rem; 
      text-transform: uppercase;
      user-select: none; 
    }

    .btn-login { 
      width: 100%; 
      background: var(--primary-soft); 
      color: white; 
      border: none; 
      padding: 16px; 
      border-radius: 18px; 
      font-size: 1.1rem; 
      font-weight: 800; 
      cursor: pointer; 
      transition: 0.4s; 
      box-shadow: 0 10px 20px rgba(108, 92, 231, 0.3);
      margin-top: 20px;
    }

    .btn-login:hover { 
      background: #5b4bc4; 
      transform: translateY(-3px);
      box-shadow: 0 15px 25px rgba(108, 92, 231, 0.4);
    }

    .links-container { 
      margin-top: 25px; 
      font-size: 0.85rem;
      color: #636e72;
    }

    .links-container a { 
      color: var(--primary-soft); 
      text-decoration: none; 
      font-weight: 700; 
    }

    .back-link { 
      display: inline-block; 
      margin-top: 30px; 
      color: #636e72; 
      text-decoration: none; 
      font-weight: 700; 
      font-size: 0.9rem;
      transition: 0.3s;
    }
    
    .back-link:hover { color: var(--primary-soft); transform: translateX(-5px); }

    footer { 
      text-align: center;
      width: 100%; 
      color: var(--text-dark);
      font-size: 0.85rem; 
      padding: 30px 20px;
      font-weight: 600;
    }

    .small-note {
      font-size: 0.8rem;
      color: #d9534f;
      margin-top: 8px;
      font-weight: 600;
      display: block;
      text-align: left;
    }
  </style>
</head>
<body>

<div class="login-wrapper" data-aos="zoom-in">
    <div class="login-container">
        
        <h2>Daftar Akaun</h2>
        <p class="subtitle">Sertai E-pembelajaran Sains Sekarang</p>

        <form id="daftarForm" action="" method="POST">
            
            <div class="input-group">
                <label for="nama">Nama Penuh</label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama penuh" required>
            </div>

            <div class="input-group">
                <label for="username">Nama Pengguna</label>
                <input type="text" id="username" name="username" placeholder="Masukkan nama pengguna" required>
            </div>

            <div class="input-group">
                <label for="katalaluan">Kata Laluan</label>
                <input type="password" id="katalaluan" name="password" placeholder="Masukkan kata laluan" required>
                <span class="toggle-password" onclick="togglePassword('katalaluan', this)">Lihat</span>
            </div>

            <div class="input-group">
                <label for="sahpassword">Sahkan Kata Laluan</label>
                <input type="password" id="sahpassword" name="confirm" placeholder="Taip semula kata laluan" required>
                <span class="toggle-password" onclick="togglePassword('sahpassword', this)">Lihat</span>
            </div>

            <div class="input-group">
                <label for="emel">E-mel</label>
                <input type="email" id="emel" name="emel" placeholder="Sila masukkan e-mel" required>
                <span class="small-note" id="email-note" style="display:none;">
                    * Sila masukkan e-mel DELIMa
                </span>
            </div>

            <div class="input-group">
                <label for="peranan">Peranan</label>
                <select id="peranan" name="peranan" required>
                    <option value="" disabled selected>- Pilih Peranan -</option>
                    <option value="pelajar">Pelajar</option>
                    <option value="pensyarah">Pensyarah</option>
                </select>
            </div>

            <div class="input-group">
                <label for="kursus">Kursus</label>
                <select id="kursus_single" name="kursus_single" required>
                    <option value="" disabled selected>- Pilih Kursus -</option>
                    <option value="ISK">ISK</option>
                    <option value="PPU">PPU</option>
                    <option value="ETE">ETE</option>
                    <option value="ETN">ETN</option>
                    <option value="WTP">WTP</option>
                    <option value="MTA">MTA</option>
                    <option value="MTK">MTK</option>
                    <option value="MPI">MPI</option>
                </select>

                <div id="kursus_multi" style="display:none;">
                    <div class="kursus-box">
                        <?php 
                        $list_kursus = ["ISK", "PPU", "ETE", "ETN", "WTP", "MTA", "MTK", "MPI"];
                        foreach($list_kursus as $k): ?>
                        <label class="kursus-item">
                            <input type="checkbox" name="kursus[]" value="<?php echo $k; ?>"> 
                            <span class="kursus-code"><?php echo $k; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="input-group">
                <label for="svm">SVM</label>
                <select id="svm" name="svm" required>
                    <option value="" disabled selected>- Pilih SVM -</option>
                    <option value="SVM 1">SVM 1</option>
                </select>
            </div>

            <div class="input-group">
                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <option value="" disabled selected>- Pilih Semester -</option>
                    <option value="Semester 1">Semester 1</option>
                </select>
            </div>

            <button type="submit" class="btn-login">Daftar Akaun Baru</button>

            <div class="links-container">
                Sudah mempunyai akaun? <a href="logmasuk.php">Log Masuk</a>
            </div>
        </form>

        <a href="index1.php" class="back-link">← Kembali ke Utama</a>
    </div>
</div>

<footer>
    &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    function togglePassword(id, elem) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            elem.textContent = "Sembunyi";
        } else {
            input.type = "password";
            elem.textContent = "Lihat";
        }
    }

    document.getElementById("peranan").addEventListener("change", function(){
        let pr = this.value;
        let kursus_single = document.getElementById("kursus_single");
        let kursus_multi = document.getElementById("kursus_multi");
        let email_note = document.getElementById("email-note");

        if(pr === "pensyarah"){
            kursus_single.style.display = "none";
            kursus_single.required = false;
            kursus_multi.style.display = "block";
            email_note.style.display = "block"; 
        } else {
            kursus_multi.style.display = "none";
            email_note.style.display = "none"; 
            kursus_single.style.display = "block";
            kursus_single.required = true;
        }
    });
</script>

</body>
</html>



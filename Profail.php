<?php
session_start();
include 'config.php';

// *** PENTING: MENDAYAKAN PAPARAN RALAT ***
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id = $_SESSION['id'];
$peranan = $_SESSION['peranan'] ?? '';

// --- KEMAS KINI SELECT untuk mendapatkan GAMBAR ---
$sql = $conn->prepare("SELECT username, emel, peranan, svm, semester, gambar FROM pengguna WHERE id = ?");

// SEMAK RALAT SELEPAS PREPARE
if ($sql === false) {
    die("Ralat SQL Kritikal dalam Profail: Gagal menyediakan query pengguna. Semak lajur 'gambar' wujud. Ralat: " . $conn->error);
}

$sql->bind_param("i", $id);
$sql->execute();
$data = $sql->get_result()->fetch_assoc();
$sql->close();

// --- LOGIK GAMBAR PROFAIL (Diselaraskan dengan index.php) ---
$gambar_default = "profile.jpg"; 
if (!empty($data['gambar']) && file_exists("uploads/" . $data['gambar'])) {
    $gambar_path = "uploads/" . $data['gambar']; 
} else {
    $gambar_path = $gambar_default; 
}

// --- Logik Kursus ---
$kursus_paparan = "Tiada Kursus Ditetapkan";

if ($peranan == "pelajar") {
    $stmt_kursus = $conn->prepare("
        SELECT k.kod_kursus FROM kursus k 
        JOIN pendaftaran_kursus pdk ON k.id = pdk.kursus_id 
        WHERE pdk.pelajar_id = ?
    ");
    $stmt_kursus->bind_param("i", $id);
    $stmt_kursus->execute();
    $result_kursus = $stmt_kursus->get_result();
    if ($row = $result_kursus->fetch_assoc()) {
        $kursus_paparan = $row['kod_kursus'] ."";
    }
    $stmt_kursus->close();
    
} else if ($peranan == "pensyarah") {
    $stmt_kursus = $conn->prepare("
        SELECT k.kod_kursus FROM kursus k 
        JOIN pengajaran_kursus pk ON k.id = pk.kursus_id 
        WHERE pk.pensyarah_id = ?
    ");
    $stmt_kursus->bind_param("i", $id);
    $stmt_kursus->execute();
    $result_kursus = $stmt_kursus->get_result();
    
    $list_kursus = [];
    while ($row = $result_kursus->fetch_assoc()) {
        $list_kursus[] = $row['kod_kursus'];
    }
    $stmt_kursus->close();

    if (!empty($list_kursus)) {
        $kursus_paparan = implode(", ", $list_kursus) . "";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Pengguna|Sains KuVocC</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    :root {
        --primary-soft: #6c5ce7;
        --secondary-soft: #a29bfe;
        --text-dark: #2d3436;
        --glass-white: rgba(255, 255, 255, 0.85);
        --gov-blue: #1a1a7c;
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
        transition: filter 0.3s ease;
    }

    /* --- CSS AKSESIBILITI (Sama seperti nota.php) --- */
    .acc-wrapper {
        position: fixed;
        right: 25px;
        top: 120px;
        z-index: 2000;
    }

    .acc-button {
        background: var(--primary-soft);
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4);
        transition: 0.3s;
    }

    .acc-button:hover { transform: scale(1.1); background: var(--gov-blue); }
    .acc-button img { width: 30px; filter: invert(1); }

    .acc-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 65px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        width: 250px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.5);
        overflow: hidden;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .acc-menu.active { display: block; }

    .acc-menu-header {
        padding: 15px 20px;
        font-weight: 800;
        background: var(--primary-soft);
        color: white;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
    }

    .acc-item {
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #444;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: 0.2s;
    }

    .acc-item:hover { background: rgba(108, 92, 231, 0.1); color: var(--primary-soft); padding-left: 25px; }
    .acc-item img { width: 20px; height: 20px; object-fit: contain; }

    html.grayscale { filter: grayscale(100%) !important; }
    html.negative-contrast { filter: invert(100%) hue-rotate(180deg) !important; }
    
    .high-contrast { background: #000 !important; color: #ffff00 !important; }
    .high-contrast * { color: #ffff00 !important; border-color: #ffff00 !important; }

    .profile-wrapper {
        width: 100%;
        max-width: 450px;
        padding: 20px;
        z-index: 10;
    }

    .container { 
        background: rgba(255, 255, 255, 0.85); 
        backdrop-filter: blur(15px);
        padding: 50px 40px; 
        border-radius: 30px; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        text-align: center; 
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .img-container { 
        position: relative; 
        width: 140px; 
        height: 140px;
        margin: 0 auto 25px; 
        cursor: pointer; 
    }

    .profile-pic { 
        width: 140px; 
        height: 140px; 
        border-radius: 50%; 
        object-fit: cover; 
        border: 5px solid white;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: 0.3s;
    }

    .img-container:hover .profile-pic {
        transform: scale(1.05);
        filter: brightness(85%);
    }

    .camera-overlay {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--primary-soft);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .camera-overlay img { width: 18px; }

    h2 { 
        color: var(--text-dark); 
        margin-bottom: 5px; 
        font-size: 1.8rem; 
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .role-badge { 
        display: inline-block;
        background: rgba(108, 92, 231, 0.1);
        color: var(--primary-soft);
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 30px;
    }

    .info-group { text-align: left; margin-bottom: 15px; }

    .input-box { 
        width: 100%; 
        padding: 15px; 
        margin-bottom: 15px;
        border: 2px solid transparent; 
        border-radius: 15px; 
        font-size: 1rem; 
        background: white;
        color: #555;
        font-weight: 600;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .btn { 
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
        margin-top: 10px;
    }

    .btn:hover { 
        background: #5b4bc4; 
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(108, 92, 231, 0.4);
    }

    .back-link { 
        display: inline-block; 
        margin-top: 25px; 
        color: #636e72; 
        text-decoration: none; 
        font-weight: 700; 
        font-size: 0.9rem;
        transition: 0.3s;
    }
    
    .back-link:hover { color: var(--primary-soft); transform: translateX(-5px); }

    footer { 
        position: fixed;
        bottom: 0;
        text-align: center;
        width: 100%; 
        color: #000000;
        font-size: 0.85rem; 
        padding: 20px;
        font-weight: 600;
    }

    .hidden { display: none; }
</style>
</head>
<body>

<div class="acc-wrapper">
    <div class="acc-button" onclick="toggleAccMenu()">
        <img src="tools.png" alt="Aksesibilitas">
    </div>
    <div class="acc-menu" id="accMenu">
        <div class="acc-menu-header">Alat Aksesibilitas</div>
        <div class="acc-item" onclick="adjustFont(10)"><img src="besar.png"> Besarkan teks</div>
        <div class="acc-item" onclick="adjustFont(-10)"><img src="kecil.png"> Kecilkan teks</div>
        <div class="acc-item" onclick="setEffect('grayscale')"><img src="gray.png"> Mod Kelabu</div>
        <div class="acc-item" onclick="setEffect('high-contrast')"><img src="high.png"> Kontras Tinggi</div>
        <div class="acc-item" onclick="setEffect('negative-contrast')"><img src="negatif.png"> Kontras Negatif</div>
        <div class="acc-item" onclick="resetAcc()" style="color: #e74c3c; border:none; justify-content:center;">
            <img src="reset.png"> Reset
        </div>
    </div>
</div>

<div class="profile-wrapper" data-aos="zoom-in">
    <div class="container">
        
        <div class="img-container" onclick="document.getElementById('uploadGambar').click();">
            <img src="<?php echo $gambar_path; ?>" class="profile-pic"> 
            <div class="camera-overlay">
                <img src="camera.png" alt="Edit"> 
            </div>
        </div>

        <form action="muatnaik.php" method="POST" enctype="multipart/form-data" class="hidden">
            <input type="file" name="gambar" id="uploadGambar" onchange="this.form.submit()">
        </form>

        <h2><?php echo strtoupper($data['username']); ?></h2>
        <div class="role-badge"><?php echo ucwords($data['peranan']); ?></div>

        <div class="info-group">
            <input class="input-box" value="<?php echo $kursus_paparan; ?>" readonly>
            <input class="input-box" value="<?php echo $data['svm'] . ' ' . $data['semester']; ?>" readonly>
        </div>

        <form action="logkeluar.php" method="POST">
            <button class="btn">Log Keluar</button>
        </form>

        <a href="index.php" class="back-link">← Kembali ke Utama</a>
    </div>
</div>

<footer>
    © 2026 Sains SVM 1 KVK | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: true });

  // SCRIPT AKSESIBILITI
  function toggleAccMenu() { document.getElementById('accMenu').classList.toggle('active'); }

  let zoom = 100;
  function adjustFont(amount) {
      zoom += amount;
      document.documentElement.style.fontSize = zoom + "%";
  }

  function setEffect(effectClass) {
      document.documentElement.classList.remove('grayscale', 'negative-contrast');
      document.body.classList.remove('high-contrast');
      if (effectClass === 'grayscale' || effectClass === 'negative-contrast') {
          document.documentElement.classList.add(effectClass);
      } else if (effectClass === 'high-contrast') {
          document.body.classList.add(effectClass);
      }
  }

  function resetAcc() {
      zoom = 100;
      document.documentElement.style.fontSize = "100%";
      document.documentElement.className = "";
      document.body.className = "";
  }

  window.onclick = function(event) {
      if (!event.target.closest('.acc-wrapper')) {
          document.getElementById('accMenu').classList.remove('active');
      }
  }
</script>

</body>
</html>

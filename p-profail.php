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

// Menentukan pautan kembali berdasarkan peranan
$back_link = ($peranan == 'pensyarah') ? 'pensyarah.php' : 'index.php';
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
    }

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

        <a href="pensyarah.php"<?php echo $back_link; ?> class="back-link">← Kembali ke Utama</a>
    </div>
</div>

<footer>
    © 2026 Sains SVM 1 KVK | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ duration: 1000, once: true });
</script>

</body>
</html>



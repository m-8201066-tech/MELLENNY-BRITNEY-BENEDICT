<?php
session_start();
include 'config.php';

// 1. Tetapkan gambar default pada awal kod
$gambar_default = "profile.jpg"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $sql = $conn->prepare("SELECT gambar FROM pengguna WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $data = $sql->get_result()->fetch_assoc();

    // 2. Semak jika data gambar profil wujud di folder uploads
    if (!empty($data['gambar']) && file_exists("uploads/" . $data['gambar'])) {
        $gambar_path = "uploads/" . $data['gambar']; 
    } else {
        $gambar_path = $gambar_default; 
    }
} else {
    $gambar_path = $gambar_default;
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nota Sains | Sains SVM 1</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <style>
        :root {
            --primary-soft: #6c5ce7;
            --secondary-soft: #a29bfe;
            --text-dark: #2d3436;
        }

        /* --- SEKATAN KESELAMATAN (ANTI-PRINT & COPY) --- */
        @media print {
            body { display: none !important; }
        }

        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background: url('bg.png') no-repeat center center fixed; 
            background-size: cover;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px);
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        header img.logo-img { height: 50px; border-radius: 8px; }
        nav { display: flex; gap: 25px; }
        nav a { text-decoration: none; color: var(--text-dark); font-weight: 800; font-size: 0.95rem; }
        .profile-btn img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid white; }

        main { flex: 1; padding: 50px 8%; }
        .back-link img { width: 50px; transition: 0.3s; }
        .back-link:hover { transform: translateX(-8px); }

        .page-header { text-align: center; margin-bottom: 50px; }
        h1 { font-size: 3rem; font-weight: 800; margin-bottom: 10px; }

        .container {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px; max-width: 1200px; margin: 0 auto;
        }

        .box {
            background: white; padding: 40px 30px; border-radius: 30px;
            text-decoration: none; color: var(--text-dark);
            display: flex; flex-direction: column; align-items: center;
            transition: 0.4s; border: 1px solid rgba(0,0,0,0.05);
            position: relative; overflow: hidden;
            text-align: center;
        }
        .box::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: var(--primary-soft); }
        .icon-circle { width: 100px; height: 100px; background: #f0edff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .icon-circle img { width: 70%; }
        .box h2 { color: var(--primary-soft); margin-bottom: 10px; }
        .box:hover { transform: translateY(-15px); box-shadow: 0 20px 40px rgba(108, 92, 231, 0.15); }

        footer { text-align: center; padding: 40px; color: #636e72; font-weight: 600; }

        /* AKSESIBILITI */
        .acc-wrapper { position: fixed; right: 25px; top: 120px; z-index: 2000; }
        .acc-button { background: var(--primary-soft); width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .acc-menu { display: none; position: absolute; right: 0; top: 65px; background: white; width: 200px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .acc-menu.active { display: block; }
        .acc-item { padding: 15px; cursor: pointer; font-size: 14px; font-weight: 600; border-bottom: 1px solid #f0f0f0; }
  </style>
</head>

<body oncontextmenu="return false;"> <div class="acc-wrapper">
    <div class="acc-button" onclick="toggleAccMenu()">
        <img src="tools.png" alt="Aksesibilitas" style="width:30px; filter:invert(1);">
    </div>
    <div class="acc-menu" id="accMenu">
        <div class="acc-item" onclick="adjustFont(10)">Besarkan Teks</div>
        <div class="acc-item" onclick="adjustFont(-10)">Kecilkan Teks</div>
        <div class="acc-item" onclick="resetAcc()" style="color:red;">Reset</div>
    </div>
</div>

<header>
    <a href="pensyarah.php"><img src="Logo.png" alt="Logo" class="logo-img"></a>
    <nav>
        <a href="pensyarah.php">Utama</a>
        <a href="p.nota.php">Nota</a>
        <a href="r-pelajar.php">Rekod</a>
    </nav>
    <div class="user-area">
        <?php if(isset($_SESSION['id'])) { ?>
            <a href="p-profail.php" class="profile-btn"><img src="<?php echo $gambar_path; ?>"></a>
        <?php } ?>
    </div>
</header>

<main>
    <a href="pensyarah.php" class="back-link"><img src="back.png" alt="Back"></a>

    <div class="page-header" data-aos="fade-up">
        <h1>Nota Sains</h1>
        <p>Modul SVM Semester 1 (Sekatan Muat Turun Aktif)</p>
    </div>

    <div class="container">
        <a href="Langkah Keselamatan Dalam Makmal.pdf#toolbar=0" target="_blank" class="box" data-aos="fade-up">
            <div class="icon-circle"><img src="safety.png"></div>
            <h2>Bab 1</h2>
            <p>Langkah Keselamatan dalam Makmal</p>
        </a>

        <a href="Bantuan Kecemasan.pdf#toolbar=0" target="_blank" class="box" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-circle"><img src="cpr.png"></div>
            <h2>Bab 2</h2>
            <p>Bantuan Kecemasan</p>
        </a>

        <a href="Teknik Mengukur Parameter Kesihatan Badan.pdf#toolbar=0" target="_blank" class="box" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-circle"><img src="suhu.png"></div>
            <h2>Bab 3</h2>
            <p>Teknik Mengukur Parameter Kesihatan Badan</p>
        </a>
    </div>
</main>

<footer>
    &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    // 1. SEKAT KEY

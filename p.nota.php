<?php
session_start();
include 'config.php';

// 1. Tetapkan gambar default pada awal kod (Sama seperti index.php)
$gambar_default = "profile.jpg"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $sql = $conn->prepare("SELECT gambar FROM pengguna WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $data = $sql->get_result()->fetch_assoc();

    // 2. Semak jika data gambar wujud DALAM pangkalan data DAN fail wujud di folder uploads
    if (!empty($data['gambar']) && file_exists("uploads/" . $data['gambar'])) {
        $gambar_path = "uploads/" . $data['gambar']; 
    } else {
        // Jika syarat gagal, guna profile.jpg
        $gambar_path = $gambar_default; 
    }
} else {
    // Jika tidak log masuk, guna profile.jpg
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
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --text-dark: #2d3436;
            --glass-white: rgba(255, 255, 255, 0.85);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }

        body {
            background: url('bg.png') no-repeat center center fixed; 
            background-size: cover;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: filter 0.3s ease; /* Transisi untuk kesan aksesibiliti */
        }

        /* --- STYLES AKSESIBILITI (SAMA SEPERTI KUIZ1.PHP) --- */
        .acc-wrapper { position: fixed; right: 25px; top: 120px; z-index: 2000; }
        .acc-button {
            background: var(--primary-soft); width: 55px; height: 55px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.4); transition: 0.3s;
        }
        .acc-button img { width: 30px; filter: invert(1); }
        .acc-menu {
            display: none; position: absolute; right: 0; top: 65px;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            width: 250px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.5); overflow: hidden; animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .acc-menu.active { display: block; }
        .acc-menu-header { padding: 15px 20px; font-weight: 800; background: var(--primary-soft); color: white; font-size: 14px; text-transform: uppercase; text-align: center; }
        .acc-item { padding: 14px 20px; display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; font-weight: 600; color: #444; border-bottom: 1px solid rgba(0,0,0,0.05); text-align: left; }
        .acc-item:hover { background: rgba(108, 92, 231, 0.1); color: var(--primary-soft); }
        .acc-item img { width: 20px; height: 20px; object-fit: contain; }

        html.grayscale { filter: grayscale(100%) !important; }
        html.negative-contrast { filter: invert(100%) hue-rotate(180deg) !important; }
        .high-contrast { background: #000 !important; color: #ffff00 !important; }
        .high-contrast * { color: #ffff00 !important; border-color: #ffff00 !important; background-image: none !important; }
        /* -------------------------------------------------- */

        /* HEADER */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 8%;
            background: rgb(255, 255, 255);
            backdrop-filter: blur(15px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        header img.logo-img { height: 50px; border-radius: 8px; }

        .header-right { display: flex; align-items: center; gap: 30px; }

        nav { display: flex; gap: 25px; }
        nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 800; 
            font-size: 0.95rem;
            transition: 0.3s;
        }

        nav a:hover { color: var(--primary-soft); }

        .btn-login {
            background: var(--primary-soft);
            color: white;
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.2);
        }

        .profile-btn img {
            width: 45px; height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* MAIN SECTION */
        main { flex: 1; padding: 50px 8%; position: relative; }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .back-link img {
            width: 50px; 
            height: auto;
            transition: 0.3s;
        }

        .back-link:hover {
            transform: translateX(-8px); 
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(to right, #000000, #000000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 1.1rem;
            color: #636e72;
            font-weight: 500;
        }

        /* NOTA GRID */
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .box {
            background: white;
            padding: 40px 30px;
            border-radius: 30px;
            text-decoration: none;
            color: var(--text-dark);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: var(--primary-soft);
            transition: 0.3s;
        }

        .box .icon-circle {
            width: 100px;
            height: 100px;
            background: #f0edff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden; 
            transition: 0.4s;
        }

        .box .icon-circle img {
            width: 70%; 
            height: 70%;
            object-fit: contain; 
        }

        .box h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--primary-soft);
        }

        .box p {
            font-size: 1rem;
            line-height: 1.5;
            color: #636e72;
            font-weight: 500;
        }

        .box:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(108, 92, 231, 0.15);
            background: #fdfdff;
            border-color: var(--secondary-soft);
        }

        .box:hover .icon-circle {
            transform: scale(1.1) rotate(5deg);
            background: var(--primary-soft);
        }

        footer {
            text-align: center;
            padding: 40px;
            background: transparent;
            color: #636e72;
            font-weight: 600;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            header { padding: 15px 5%; }
            nav { display: none; }
            h1 { font-size: 2.2rem; }
            main { padding: 30px 5%; }
        }
    </style>
</head>
<body>

<div class="acc-wrapper">
    <div class="acc-button" onclick="toggleAccMenu()" title="Pilihan Aksesibilitas">
        <img src="tools.png" alt="Aksesibilitas">
    </div>
    <div class="acc-menu" id="accMenu">
        <div class="acc-menu-header">Alat Aksesibilitas</div>
        <div class="acc-item" onclick="adjustFont(10)">
            <img src="besar.png" alt="Zoom In"> Besarkan teks
        </div>
        <div class="acc-item" onclick="adjustFont(-10)">
            <img src="kecil.png" alt="Zoom Out"> Kecilkan teks
        </div>
        <div class="acc-item" onclick="setEffect('grayscale')">
            <img src="gray.png" alt="Grayscale"> Mod Kelabu
        </div>
        <div class="acc-item" onclick="setEffect('high-contrast')">
            <img src="high.png" alt="Contrast"> Kontras Tinggi
        </div>
        <div class="acc-item" onclick="setEffect('negative-contrast')">
            <img src="negatif.png" alt="Negative"> Kontras Negatif
        </div>
        <div class="acc-item" onclick="resetAcc()" style="color: #e74c3c; border-bottom: none; justify-content: center; font-weight: 800;">
            <img src="reset.png" alt="Reset"> Reset
        </div>
    </div>
</div>

<header>
    <a href="pensyarah.php">
        <img src="Logo.png" alt="Logo" class="logo-img">
    </a>

    <div class="header-right">
        <nav>
            <a href="pensyarah.php">Utama</a>
            <a href="p.nota.php">Nota</a>

            <a href="r-pelajar.php">Rekod</a>
        </nav>

        <div class="user-area">
            <?php if(isset($_SESSION['id'])) { ?>
                <a href="p-profail.php" class="profile-btn">
                    <img src="<?php echo $gambar_path; ?>" alt="Profil">
                </a>
            <?php } else { ?>
                <a href="logmasuk.php" class="btn-login">Log Masuk</a>
            <?php } ?>
        </div>
    </div>
</header>

<main>
    <a href="pensyarah.php" class="back-link" data-aos="fade-right">
        <img src="back.png" alt="Back">
    </a>

    <div class="page-header" data-aos="fade-up">
        <h1>Nota Sains</h1>
        <p class="subtitle">Modul Pembelajaran SVM Semester 1</p>
    </div>

    <div class="container">
        <a href="Langkah Keselamatan Dalam Makmal.pdf" class="box" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-circle">
                <img src="safety.png" alt="Bab 1"> </div>
            <h2>Bab 1</h2>
            <p>Langkah Keselamatan dalam Makmal</p>
        </a>

        <a href="Bantuan Kecemasan.pdf" class="box" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-circle">
                <img src="cpr.png" alt="Bab 2"> </div>
            <h2>Bab 2</h2>
            <p>Bantuan Kecemasan</p>
        </a>

        <a href="Teknik Mengukur Parameter Kesihatan Badan.pdf" class="box" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-circle">
                <img src="suhu.png" alt="Bab 3"> </div>
            <h2>Bab 3</h2>
            <p>Teknik Mengukur Parameter Kesihatan Badan</p>
        </a>
    </div>
</main>

<footer data-aos="fade-up">
    &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 1000,
    once: true,
  });

  // --- JAVASCRIPT AKSESIBILITI (SAMA SEPERTI KUIZ1.PHP) ---
  function toggleAccMenu() {
      document.getElementById('accMenu').classList.toggle('active');
  }

  let zoom = 100;
  function adjustFont(amount) {
      zoom += amount;
      if(zoom < 70) zoom = 70;
      if(zoom > 150) zoom = 150;
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

  // Menutup menu jika klik di luar
  window.onclick = function(event) {
      if (!event.target.closest('.acc-wrapper')) {
          let menu = document.getElementById('accMenu');
          if(menu && menu.classList.contains('active')) {
              menu.classList.remove('active');
          }
      }
  }
</script>

</body>
</html>

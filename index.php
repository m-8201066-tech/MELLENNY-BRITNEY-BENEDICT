<?php
session_start();
include 'config.php';

// 1. Tetapkan gambar default pada awal kod
$gambar = "profile.jpg"; 

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
        // Jika salah satu syarat gagal, guna profile.jpg
        $gambar_path = $gambar; 
    }
} else {
    // Jika tidak log masuk, guna profile.jpg
    $gambar_path = $gambar;
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Utama|Sains KuVocC</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-soft: #6c5ce7;
      --secondary-soft: #a29bfe;
      --accent-soft: #fab1a0;
      --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      --text-dark: #2d3436;
      --glass-white: rgba(255, 255, 255, 0.85);
      --gov-blue: #1a1a7c; /* Tambahan untuk alat aksesibiliti */
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }

    body { 
        background: url('bg.png') no-repeat center center fixed; 
        background-size: cover;
        color: var(--text-dark); 
        min-height: 100vh;
        overflow-x: hidden; 
        transition: filter 0.3s ease; /* Tambahan untuk kesan transisi */
    }

    /* =========================================
        STRUKTUR BARU ACCESSIBILITY TOOLS CSS
       ========================================= */
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
    
    .acc-item img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }

    html.grayscale { filter: grayscale(100%) !important; }
    html.negative-contrast { filter: invert(100%) hue-rotate(180deg) !important; }
    
    .high-contrast { background: #000 !important; color: #ffff00 !important; }
    .high-contrast * { color: #ffff00 !important; border-color: #ffff00 !important; }
    /* ========================================= */

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 8%;
      background: rgb(255, 253, 253);
      backdrop-filter: blur(15px);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
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
      font-size: 0.9rem;
      transition: 0.3s;
      box-shadow: 0 4px 15px rgba(108, 92, 231, 0.2);
    }

    .profile-btn img {
      width: 45px; height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .hero { 
        position: relative; 
        height: 50vh; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        margin: 20px 5% 0;
        border-radius: 30px;
        overflow: hidden;
    }
    .hero img { 
        position: absolute; width: 100%; height: 100%; 
        object-fit: cover; z-index: -1; 
        filter: brightness(60%);
    }
    .hero-text { color: white; text-align: center; }
    .hero-text h1 { font-size: 3rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 10px; }
    .hero-text p { font-size: 1.1rem; opacity: 0.9; font-weight: 400; }

    .content-container {
        max-width: 1100px;
        margin: -40px auto 50px;
        padding: 0 20px;
    }

    .about { 
        background: var(--glass-white); 
        padding: 40px; 
        border-radius: 24px; 
        text-align: center; 
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        margin-bottom: 40px;
        border: 1px solid rgba(255,255,255,0.5);
    }
    .about b { color: var(--primary-soft); font-size: 1.2rem; letter-spacing: 2px; display: block; margin-bottom: 10px; }
    .about p { line-height: 1.6; color: #555; max-width: 700px; margin: 0 auto; }

    .button-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .main-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 30px;
      background: white;
      border-radius: 24px;
      text-decoration: none;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      box-shadow: 0 10px 20px rgba(0,0,0,0.02);
      border: 1px solid #eee;
    }

    .btn-icon-img {
      width: 110px; 
      height: 110px;
      object-fit: contain; 
      margin-bottom: 15px;
      transition: 0.4s;
    }

    .main-btn span { color: var(--text-dark); font-weight: 700; font-size: 1.2rem; }

    .main-btn:hover {
      transform: translateY(-12px);
      box-shadow: 0 20px 30px rgba(108, 92, 231, 0.1);
      border-color: var(--secondary-soft);
    }

    footer { padding: 50px; opacity: 0.7; font-weight: 500; }

    @media (max-width: 768px) {
      header { padding: 15px 5%; }
      nav { display: none; }
      .hero-text h1 { font-size: 2rem; }
      .hero { height: 40vh; margin: 10px; border-radius: 20px; }
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
    <a href="index.php">
        <img src="Logo.png" alt="Logo" class="logo-img">
    </a>

    <div class="header-right">
        <nav>
            <a href="index.php">Utama</a>
            <a href="nota.php">Nota</a>
             <a href="kuiz.php">Kuiz</a>
            <a href="rekod.php">Rekod</a>
        </nav>

        <div class="user-area">
            <?php if(isset($_SESSION['id'])) { ?>
                <a href="Profail.php" class="profile-btn">
                    <img src="<?php echo $gambar_path; ?>" alt="Profil">
                </a>
            <?php } else { ?>
                <a href="logmasuk.php" class="btn-login">Log Masuk</a>
            <?php } ?>
        </div>
    </div>
</header>

<section class="hero">
    <img src="kv.jpg" alt="Kolej Vokasional Kudat">
    <div class="hero-text">
        <h1>E-PEMBELAJARAN SAINS SVM 1</h1>
        <p>KOLEJ VOKASIONAL KUDAT</p>
    </div>
</section>

<div class="content-container">
    <section class="about">
        <b>TENTANG KAMI</b>
        <p>E-pembelajaran Sains SVM 1 merupakan satu platform yang dibuat untuk memudahkan pembelajaran secara atas talian bagi pensyarah dan pelajar di Kolej Vokasional Kudat.</p>
    </section>

<div class="button-grid">
    <a href="nota.php" class="main-btn nota-card">
        <img src="nota.png" alt="Ikon Nota" class="btn-icon-img">
        <span>Nota</span>
    </a>
    
    <?php if(isset($_SESSION['id'])) { ?>
        <a href="kuiz.php" class="main-btn kuiz-card">
            <img src="kuiz.png" alt="Ikon Kuiz" class="btn-icon-img">
            <span>Kuiz</span>
        </a>

        <a href="rekod.php" class="main-btn rekod-card">
            <img src="rekod.png" alt="Ikon Rekod" class="btn-icon-img">
            <span>Rekod Kuiz</span>
        </a>
    <?php } else { ?>
        <a href="#" class="main-btn kuiz-card" onclick="alert('Sila log masuk untuk menjawab kuiz.'); window.location='logmasuk.php';">
            <img src="kuiz.png" alt="Ikon Kuiz" class="btn-icon-img">
            <span>Kuiz</span>
        </a>

        <a href="#" class="main-btn rekod-card" onclick="alert('Harap maaf!! Sila log masuk untuk melihat rekod.'); window.location='logmasuk.php';">
            <img src="rekod.png" alt="Ikon Rekod" class="btn-icon-img">
            <span>Rekod</span>
        </a>
    <?php } ?>
</div>
</div>
</div>

<footer style="text-align: center; font-size: 0.85em; color: #000000;">
     &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script>
    function toggleAccMenu() {
        document.getElementById('accMenu').classList.toggle('active');
    }

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



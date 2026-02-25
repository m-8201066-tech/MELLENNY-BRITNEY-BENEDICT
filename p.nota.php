<?php
session_start();
include 'config.php';

$gambar_default = "profile.jpg"; 

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $sql = $conn->prepare("SELECT gambar FROM pengguna WHERE id = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $data = $sql->get_result()->fetch_assoc();

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

        /* MENYEKAT PRINT DALAM CSS */
        @media print {
            body { display: none !important; }
        }

        /* MENYEKAT SELECTION TEKS */
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }

        /* HEADER & UI STYLES */
        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; background: rgb(255, 255, 255); backdrop-filter: blur(15px);
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        header img.logo-img { height: 50px; border-radius: 8px; }
        nav { display: flex; gap: 25px; }
        nav a { text-decoration: none; color: var(--text-dark); font-weight: 800; transition: 0.3s; }
        .profile-btn img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid white; }

        /* MAIN SECTION */
        main { flex: 1; padding: 50px 8%; }
        .back-link img { width: 50px; transition: 0.3s; }
        .back-link:hover { transform: translateX(-8px); }

        .page-header { text-align: center; margin-bottom: 50px; }
        h1 { font-size: 3rem; font-weight: 800; color: #000; }

        .container {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px; max-width: 1200px; margin: 0 auto;
        }

        .box {
            background: white; padding: 40px 30px; border-radius: 30px;
            text-decoration: none; color: var(--text-dark);
            display: flex; flex-direction: column; align-items: center;
            transition: 0.4s; border: 1px solid rgba(0,0,0,0.02);
            position: relative; overflow: hidden;
        }
        .box::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: var(--primary-soft); }
        .icon-circle { width: 100px; height: 100px; background: #f0edff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .icon-circle img { width: 70%; }
        .box:hover { transform: translateY(-15px); box-shadow: 0 20px 40px rgba(108, 92, 231, 0.15); }

        /* ACCESSIBILITY STYLES (SAMA SEPERTI KUIZ1.PHP) */
        .acc-wrapper { position: fixed; right: 25px; top: 120px; z-index: 2000; }
        .acc-button { background: var(--primary-soft); width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .acc-menu { display: none; position: absolute; right: 0; top: 65px; background: white; width: 250px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); overflow: hidden; }
        .acc-menu.active { display: block; }
        .acc-item { padding: 14px 20px; display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; font-weight: 600; border-bottom: 1px solid #eee; }
  </style>
</head>

<body oncontextmenu="return false;"> <div class="acc-wrapper">
    <div class="acc-button" onclick="toggleAccMenu()">
        <img src="tools.png" alt="Tools" style="width:30px; filter:invert(1);">
    </div>
    <div class="acc-menu" id="accMenu">
        <div class="acc-item" onclick="adjustFont(10)">Besarkan teks</div>
        <div class="acc-item" onclick="adjustFont(-10)">Kecilkan teks</div>
        <div class="acc-item" onclick="resetAcc()">Reset</div>
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
        <a href="p-profail.php" class="profile-btn"><img src="<?php echo $gambar_path; ?>"></a>
    </div>
</header>

<main>
    <a href="pensyarah.php" class="back-link"><img src="back.png"></a>

    <div class="page-header" data-aos="fade-up">
        <h1>Nota Sains</h1>
        <p>Modul Pembelajaran SVM Semester 1 (Paparan Sahaja)</p>
    </div>

    <div class="container">
        <?php
        $nota_files = [
            ['file' => 'Langkah Keselamatan Dalam Makmal.pdf', 'title' => 'Bab 1', 'desc' => 'Keselamatan Makmal', 'icon' => 'safety.png'],
            ['file' => 'Bantuan Kecemasan.pdf', 'title' => 'Bab 2', 'desc' => 'Bantuan Kecemasan', 'icon' => 'cpr.png'],
            ['file' => 'Teknik Mengukur Parameter Kesihatan Badan.pdf', 'title' => 'Bab 3', 'desc' => 'Parameter Kesihatan', 'icon' => 'suhu.png']
        ];

        foreach ($nota_files as $nota) {
            // Nota dibuka melalui Viewer khas untuk menyekat toolbar default browser
            $viewer_url = "pelihat_nota.php?file=" . urlencode($nota['file']);
            echo "
            <a href='$viewer_url' target='_blank' class='box' data-aos='fade-up'>
                <div class='icon-circle'><img src='{$nota['icon']}'></div>
                <h2>{$nota['title']}</h2>
                <p>{$nota['desc']}</p>
            </a>";
        }
        ?>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    // MENYEKAT SHORTCUT (CTRL+P, CTRL+S, CTRL+U, F12)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && (e.key === 'p' || e.key === 's' || e.key === 'u' || e.key === 'c')) {
            e.preventDefault();
            alert("Fungsi ini dilarang demi keselamatan nota.");
        }
        if (e.key === 'F12') {
            e.preventDefault();
        }
    });

    function toggleAccMenu() { document.getElementById('accMenu').classList.toggle('active'); }
    let zoom = 100;
    function adjustFont(amount) { 
        zoom += amount; 
        document.documentElement.style.fontSize = zoom + "%"; 
    }
    function resetAcc() { 
        zoom = 100; 
        document.documentElement.style.fontSize = "100%"; 
    }
</script>
</body>
</html>

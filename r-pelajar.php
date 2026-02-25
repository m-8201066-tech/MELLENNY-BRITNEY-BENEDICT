<?php
session_start();
include 'config.php';

// Sekuriti: hanya pensyarah
if (!isset($_SESSION['id']) || $_SESSION['peranan'] != 'pensyarah') {
    echo "Akses tidak dibenarkan!";
    exit();
}

// 1. Ambil ID Pensyarah yang sedang log masuk
$pensyarah_id = $_SESSION['id'];

// --- LOGIK HEADER (Diselaraskan dengan index.php & p-nota.php) ---
$gambar_default = "profile.jpg"; 

$sql_img = $conn->prepare("SELECT gambar FROM pengguna WHERE id = ?");
$sql_img->bind_param("i", $pensyarah_id);
$sql_img->execute();
$data_img = $sql_img->get_result()->fetch_assoc();

// Semak jika data gambar wujud DALAM pangkalan data DAN fail wujud di folder uploads
if (!empty($data_img['gambar']) && file_exists("uploads/" . $data_img['gambar'])) {
    $gambar_path = "uploads/" . $data_img['gambar']; 
} else {
    // Jika syarat gagal, guna profile.jpg
    $gambar_path = $gambar_default; 
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rekod Pelajar|Sains KuVocC</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    :root {
        --primary-soft: #6c5ce7;
        --secondary-soft: #a29bfe;
        --text-dark: #2d3436;
        --glass-white: rgba(255, 255, 255, 0.9);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }

    body {
        background: url('bg.png') no-repeat center center fixed; 
        background-size: cover;
        color: var(--text-dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

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

    .profile-btn img {
        width: 45px; height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    /* MAIN CONTENT */
    main { flex: 1; padding: 50px 8%; position: relative; }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        transition: 0.3s;
    }
    .back-link img { width: 50px; transition: 0.3s; }
    .back-link:hover { transform: translateX(-8px); }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    h1 {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(to right, #000000, #434343);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .subtitle {
        font-size: 1.1rem;
        color: #636e72;
        font-weight: 500;
    }

    /* SEARCH BOX */
    .search-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }

    .search-box {
        display: flex;
        align-items: center;
        background: white;
        border: 2px solid #f0edff;
        border-radius: 15px;
        padding: 5px 20px;
        width: 100%;
        max-width: 450px;
        transition: 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .search-box input {
        border: none;
        outline: none;
        width: 100%;
        padding: 10px 0;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .search-box img {
        width: 25px;
        height: 25px;
        margin-left: 10px;
        opacity: 0.7;
    }

    /* TABLE CONTAINER */
    .table-container {
        background: var(--glass-white);
        border-radius: 30px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        border: 1px solid rgba(255, 255, 255, 0.3);
        max-width: 1100px;
        margin: 0 auto;
        overflow-x: auto;
    }

    table { width: 100%; border-collapse: collapse; }

    th {
        text-align: center;
        padding: 20px;
        color: var(--primary-soft);
        font-weight: 800;
        font-size: 1rem;
        border-bottom: 2px solid #f0edff;
    }

    td {
        padding: 20px;
        font-size: 0.95rem;
        font-weight: 600;
        border-bottom: 1px solid #f9f9f9;
        text-align: center;
    }

    tr:hover td { background: rgba(108, 92, 231, 0.03); }

    .badge-bab {
        background: #f0edff;
        color: var(--primary-soft);
        padding: 6px 15px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 800;
    }

    .markah-box { font-weight: 800; color: #2d3436; }

    footer {
        text-align: center;
        padding: 40px;
        color: #000000;
        font-weight: 600;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        header { padding: 15px 5%; }
        nav { display: none; }
        h1 { font-size: 2.2rem; }
    }
</style>
</head>

<body>

<header>
    <a href="pensyarah.php">
        <img src="Logo.png" alt="Logo" class="logo-img">
    </a>
    <div class="header-right">
        <nav>
            <a href="pensyarah.php">Utama</a>
            <a href="p-nota.php">Nota</a>
            <a href="r-pelajar.php">Rekod</a>
        </nav>
        <div class="user-area">
            <a href="Profail.php" class="profile-btn">
                <img src="<?php echo $gambar_path; ?>" alt="Profil">
            </a>
        </div>
    </div>
</header>

<main>
    <a href="pensyarah.php" class="back-link" data-aos="fade-right">
        <img src="back.png" alt="Back">
    </a>

    <div class="page-header" data-aos="fade-up">
        <h1>Senarai & Rekod Pelajar</h1>
        <p class="subtitle">Pantau prestasi kuiz pelajar di bawah seliaan anda</p>
    </div>

    <div class="search-container" data-aos="fade-up" data-aos-delay="100">
        <div class="search-box">
            <input id="searchInput" type="text" placeholder="Cari nama pelajar atau kursus...">
            <img src="search.png" alt="Search">
        </div>
    </div>

    <div class="table-container" data-aos="zoom-in" data-aos-delay="200">
        <table id="pelajarTable">
            <thead>
                <tr>
                    <th>Nama Pelajar</th>
                    <th>Kursus</th> 
                    <th>Bab</th>
                    <th>Markah</th>
                    <th>Percubaan</th>
                    <th>Tarikh Jawab</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_records = "
                SELECT 
                    p.username, k.kod_kursus, r.bab, r.markah, r.percubaan, r.tarikh_jawab
                FROM pengguna p
                INNER JOIN pendaftaran_kursus pk ON p.id = pk.pelajar_id
                INNER JOIN kursus k ON pk.kursus_id = k.id
                LEFT JOIN rekod_kuiz r ON p.id = r.pelajar_id
                INNER JOIN pengajaran_kursus pjk ON k.id = pjk.kursus_id
                WHERE p.peranan = 'pelajar'
                AND pjk.pensyarah_id = ? 
                ORDER BY p.username, r.tarikh_jawab DESC
                ";

                $stmt = $conn->prepare($sql_records);
                $stmt->bind_param("i", $pensyarah_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $tarikh = ($row['tarikh_jawab']) ? date("d M Y", strtotime($row['tarikh_jawab'])) : '-';
                        echo "<tr>
                            <td><strong style='color: var(--primary-soft);'>{$row['username']}</strong></td>
                            <td>" . ($row['kod_kursus'] ?? '-') . "</td>
                            <td><span class='badge-bab'>" . ($row['bab'] ?? '-') . "</span></td>
                            <td><span class='markah-box'>" . ($row['markah'] !== null ? $row['markah']." / 100" : "-") . "</span></td>
                            <td>" . ($row['percubaan'] ? "Percubaan ke-".$row['percubaan'] : "-") . "</td>
                            <td style='color: #95a5a6;'>{$tarikh}</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='padding: 40px;'>Tiada rekod pelajar dijumpai.</td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>

<footer data-aos="fade-up">
    &copy; 2026 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Hak Cipta Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });

    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll("#pelajarTable tbody tr");

    searchInput.addEventListener("keyup", function () {
        let keyword = this.value.toLowerCase();
        rows.forEach((row) => {
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? "" : "none";
        });
    });
</script>
</body>
</html>

<?php
session_start();
include 'config.php';

// Pastikan pelajar sudah log masuk
if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id_pelajar = $_SESSION['id'];

// --- LOGIK HEADER ---
$gambar_default = "profile.jpg"; 

$sql_user = $conn->prepare("SELECT gambar FROM pengguna WHERE id = ?");
$sql_user->bind_param("i", $id_pelajar);
$sql_user->execute();
$data_user = $sql_user->get_result()->fetch_assoc();

if (!empty($data_user['gambar']) && file_exists("uploads/" . $data_user['gambar'])) {
    $gambar_path = "uploads/" . $data_user['gambar']; 
} else {
    $gambar_path = $gambar_default; 
}

// --- DATA SOALAN BAB 3 ---
$bahagian_a = [
    1 => ['soalan' => 'Apakah maksud suhu badan?', 'pilihan' => ['A' => 'Tahap kecergasan fizikal seseorang', 'B' => 'Darjah kepanasan atau kesejukan badan seseorang', 'C' => 'Ukuran tenaga yang dihasilkan', 'D' => 'Bilangan degupan jantung'], 'jawapan' => 'B', 'gambar' => ''],
    2 => ['soalan' => 'Berapakah suhu badan normal bagi seorang manusia?', 'pilihan' => ['A' => '35.5 °C', 'B' => '36.0 °C', 'C' => '36.9 °C atau 37 °C', 'D' => '38.5 °C'], 'jawapan' => 'C', 'gambar' => ''],
    3 => ['soalan' => 'Termometer manakah yang paling sesuai digunakan tanpa sentuhan fizikal?', 'pilihan' => ['A' => 'Termometer makmal', 'B' => 'Termometer klinik', 'C' => 'Termometer inframerah', 'D' => 'Termometer rektal'], 'jawapan' => 'C', 'gambar' => 'infrared.png'],
    4 => ['soalan' => 'Apakah kegunaan utama pencerutan pada termometer klinik?', 'pilihan' => ['A' => 'Supaya merkuri tidak turun dengan cepat', 'B' => 'Memudahkan termometer masuk ke mulut', 'C' => 'Supaya tidak mudah pecah', 'D' => 'Meningkatkan julat suhu'], 'jawapan' => 'A', 'gambar' => 'pencerutan.png'],
    5 => ['soalan' => 'Termometer rektal biasanya digunakan untuk golongan manakah?', 'pilihan' => ['A' => 'Warga emas', 'B' => 'Atlet', 'C' => 'Bayi berumur kurang daripada 3 bulan', 'D' => 'Murid sekolah rendah'], 'jawapan' => 'C', 'gambar' => 'rektal.png'],
    6 => ['soalan' => 'Suhu badan di bawah 35 °C secara berterusan boleh menyebabkan...', 'pilihan' => ['A' => 'Hipertensi', 'B' => 'Hipotermia', 'C' => 'Strok haba', 'D' => 'Anemia'], 'jawapan' => 'B', 'gambar' => ''],
    7 => ['soalan' => 'Apakah yang dimaksudkan dengan kadar denyutan nadi?', 'pilihan' => ['A' => 'Tekanan darah pada dinding arteri', 'B' => 'Pengukuran bilangan degupan jantung per minit', 'C' => 'Kelajuan aliran darah di dalam vena', 'D' => 'Jumlah oksigen dalam darah'], 'jawapan' => 'B', 'gambar' => ''],
    8 => ['soalan' => 'Mengapakah nadi lelaki dewasa biasanya lebih rendah berbanding perempuan dewasa?', 'pilihan' => ['A' => 'Lelaki lebih aktif', 'B' => 'Jisim badan lelaki lebih berat', 'C' => 'Isi padu jantung lelaki adalah lebih besar', 'D' => 'Saiz arteri lelaki lebih lebar'], 'jawapan' => 'C', 'gambar' => ''],
    9 => ['soalan' => 'Di manakah titik nadi yang paling kerap digunakan kerana mudah dirasai?', 'pilihan' => ['A' => 'Leher', 'B' => 'Belakang lutut', 'C' => 'Pergelangan tangan', 'D' => 'Buku lali'], 'jawapan' => 'C', 'gambar' => 'nadi.png'],
    10 => ['soalan' => 'Alat yang digunakan untuk mengukur tekanan darah ialah...', 'pilihan' => ['A' => 'Stetoskop', 'B' => 'Sfigmomanometer', 'C' => 'Termometer', 'D' => 'Barometer'], 'jawapan' => 'B', 'gambar' => 'alat.png'],
    11 => ['soalan' => 'Unit bagi ukuran tekanan darah ialah...', 'pilihan' => ['A' => 'kg m-2', 'B' => 'mmHg', 'C' => '°C', 'D' => 'bpm'], 'jawapan' => 'B', 'gambar' => ''],
    12 => ['soalan' => 'Dalam bacaan 120/80 mmHg, nombor atas (120) mewakili...', 'pilihan' => ['A' => 'Tekanan diastolik', 'B' => 'Tekanan nadi', 'C' => 'Tekanan sistolik', 'D' => 'Tekanan atmosfera'], 'jawapan' => 'C', 'gambar' => ''],
    13 => ['soalan' => 'Bacaan 150/95 mmHg dikategorikan sebagai...', 'pilihan' => ['A' => 'Normal', 'B' => 'Berisiko', 'C' => 'Tekanan darah tinggi peringkat 1', 'D' => 'Tekanan darah tinggi peringkat 3'], 'jawapan' => 'C', 'gambar' => 't.darah.png'],
    14 => ['soalan' => 'Apakah rumus yang digunakan untuk menghitung BMI?', 'pilihan' => ['A' => 'Berat (kg) / [Tinggi (m) x Tinggi (m)]', 'B' => 'Tinggi / (Berat x Berat)', 'C' => 'Berat x Tinggi', 'D' => 'Berat / Tinggi'], 'jawapan' => 'A', 'gambar' => 'bmi.jpg'],
    15 => ['soalan' => 'Antara berikut, yang manakah merupakan kesan kekurangan jisim badan?', 'pilihan' => ['A' => 'Diabetes mellitus', 'B' => 'Tekanan darah tinggi', 'C' => 'Anemia dan keletihan', 'D' => 'Serangan jantung'], 'jawapan' => 'C', 'gambar' => '']
];

$bahagian_b = [
    1 => ['soalan' => 'Nyatakan empat jenis termometer yang digunakan untuk menyukat suhu.', 'jawapan' => 'klinik, makmal, rektal, inframerah', 'gambar' => ''],
    2 => ['soalan' => 'Berapakah julat suhu badan normal manusia?', 'jawapan' => '36.9, 37', 'gambar' => ''],
    3 => ['soalan' => 'Mengapakah termometer makmal tidak sesuai untuk menyukat suhu badan?', 'jawapan' => 'pencerutan, cepat, tidak tepat', 'gambar' => 'makmal.png'],
    4 => ['soalan' => 'Berikan satu faktor suhu badan menjadi tidak normal.', 'jawapan' => 'jangkitan, senaman, panas, sejuk', 'gambar' => ''],
    5 => ['soalan' => 'Apakah tiga faktor utama yang mempengaruhi kadar denyutan nadi?', 'jawapan' => 'jantina, umur, aktiviti fizikal', 'gambar' => ''],
    6 => ['soalan' => 'Apakah perbezaan otot jantung semasa tekanan sistolik dan diastolik?', 'jawapan' => 'mengecut, berehat', 'gambar' => ''],
    7 => ['soalan' => 'Hitung BMI jika berat 60 kg dan tinggi 1.6 m.', 'jawapan' => '23.44', 'gambar' => 'bmi.jpg'],
    8 => ['soalan' => 'Nyatakan satu penyakit akibat jisim badan berlebihan (Obes).', 'jawapan' => 'diabetes, jantung, darah tinggi', 'gambar' => ''],
    9 => ['soalan' => 'Berikan satu cara menambah jisim badan secara sihat.', 'jawapan' => 'makan sihat, snek padat nutrien, waktu tepat', 'gambar' => ''],
    10 => ['soalan' => 'Mengapakah nadi bayi lebih tinggi berbanding dewasa?', 'jawapan' => 'jantung kecil', 'gambar' => '']
];

// --- LOGIK PENGIRAAN MARKAH ---
$skor_keseluruhan = 0;
$betul_a = 0;
$betul_b = 0;
$markah_per_soalan = 4; 
$sudah_hantar = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sudah_hantar = true;
    foreach ($bahagian_a as $id => $data) {
        if (isset($_POST["a_$id"]) && $_POST["a_$id"] == $data['jawapan']) { 
            $skor_keseluruhan += $markah_per_soalan; 
            $betul_a++;
        }
    }
    foreach ($bahagian_b as $id => $data) {
        $jawapan_pelajar = strtolower(trim($_POST["b_$id"] ?? ''));
        $kata_kunci_array = explode(', ', strtolower($data['jawapan']));
        $jumpa_point = false;
        foreach ($kata_kunci_array as $kunci) {
            if (!empty($jawapan_pelajar) && strpos($jawapan_pelajar, trim($kunci)) !== false) {
                $jumpa_point = true;
                break;
            }
        }
        if ($jumpa_point) { 
            $skor_keseluruhan += $markah_per_soalan; 
            $betul_b++;
        }
    }
    $markah_final = round($skor_keseluruhan);
    $tarikh = date("Y-m-d H:i:s");
    $bab = "Bab 3";

    $stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM rekod_kuiz WHERE pelajar_id = ? AND bab = ?");
    $stmt_check->bind_param("is", $id_pelajar, $bab);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result()->fetch_assoc();
    $percubaan = $res_check['total'] + 1;

    $stmt_ins = $conn->prepare("INSERT INTO rekod_kuiz (pelajar_id, bab, markah, tarikh_jawab, percubaan) VALUES (?, ?, ?, ?, ?)");
    $stmt_ins->bind_param("isisi", $id_pelajar, $bab, $markah_final, $tarikh, $percubaan);
    $stmt_ins->execute();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuiz 3 Sains | Sains KuVocC</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root { 
            --primary-soft: #6c5ce7; 
            --secondary-soft: #a29bfe;
            --text-dark: #2d3436; 
            --gov-blue: #1a1a7c;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Plus Jakarta Sans", sans-serif; }
        
        body { 
            background: url('bg.png') no-repeat center center fixed; 
            background-size: cover; 
            color: var(--text-dark); 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            transition: filter 0.3s ease;
        }

        /* ACCESSIBILITY TOOLS */
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
        .acc-item { padding: 14px 20px; display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; font-weight: 600; color: #444; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .acc-item:hover { background: rgba(108, 92, 231, 0.1); color: var(--primary-soft); }
        .acc-item img { width: 20px; height: 20px; object-fit: contain; }

        html.grayscale { filter: grayscale(100%) !important; }
        html.negative-contrast { filter: invert(100%) hue-rotate(180deg) !important; }
        .high-contrast { background: #000 !important; color: #ffff00 !important; }
        .high-contrast * { color: #ffff00 !important; border-color: #ffff00 !important; }

        /* HEADER */
        header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 8%; background: rgb(255, 255, 255); backdrop-filter: blur(15px);
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        header img.logo-img { height: 50px; border-radius: 8px; }
        .header-right { display: flex; align-items: center; gap: 30px; }
        nav { display: flex; gap: 25px; }
        nav a { text-decoration: none; color: var(--text-dark); font-weight: 800; font-size: 0.95rem; }
        .profile-btn img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid white; }

        /* MAIN SECTION */
        main { flex: 1; padding: 50px 8%; position: relative; }
        .container { max-width: 850px; margin: auto; background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        h1 { text-align: center; font-size: 2.2rem; font-weight: 800; color: #4834d4; }
        h3 { text-align: center; color: #636e72; margin-bottom: 30px; }
        .section-badge { display: inline-block; background: #a29bfe; color: white; padding: 8px 20px; border-radius: 12px; font-weight: 700; margin-bottom: 20px; }
        .card { background: white; border-radius: 20px; padding: 25px; margin-bottom: 25px; border: 1px solid #f0f0f0; }
        .card.is-wrong { border-left: 5px solid #ff7675; }
        .card.is-correct { border-left: 5px solid #55efc4; }
        .question-text { font-size: 1.1rem; font-weight: 700; margin-bottom: 15px; }
        .img-rajah { width: 100%; max-width: 400px; border-radius: 15px; margin-bottom: 20px; display: block; }
        .radio-group label { display: block; padding: 12px 18px; background: #fdfdfd; border-radius: 12px; margin: 8px 0; cursor: pointer; border: 1px solid #eee; }
        input[type="text"] { width: 100%; padding: 15px; border: 2px solid #f0f0f0; border-radius: 12px; outline: none; background: #fafafa; }
        .btn-submit { display: block; width: 100%; padding: 18px; background: #6c5ce7; color: white; border: none; border-radius: 15px; font-size: 1.1rem; font-weight: 800; cursor: pointer; text-align: center; text-decoration: none; }
        
        /* RESULTS */
        .result-container { text-align: center; padding: 40px; background: linear-gradient(135deg, #f5f7fa 0%, #e3f2fd 100%); border: 2px solid #bbdefb; border-radius: 30px; margin-bottom: 40px; }
        .score-display { font-size: 4.5rem; font-weight: 800; color: #1976d2; }
        .stat-flex { display: flex; justify-content: center; gap: 20px; margin-top: 25px; }
        .stat-card { background: white; padding: 15px 25px; border-radius: 15px; border: 1px solid #e1f5fe; min-width: 140px; }
        .stat-val { display: block; font-size: 1.4rem; font-weight: 800; color: #1e88e5; }
        .back-link img { width: 50px; transition: 0.3s; }

        footer { text-align: center; padding: 40px; color: #636e72; font-weight: 600; }

        @media print {
            header, footer, .back-link, .acc-wrapper, .btn-submit, button { display: none !important; }
            .container { box-shadow: none !important; border: none !important; width: 100% !important; }
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
    <a href="index1.php"><img src="Logo.png" alt="Logo" class="logo-img"></a>
    <div class="header-right">
        <nav>
            <a href="index1.php">Utama</a>
            <a href="nota.php">Nota</a>
            <a href="kuiz.php">Kuiz</a>
            <a href="rekod.php">Rekod</a>
        </nav>
        <a href="Profail.php" class="profile-btn"><img src="<?php echo $gambar_path; ?>" alt="Profil"></a>
    </div>
</header>

<main>
    <a href="kuiz.php" class="back-link" data-aos="fade-right"><img src="back.png" alt="Back"></a>

    <div class="container" data-aos="fade-up">
        <h1>Bab 3</h1>
        <h3>Parameter Kesihatan Badan</h3>

        <?php if ($sudah_hantar): ?>
            <div class="result-container" data-aos="zoom-in">
                <span style="font-size:3.5rem;"><?php echo ($markah_final >= 50) ? "🌿" : "🌱"; ?></span>
                <p style="font-weight:700; color:#64b5f6; text-transform:uppercase;">Skor Akhir</p>
                <div class="score-display"><?php echo $markah_final; ?><span style="font-size: 1.5rem; color: #90caf9;">/100</span></div>
                
                <div class="stat-flex">
                    <div class="stat-card">
                        <span class="stat-val"><?php echo $betul_a + $betul_b; ?> / 25</span>
                        <span style="font-size:0.75rem; color:#90caf9; font-weight:700;">Betul</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-val">#<?php echo $percubaan; ?></span>
                        <span style="font-size:0.75rem; color:#90caf9; font-weight:700;">Percubaan</span>
                    </div>
                </div>

                <div style="margin-top:25px; color:#546e7a; font-weight:600;">
                    <?php 
                    if($markah_final >= 80) echo "Cemerlang! Anda sangat memahami parameter kesihatan badan.";
                    elseif($markah_final >= 50) echo "Bagus! Anda telah lulus kuiz ini. Teruskan belajar!";
                    else echo "Usaha lagi! Sila baca nota Teknik Mengukur untuk meningkatkan skor anda.";
                    ?>
                </div>

                <div style="display: flex; gap: 12px; justify-content: center; margin-top:35px;">
                    <a href="kuiz3.php" class="btn-submit" style="width: auto; padding: 12px 30px; background: white; color: #6c5ce7; border: 2px solid #e0e0e0;">Cuba Lagi</a>
                    <button onclick="window.print()" class="btn-submit" style="width: auto; padding: 12px 30px; background: #2d3436;">Cetak Hasil</button>
                    <a href="rekod.php" class="btn-submit" style="width: auto; padding: 12px 30px; background: #81acee;">Lihat Rekod</a>
                </div>
            </div>

            <div class="section-badge">Semakan Jawapan</div>
            <?php foreach ($bahagian_a as $id => $data): $jawapan_p = $_POST["a_$id"] ?? 'Tiada'; $betul = ($jawapan_p == $data['jawapan']); ?>
                <div class="card <?php echo $betul ? 'is-correct' : 'is-wrong'; ?>">
                    <div class="question-text"><?php echo "A$id. " . $data['soalan']; ?></div>
                    <p style="font-size: 0.95rem;">Jawapan Anda: <strong><?php echo $jawapan_p; ?></strong> | Betul: <strong><?php echo $data['jawapan']; ?></strong></p>
                </div>
            <?php endforeach; ?>
            <?php foreach ($bahagian_b as $id => $data): $jawapan_p = trim($_POST["b_$id"] ?? ''); $betul = false;
                foreach (explode(', ', strtolower($data['jawapan'])) as $kunci) { if (!empty($jawapan_p) && strpos(strtolower($jawapan_p), trim($kunci)) !== false) { $betul = true; break; } } ?>
                <div class="card <?php echo $betul ? 'is-correct' : 'is-wrong'; ?>">
                    <div class="question-text"><?php echo "B$id. " . $data['soalan']; ?></div>
                    <p style="font-size: 0.95rem;">Jawapan: <strong><?php echo $jawapan_p ?: 'Tiada'; ?></strong></p>
                    <p style="color: #95a5a6; font-size: 0.85rem; margin-top:5px;">Skema: <?php echo $data['jawapan']; ?></p>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <form action="" method="POST">
                <div class="section-badge">Bahagian A: Objektif (60%)</div>
                <?php foreach ($bahagian_a as $id => $item): ?>
                    <div class="card">
                        <div class="question-text"><?php echo "$id. " . $item['soalan']; ?></div>
                        <?php if(!empty($item['gambar'])): ?><img src="<?php echo $item['gambar']; ?>" class="img-rajah"><?php endif; ?>
                        <div class="radio-group">
                            <?php foreach ($item['pilihan'] as $h => $t): ?>
                                <label><input type="radio" name="a_<?php echo $id; ?>" value="<?php echo $h; ?>" required> <strong><?php echo $h; ?>.</strong> <?php echo $t; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="section-badge">Bahagian B: Subjektif (40%)</div>
                <?php foreach ($bahagian_b as $id => $item): ?>
                    <div class="card">
                        <div class="question-text"><?php echo "$id. " . $item['soalan']; ?></div>
                        <input type="text" name="b_<?php echo $id; ?>" placeholder="Taip jawapan pendek..." required>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn-submit">Hantar Jawapan</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Mellenny Britney</span>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    // FUNGSI AKSESIBILITI
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

    // Tutup menu jika klik di luar
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

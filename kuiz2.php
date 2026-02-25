<?php
session_start();
include 'config.php';

// Pastikan pelajar sudah log masuk
if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id_pelajar = $_SESSION['id'];

// --- LOGIK HEADER (Diselaraskan dengan kuiz.php) ---
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

// --- DATA SOALAN BAB 2 ---
$bahagian_a = [
    1 => ['soalan' => 'Apakah maksud akronim CPR?', 'pilihan' => ['A' => 'Cardiopulmonary Resusicitation', 'B' => 'Cardiac Pressure Rescue', 'C' => 'Chest Pulse Revival', 'D' => 'Cardiorespiratory Procedure'], 'jawapan' => 'A', 'gambar' => ''],
    2 => ['soalan' => 'Manakah antara berikut merupakan teknik yang digunakan dalam CPR?', 'pilihan' => ['A' => 'Sapuan pada belakang badan', 'B' => 'Teknik bantuan pernafasan mulut ke mulut dan teknik yang menggunakan tekanan dada', 'C' => 'Teknik menepuk bahu dan mengangkat kaki', 'D' => 'Memberi minuman air suam'], 'jawapan' => 'B', 'gambar' => 'tekanan.png'],
    3 => ['soalan' => 'Situasi manakah yang memerlukan bantuan CPR?', 'pilihan' => ['A' => 'Mangsa mengalami luka kecil', 'B' => 'Mangsa boleh bercakap tetapi sesak nafas', 'C' => 'Mangsa tidak bernafas, tiada respon terhadap rangsangan, dan tiada degupan jantung', 'D' => 'Mangsa memegang leher kerana tercekik'], 'jawapan' => 'C', 'gambar' => ''],
    4 => ['soalan' => 'Berapakah kedalaman tekanan dada yang disyorkan semasa melakukan CPR kepada mangsa dewasa?', 'pilihan' => ['A' => '2-3 cm', 'B' => '3-4 cm', 'C' => '5-6 cm', 'D' => '7-8 cm'], 'jawapan' => 'C', 'gambar' => ''],
    5 => ['soalan' => 'Apakah risiko sekiranya teknik CPR dilakukan dengan cara yang tidak betul?', 'pilihan' => ['A' => 'Mangsa akan terus sedar', 'B' => 'Tulang rusuk mangsa boleh patah', 'C' => 'Tekanan darah menjadi rendah', 'D' => 'Mangsa akan mengalami pening kepala'], 'jawapan' => 'B', 'gambar' => ''],
    6 => ['soalan' => 'Berapakah kadar kelajuan tekanan dada yang perlu dilakukan dalam seminit?', 'pilihan' => ['A' => '60-80 tekanan', 'B' => '80-100 tekanan', 'C' => '100-120 tekanan', 'D' => '120-140 tekanan'], 'jawapan' => 'C', 'gambar' => ''],
    7 => ['soalan' => 'Apakah nisbah tekanan dada kepada bantuan pernafasan yang disyorkan?', 'pilihan' => ['A' => '15 tekanan : 2 hembusan', 'B' => '30 tekanan : 2 hembusan', 'C' => '30 tekanan : 5 hembusan', 'D' => '50 tekanan : 2 hembusan'], 'jawapan' => 'B', 'gambar' => ''],
    8 => ['soalan' => 'Apakah kepentingan melakukan bantuan pernafasan mulut ke mulut?', 'pilihan' => ['A' => 'Untuk memanaskan badan mangsa', 'B' => 'Membolehkan mangsa menerima udara beroksigen ke dalam peparunya', 'C' => 'Menghilangkan halangan di kerongkong', 'D' => 'Mempercepatkan denyutan nadi'], 'jawapan' => 'B', 'gambar' => 'mulut.png'],
    9 => ['soalan' => 'Jika mangsa bernafas tetapi masih belum sedar selepas CPR, apakah tindakan seterusnya?', 'pilihan' => ['A' => 'Teruskan tekanan dada', 'B' => 'Berikan minuman manis', 'C' => 'Ubah kedudukan badan mangsa dalam keadaan mengiring', 'D' => 'Biarkan mangsa dalam posisi terlentang'], 'jawapan' => 'C', 'gambar' => ''],
    10 => ['soalan' => 'Heimlich Manoeuvre dilakukan untuk membantu mangsa yang...', 'pilihan' => ['A' => 'Lemas di dalam air', 'B' => 'Terkena renjatan elektrik', 'C' => 'Tercekik makanan atau benda asing', 'D' => 'Mengalami serangan jantung'], 'jawapan' => 'C', 'gambar' => ''],
    11 => ['soalan' => 'Apakah petanda seseorang itu sedang mengalami situasi tercekik?', 'pilihan' => ['A' => 'Mangsa ketawa dengan kuat', 'B' => 'Memegang leher dengan kedua-dua tangan dan tidak boleh bercakap', 'C' => 'Mangsa bersin secara berterusan', 'D' => 'Kulit mangsa menjadi kemerah-merahan'], 'jawapan' => 'B', 'gambar' => 'tercekik.png'],
    12 => ['soalan' => 'Di manakah kedudukan genggaman tangan semasa melakukan Heimlich Manoeuvre?', 'pilihan' => ['A' => 'Di atas dada (sternum)', 'B' => 'Di antara pusat dengan bawah rusuk mangsa', 'C' => 'Di bahagian belakang leher', 'D' => 'Di bawah pusat'], 'jawapan' => 'B', 'gambar' => 'ktangan.png'],
    13 => ['soalan' => 'Mengapakah tekanan dan sentakan kuat perlu dilakukan semasa Heimlich Manoeuvre?', 'pilihan' => ['A' => 'Untuk menguatkan otot perut', 'B' => 'Untuk memaksa mangsa muntah', 'C' => 'Menghasilkan tekanan dalam peparu supaya benda asing tertolak keluar', 'D' => 'Untuk membantu mangsa bernafas dengan lebih perlahan'], 'jawapan' => 'C', 'gambar' => ''],
    14 => ['soalan' => 'Bagaimanakah cara melakukan Heimlich Manoeuvre jika tiada orang lain berdekatan (sendiri)?', 'pilihan' => ['A' => 'Berlari sekuat hati', 'B' => 'Menelan lebih banyak air', 'C' => 'Menolak bahagian antara pusat dan bawah rusuk ke objek separas pinggang seperti meja atau kerusi', 'D' => 'Berbaring secara meniarap di lantai'], 'jawapan' => 'C', 'gambar' => 'sendiri.png'],
    15 => ['soalan' => 'Apakah fungsi epiglotis semasa proses menelan makanan yang normal?', 'pilihan' => ['A' => 'Membuka saluran udara lebih luas', 'B' => 'Terlipat dan menutup saluran pernafasan (trakea)', 'C' => 'Membantu makanan masuk ke dalam peparu', 'D' => 'Menapis udara yang masuk ke esofagus'], 'jawapan' => 'B', 'gambar' => 'epiglotis.png']
];

$bahagian_b = [
    1 => ['soalan' => 'Jelaskan definisi teknik CPR secara ringkas.', 'jawapan' => 'bantuan pernafasan, mulut ke mulut, tekanan dada', 'gambar' => ''],
    2 => ['soalan' => 'Senaraikan empat keadaan individu yang memerlukan bantuan CPR.', 'jawapan' => 'serangan jantung, renjatan elektrik, lemas, panahan petir', 'gambar' => ''],
    3 => ['soalan' => 'Terangkan kepentingan melakukan teknik tekanan dada kepada mangsa serangan jantung.', 'jawapan' => 'peredaran darah, mengepam jantung, aliran darah', 'gambar' => ''],
    4 => ['soalan' => 'Apakah kesan yang akan berlaku jika otak tidak menerima oksigen dalam tempoh tertentu?', 'jawapan' => 'kerosakan otak', 'gambar' => ''],
    5 => ['soalan' => 'Nyatakan langkah pertama yang perlu dilakukan apabila anda menemui seorang mangsa yang tidak sedarkan diri.', 'jawapan' => 'periksa respon, tepuk bahu, tanya khabar', 'gambar' => ''],
    6 => ['soalan' => 'Terangkan kaedah "Head tilt-chin lift" dan tujuannya.', 'jawapan' => 'dongakkan kepala, angkat dagu, buka saluran pernafasan', 'gambar' => 'head.png'],
    7 => ['soalan' => 'Berikan tiga tanda fizikal mangsa yang memerlukan bantuan Heimlich Manoeuvre.', 'jawapan' => 'pegang leher, tidak boleh bercakap, membiru, kehitaman, sukar bernafas', 'gambar' => ''],
    8 => ['soalan' => 'Mengapakah seseorang itu boleh tercekik semasa makan?', 'jawapan' => 'makanan menghalang saluran pernafasan, trakea', 'gambar' => 'tercekik.png'],
    9 => ['soalan' => 'Perihalkan kedudukan penyelamat semasa melakukan Heimlich Manoeuvre kepada mangsa.', 'jawapan' => 'berdiri di belakang mangsa, lilitkan tangan, bongkokkan badan', 'gambar' => ''],
    10 => ['soalan' => 'Apakah yang harus dilakukan sekiranya anda melakukan CPR tetapi udara yang dihembus tidak sampai ke peparu mangsa?', 'jawapan' => 'buka saluran pernafasan, dongakkan kepala', 'gambar' => '']
];

// --- LOGIK PENGIRAAN MARKAH ---
$skor_keseluruhan = 0;
$betul_a = 0;
$betul_b = 0;
$jumlah_soalan = count($bahagian_a) + count($bahagian_b);
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
    $bab = "Bab 2";

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
  <title>Kuiz 2 Sains | Sains KuVocC</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
        /* CSS DISESUAIKAN DENGAN KUIZ.PHP */
        :root { 
            --primary-soft: #6c5ce7; 
            --secondary-soft: #a29bfe;
            --text-dark: #2d3436; 
            --soft-blue: #e3f2fd; 
            --soft-mint: #e8f5e9; 
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

        /* HEADER - SAMA SEPERTI KUIZ.PHP */
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

        /* MAIN & CONTENT */
        main { flex: 1; padding: 50px 8%; position: relative; }
        
        .back-link { display: inline-block; margin-bottom: 20px; transition: 0.3s; }
        .back-link img { width: 50px; height: auto; transition: 0.3s; }
        .back-link:hover { transform: translateX(-8px); }

        .container { max-width: 850px; margin: auto; background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        
        h1 { text-align: center; font-size: 2.2rem; font-weight: 800; margin-bottom: 5px; color: #4834d4; }
        h3 { text-align: center; color: #636e72; font-weight: 600; margin-bottom: 30px; }
        
        .section-badge { display: inline-block; background: #a29bfe; color: white; padding: 8px 20px; border-radius: 12px; font-weight: 700; margin-bottom: 20px; font-size: 0.9rem; }
        
        .card { background: white; border-radius: 20px; padding: 25px; margin-bottom: 25px; border: 1px solid #f0f0f0; }
        .card.is-wrong { border-left: 5px solid #ff7675; }
        .card.is-correct { border-left: 5px solid #55efc4; }
        
        .question-text { font-size: 1.1rem; font-weight: 700; margin-bottom: 15px; line-height: 1.5; }
        .img-rajah { width: 100%; max-width: 400px; border-radius: 15px; margin-bottom: 20px; display: block; }
        
        .radio-group label { display: block; padding: 12px 18px; background: #fdfdfd; border-radius: 12px; margin: 8px 0; cursor: pointer; border: 1px solid #eee; transition: 0.2s; }
        .radio-group label:hover { background: #f1f2f6; }
        
        input[type="text"] { width: 100%; padding: 15px; border: 2px solid #f0f0f0; border-radius: 12px; outline: none; background: #fafafa; }
        
        .btn-submit { display: block; width: 100%; padding: 18px; background: #6c5ce7; color: white; border: none; border-radius: 15px; font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: 0.3s; text-decoration: none; text-align: center; }
        
        /* BOX SKOR */
        .result-container { text-align: center; padding: 40px; background: linear-gradient(135deg, #f5f7fa 0%, #e3f2fd 100%); border: 2px solid #bbdefb; border-radius: 30px; color: #1565c0; margin-bottom: 40px; }
        .result-emoji { font-size: 3.5rem; margin-bottom: 15px; display: block; }
        .score-display { font-size: 4.5rem; font-weight: 800; color: #1976d2; margin: 10px 0; }
        .stat-flex { display: flex; justify-content: center; gap: 20px; margin-top: 25px; }
        .stat-card { background: white; padding: 15px 25px; border-radius: 15px; border: 1px solid #e1f5fe; min-width: 140px; }
        .stat-val { display: block; font-size: 1.4rem; font-weight: 800; color: #1e88e5; }
        .result-msg { font-size: 1.1rem; font-weight: 600; margin-top: 25px; color: #546e7a; line-height: 1.6; }

        footer { text-align: center; padding: 40px; color: #636e72; font-weight: 600; font-size: 0.9rem; }

        @media print {
            header, footer, .back-link, .btn-submit, .profile-btn, .header-right, button { display: none !important; }
            body { background: white !important; }
            .container { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
            .result-container { border: 2px solid #333 !important; background: none !important; color: black !important; }
            .card { page-break-inside: avoid; border: 1px solid #ccc !important; }
            .is-correct { border-left: 10px solid #55efc4 !important; }
            .is-wrong { border-left: 10px solid #ff7675 !important; }
        }

        @media (max-width: 768px) {
            header { padding: 15px 5%; }
            nav { display: none; }
            main { padding: 30px 5%; }
        }
    
  </style>
</head>
<body>

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
        <h1>Bab 2</h1>
        <h3>Bantuan Kecemasan</h3>

        <?php if ($sudah_hantar): ?>
            <div class="result-container" data-aos="zoom-in">
                <span class="result-emoji"><?php echo ($markah_final >= 50) ? "🌿" : "🌱"; ?></span>
                <p class="score-label">Skor Akhir</p>
                <div class="score-display"><?php echo $markah_final; ?><span style="font-size: 1.5rem; color: #90caf9;">/100</span></div>
                
                <div class="stat-flex">
                    <div class="stat-card">
                        <span class="stat-val"><?php echo $betul_a + $betul_b; ?> / 25</span>
                        <span class="stat-lab">Betul</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-val">#<?php echo $percubaan; ?></span>
                        <span class="stat-lab">Percubaan</span>
                    </div>
                </div>

                <div class="result-msg">
                    <?php 
                    if($markah_final >= 80) echo "Cemerlang! Anda sangat memahami bantuan kecemasan.";
                    elseif($markah_final >= 50) echo "Bagus! Anda telah lulus kuiz ini. Teruskan belajar!";
                    else echo "Usaha lagi! Sila baca nota Bab 2 untuk meningkatkan skor anda.";
                    ?>
                </div>

                <div style="display: flex; gap: 12px; justify-content: center; margin-top:35px;">
                    <a href="kuiz2.php" class="btn-submit" style="width: auto; padding: 12px 30px; font-size: 0.95rem; background: white; color: #6c5ce7; border: 2px solid #e0e0e0;">Cuba Lagi</a>
                    <button onclick="window.print()" class="btn-submit" style="width: auto; padding: 12px 30px; font-size: 0.95rem; background: #2d3436;">Cetak Hasil</button>
                    <a href="rekod.php" class="btn-submit" style="width: auto; padding: 12px 30px; font-size: 0.95rem; background: #81acee;">Lihat Rekod</a>
                </div>
            </div>

            <div class="section-badge">Semakan Jawapan</div>
            <?php foreach ($bahagian_a as $id => $data): $jawapan_p = $_POST["a_$id"] ?? 'Tiada'; $betul = ($jawapan_p == $data['jawapan']); ?>
                <div class="card <?php echo $betul ? 'is-correct' : 'is-wrong'; ?>">
                    <div class="question-text"><?php echo "A$id. " . $data['soalan']; ?></div>
                    <p>Jawapan Anda: <strong><?php echo $jawapan_p; ?></strong> | Betul: <strong><?php echo $data['jawapan']; ?></strong></p>
                </div>
            <?php endforeach; ?>
            <?php foreach ($bahagian_b as $id => $data): $jawapan_p = trim($_POST["b_$id"] ?? ''); $betul = false;
                foreach (explode(', ', strtolower($data['jawapan'])) as $kunci) { if (!empty($jawapan_p) && strpos(strtolower($jawapan_p), trim($kunci)) !== false) { $betul = true; break; } } ?>
                <div class="card <?php echo $betul ? 'is-correct' : 'is-wrong'; ?>">
                    <div class="question-text"><?php echo "B$id. " . $data['soalan']; ?></div>
                    <p>Jawapan: <strong><?php echo $jawapan_p ?: 'Tiada'; ?></strong></p>
                    <p style="color: #95a5a6; font-size: 0.85rem; margin-top:5px;">Skema: <?php echo $data['jawapan']; ?></p>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <form action="" method="POST">
                <div class="section-badge">Bahagian A: Objektif</div>
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

                <div class="section-badge">Bahagian B: Subjektif</div>
                <?php foreach ($bahagian_b as $id => $item): ?>
                    <div class="card">
                        <div class="question-text"><?php echo "$id. " . $item['soalan']; ?></div>
                        <?php if(!empty($item['gambar'])): ?><img src="<?php echo $item['gambar']; ?>" class="img-rajah"><?php endif; ?>
                        <input type="text" name="b_<?php echo $id; ?>" placeholder="Taip jawapan pendek..." required>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn-submit">Hantar Jawapan</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>&copy; 2025 Sains SVM 1 KuVocC | <span style="color: var(--primary-soft);">Mellenny Britney</span></footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
</body>
</html>




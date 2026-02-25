<?php
session_start();
include 'config.php';

// Jika belum login atau parameter tidak cukup, hantar balik ke rekod.php
if (!isset($_SESSION['id']) || !isset($_GET['bab']) || !isset($_GET['percubaan'])) {
    header("Location: rekod.php");
    exit();
}

$pelajar_id = $_SESSION['id'];
$bab = $_GET['bab'];
$percubaan = $_GET['percubaan'];

// Ambil data jawapan menggunakan nama database lengkap untuk mengelakkan ralat 'Unknown Column'
$sql = $conn->prepare("SELECT jawapan_pelajar, markah FROM db_pengguna.rekod_kuiz WHERE pelajar_id = ? AND bab = ? AND percubaan = ?");
$sql->bind_param("isi", $pelajar_id, $bab, $percubaan);
$sql->execute();
$result = $sql->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "<script>alert('Rekod tidak ditemui!'); window.location.href='rekod.php';</script>";
    exit();
}

// Tukar format JSON kepada Array PHP
$jawapan_array = json_decode($data['jawapan_pelajar'], true);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semakan Jawapan | <?php echo htmlspecialchars($bab); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6c5ce7;
            --bg-body: #f4f7fe;
            --correct: #27ae60;
            --wrong: #eb4d4b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-body); 
            padding: 20px;
            color: #2d3436;
        }

        .container { 
            max-width: 800px; 
            margin: 40px auto; 
            background: white; 
            padding: 40px; 
            border-radius: 24px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
        }

        h1 { 
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .info-card { 
            margin-bottom: 30px; 
            padding: 20px; 
            background: #f0edff; 
            border-radius: 15px; 
            border-left: 5px solid var(--primary);
        }

        .info-card p { margin: 5px 0; font-weight: 600; }

        .soalan-box { 
            border-bottom: 1px solid #eee; 
            padding: 20px 0; 
        }

        .soalan-text { font-weight: 700; margin-bottom: 10px; display: block; }
        
        .jawapan-anda { font-weight: 600; }
        .betul { color: var(--correct); }
        .salah { color: var(--wrong); }

        .jawapan-sebenar {
            margin-top: 8px;
            font-size: 0.9rem;
            color: #636e72;
            background: #f9f9f9;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
        }

        .btn-back { 
            display: inline-block; 
            margin-top: 30px; 
            padding: 12px 25px; 
            background: var(--primary); 
            color: white; 
            text-decoration: none; 
            border-radius: 12px; 
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Semakan Jawapan</h1>
    <p style="color: #636e72; margin-bottom: 25px;">Lihat semula prestasi dan jawapan anda.</p>

    <div class="info-card">
        <p>Bab: <?php echo htmlspecialchars($bab); ?></p>
        <p>Percubaan: Ke-<?php echo $percubaan; ?></p>
        <p>Markah: <span style="color: var(--primary); font-size: 1.2rem;"><?php echo $data['markah']; ?>/100</span></p>
    </div>

    <div class="senarai-jawapan">
        <?php if (!empty($jawapan_array)): ?>
            <?php foreach ($jawapan_array as $index => $item): ?>
                <div class="soalan-box">
                    <span class="soalan-text">Soalan <?php echo $index + 1; ?>: <?php echo htmlspecialchars($item['soalan']); ?></span>
                    
                    <p class="jawapan-anda">
                        Jawapan Anda: 
                        <span class="<?php echo ($item['status'] == 'betul') ? 'betul' : 'salah'; ?>">
                            <?php echo htmlspecialchars($item['jawapan_anda']); ?>
                        </span>
                    </p>

                    <?php if ($item['status'] == 'salah'): ?>
                        <div class="jawapan-sebenar">
                            <strong>Jawapan Betul:</strong> <?php echo htmlspecialchars($item['jawapan_sebenar']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 30px;">
                <p>Maaf, data jawapan terperinci tidak ditemui atau format tidak sah.</p>
            </div>
        <?php endif; ?>
    </div>

    <a href="rekod.php" class="btn-back">← Kembali ke Rekod</a>
</div>

</body>
</html>


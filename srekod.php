<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: logmasuk.php");
    exit();
}

$id = $_SESSION['id'];
$sql = $conn->prepare("SELECT username, gambar FROM pengguna WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
$data = $sql->get_result()->fetch_assoc();

$nama = $data['username'];
$gambar = (!empty($data['gambar'])) ? $data['gambar'] : "profail.jpg";

// dapatkan markah dari URL
$markah = isset($_GET['markah']) ? $_GET['markah'] : 0;
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keputusan Kuiz</title>

<style>
    body{
        margin:0;
        font-family:Poppins, sans-serif;
        text-align:center;
        background:white;
    }

    header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:20px 30px;
        background:white;
    }

    .logo img{
        height:70px;
    }

    .profile-btn img{
        width:55px;
        height:55px;
        border-radius:50%;
        border:3px solid #33aaff;
        object-fit:cover;
    }

    /* Kotak keputusan */
    .result-box{
        margin:60px auto;
        width:75%;
        padding:80px 30px;
        background:white;
        border-radius:45px;
        box-shadow:0px 0px 20px rgba(0,0,0,0.2);
    }

    .result-box h1{
        font-size:60px;
        font-weight:800;
    }

    .result-box p{
        font-size:30px;
        font-weight:600;
        margin-top:20px;
    }

    /* Buttons */
    .btn-area{
        margin-top:50px;
        display:flex;
        justify-content:center;
        gap:60px;
    }

    .btn-area a{
        background:#42a5ff;
        padding:15px 35px;
        font-size:25px;
        border-radius:20px;
        color:white;
        text-decoration:none;
    }
</style>

</head>
<body>

<header>
    <div class="logo"><img src="Logo.jpg"></div>
    <a href="logmasuk.php" class="profile-btn"><img src="uploads/<?php echo $gambar; ?>"></a>
</header>

<div class="result-box">
    <h1>Tahniah, <?php echo $nama; ?>!</h1>

    <p>Markah anda : <b><?php echo $markah; ?></b></p>

    <div class="btn-area">
        <a href="kuiz.php">Cuba lagi</a>
        <a href="rekod.php">Lihat rekod</a>
    </div>
</div>

</body>
</html>

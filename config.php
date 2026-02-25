<?php
// 1. Cuba kesan Host Railway (Sama ada MYSQLHOST atau MYSQL_HOST)
$railwayHost = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST');

if ($railwayHost) {
    // Tetapan untuk Railway - Menggunakan nama variable yang tepat seperti di dashboard anda
    $servername = $railwayHost;
    $dbusername = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
    $dbpassword = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD');
    $dbname     = getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'railway';
    $dbport     = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: '3306';
} else {
    // Tetapan untuk Localhost (XAMPP/WAMP)
    $servername = "localhost";
    $dbusername = "root"; 
    $dbpassword = "";     
    $dbname     = "db_pengguna";
    $dbport     = "3306";
}

// 2. Tambah error reporting untuk memudahkan debugging jika gagal
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 3. Sambungan dengan parameter port
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname, $dbport);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Sambungan Gagal! Sila semak tetapan database: " . $e->getMessage());
}
?>

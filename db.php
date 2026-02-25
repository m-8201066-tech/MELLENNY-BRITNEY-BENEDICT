<?php
$host = "localhost";
$dbname = "db_pengguna";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

CREATE DATABASE e_pembelajaran_sains;
USE e_pembelajaran_sains;

CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    kata_laluan VARCHAR(255) NOT NULL,
    peranan ENUM('pelajar', 'pensyarah') DEFAULT 'pelajar',
    kelas varchar(255)
);



CREATE TABLE nota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tajuk VARCHAR(255) NOT NULL,
    kandungan TEXT NOT NULL,
    fail_url VARCHAR(255),
    tarikh_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kuiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    soalan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    jawapan_betul CHAR(1) NOT NULL   -- A / B / C / D
);

CREATE TABLE keputusan_kuiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    markah INT NOT NULL,
    tarikh_jawab TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (nama, email, kata_laluan, peranan)
VALUES ('Mellenny', 'm-8201066@moe-dl.edu.my', '12345678', 'pelajar');

INSERT INTO kuiz (soalan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawapan_betul)
VALUES
('Antara berikut, apakah peralatan yang digunakan untuk melindung mata dalam makmal?', 'topeng muka', 'Gogal', 'Sarung tamgan', 'Baju makmal', 'B'),
('Apakah simbol kimia bagi air?', 'O2', 'CO2', 'H2O', 'NaCl', 'C');

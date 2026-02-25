<?php
session_start();
include 'config.php'; 

// Paparan ralat (boleh dipadam selepas projek siap)
error_reporting(E_ALL); 
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // PEMBETULAN: Cari berdasarkan username sahaja dahulu
    $sql = $conn->prepare("SELECT id, username, password, peranan, emel, svm, semester FROM pengguna WHERE username = ?");

    if ($sql === false) {
         echo "<script>alert('Ralat SQL! Sila semak query: " . $conn->error . "'); window.history.back();</script>";
         exit();
    }
    
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();
    
    // Jika username dijumpai
    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        
        // PEMBETULAN: Gunakan password_verify untuk semak kata laluan
        if (password_verify($password, $data['password'])) {
            
            $peranan_db = strtolower($data['peranan']);

            $_SESSION['id']       = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['peranan']  = $peranan_db;
            $_SESSION['kursus'] = ''; 

            if ($peranan_db == "pensyarah") { 
                // Dapatkan Kursus Pensyarah
                $sql_kursus = $conn->prepare("SELECT k.kod_kursus FROM kursus k JOIN pengajaran_kursus pk ON k.id = pk.kursus_id WHERE pk.pensyarah_id = ?");
                $sql_kursus->bind_param("i", $data['id']);
                $sql_kursus->execute();
                $result_kursus = $sql_kursus->get_result();
                
                $kursus_list = [];
                while($kursus_row = $result_kursus->fetch_assoc()){
                     $kursus_list[] = $kursus_row['kod_kursus'];
                }
                $_SESSION['kursus'] = implode(",", $kursus_list); 
                header("Location: pensyarah.php");
                exit();

            } else if ($peranan_db == "pelajar") { 
                // Dapatkan Kursus Pelajar
                $sql_kursus = $conn->prepare("SELECT k.kod_kursus FROM kursus k JOIN pendaftaran_kursus pdk ON k.id = pdk.kursus_id WHERE pdk.pelajar_id = ?");
                $sql_kursus->bind_param("i", $data['id']);
                $sql_kursus->execute();
                $result_kursus = $sql_kursus->get_result();
                
                if ($kursus_row = $result_kursus->fetch_assoc()){
                     $_SESSION['kursus'] = $kursus_row['kod_kursus'];
                }
                header("Location: index.php");
                exit();
            }
        } else {
            // Jika password salah
            echo "<script>alert('Username atau password salah!'); window.history.back();</script>";
            exit();
        }

    } else {
        // Jika username tidak wujud
        echo "<script>alert('Username atau password salah!'); window.history.back();</script>";
        exit();
    }
    $sql->close(); 
}
?>

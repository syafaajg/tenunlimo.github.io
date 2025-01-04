<?php
session_start();

// Cek apakah user sudah login dan adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

if (!isset($_GET['file'])) {
    $_SESSION['error'] = "File tidak ditemukan";
    header('Location: collection.php');
    exit();
}

$filename = $_GET['file'];
$filepath = "../assets/img/collections/" . $filename;

try {
    // Validasi path file
    $realPath = realpath($filepath);
    $collectionsDir = realpath("../assets/img/collections");
    
    // Pastikan file berada dalam direktori collections
    if ($realPath === false || strpos($realPath, $collectionsDir) !== 0) {
        throw new Exception("File tidak valid");
    }
    
    // Cek apakah file ada
    if (!file_exists($filepath)) {
        throw new Exception("File tidak ditemukan");
    }
    
    // Hapus file
    if (!unlink($filepath)) {
        throw new Exception("Gagal menghapus file");
    }
    
    $_SESSION['success'] = "Gambar berhasil dihapus";
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: collection.php');
exit(); 
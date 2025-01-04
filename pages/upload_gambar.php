<?php
session_start();

// Cek apakah user sudah login dan adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: collection.php');
    exit();
}

// Konfigurasi upload
$targetDir = "../assets/img/collections/";
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

try {
    // Validasi file yang diupload
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Terjadi kesalahan saat upload file");
    }

    $file = $_FILES['gambar'];
    
    // Validasi tipe file
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Tipe file tidak diizinkan. Hanya file JPG, PNG, dan GIF yang diperbolehkan");
    }
    
    // Validasi ukuran file
    if ($file['size'] > $maxSize) {
        throw new Exception("Ukuran file terlalu besar. Maksimal 5MB");
    }
    
    // Buat nama file unik
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = uniqid('tenun_') . '.' . $fileExtension;
    $targetPath = $targetDir . $fileName;
    
    // Cek dan buat direktori jika belum ada
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            throw new Exception("Gagal membuat direktori upload");
        }
    }
    
    // Pindahkan file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception("Gagal mengupload file");
    }
    
    $_SESSION['success'] = "Gambar berhasil diupload";
    
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: collection.php');
exit(); 
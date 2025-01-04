<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

require_once '../../config/database.php';

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID barang tidak ditemukan";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

try {
    // Cek apakah barang masih memiliki riwayat transaksi
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) as count FROM barang_masuk WHERE id_barang = ?");
    $stmtCheck->execute([$id]);
    $masukCount = $stmtCheck->fetch()['count'];
    
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) as count FROM barang_keluar WHERE id_barang = ?");
    $stmtCheck->execute([$id]);
    $keluarCount = $stmtCheck->fetch()['count'];
    
    if ($masukCount > 0 || $keluarCount > 0) {
        $_SESSION['error'] = "Barang tidak dapat dihapus karena memiliki riwayat transaksi";
        header('Location: index.php');
        exit();
    }
    
    // Hapus barang jika tidak ada riwayat transaksi
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Data barang berhasil dihapus";
    } else {
        $_SESSION['error'] = "Data barang tidak ditemukan";
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
}

header('Location: index.php');
exit(); 
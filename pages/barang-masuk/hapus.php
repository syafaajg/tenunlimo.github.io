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
    $_SESSION['error'] = "ID barang masuk tidak ditemukan";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Ambil data barang masuk yang akan dihapus
    $stmt = $pdo->prepare("SELECT id_barang, jumlah FROM barang_masuk WHERE id = ?");
    $stmt->execute([$id]);
    $barangMasuk = $stmt->fetch();

    if (!$barangMasuk) {
        throw new Exception("Data barang masuk tidak ditemukan");
    }

    // Kurangi stok barang
    $stmtUpdateStok = $pdo->prepare("
        UPDATE barang 
        SET stok = stok - ? 
        WHERE id = ?
    ");
    $stmtUpdateStok->execute([$barangMasuk['jumlah'], $barangMasuk['id_barang']]);

    // Hapus data barang masuk
    $stmtDelete = $pdo->prepare("DELETE FROM barang_masuk WHERE id = ?");
    $stmtDelete->execute([$id]);

    $pdo->commit();
    $_SESSION['success'] = "Data barang masuk berhasil dihapus";

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
}

header('Location: index.php');
exit(); 
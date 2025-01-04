<?php
session_start();
require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Ambil data barang keluar
        $stmt = $pdo->prepare("SELECT id_barang, jumlah FROM barang_keluar WHERE id = ?");
        $stmt->execute([$id]);
        $barang_keluar = $stmt->fetch();

        // Mulai transaksi
        $pdo->beginTransaction();

        // Kembalikan stok
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
        $stmt->execute([$barang_keluar['jumlah'], $barang_keluar['id_barang']]);

        // Hapus data barang keluar
        $stmt = $pdo->prepare("DELETE FROM barang_keluar WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        $_SESSION['success'] = "Data barang keluar berhasil dihapus";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
}

header('Location: index.php');
exit; 
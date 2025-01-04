<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];

    try {
        // Ambil data barang keluar lama
        $stmt = $pdo->prepare("SELECT id_barang, jumlah FROM barang_keluar WHERE id = ?");
        $stmt->execute([$id]);
        $old_data = $stmt->fetch();

        // Mulai transaksi
        $pdo->beginTransaction();

        // Kembalikan stok lama
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
        $stmt->execute([$old_data['jumlah'], $old_data['id_barang']]);

        // Cek stok setelah pengembalian
        $stmt = $pdo->prepare("SELECT stok FROM barang WHERE id = ?");
        $stmt->execute([$id_barang]);
        $barang = $stmt->fetch();

        if ($barang['stok'] < $jumlah) {
            throw new Exception("Stok tidak mencukupi!");
        }

        // Update data barang keluar
        $stmt = $pdo->prepare("UPDATE barang_keluar SET id_barang = ?, jumlah = ?, tanggal = ?, keterangan = ? WHERE id = ?");
        $stmt->execute([$id_barang, $jumlah, $tanggal, $keterangan, $id]);

        // Kurangi stok baru
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
        $stmt->execute([$jumlah, $id_barang]);

        $pdo->commit();
        $_SESSION['success'] = "Data barang keluar berhasil diupdate";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Metode tidak diizinkan";
}

header('Location: index.php');
exit; 
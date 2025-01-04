<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $username = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $userId = $_SESSION['user_id'];

    // Validasi input
    if (empty($username) || empty($nama_lengkap)) {
        $_SESSION['error'] = "Username dan nama lengkap harus diisi!";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        // Cek username sudah dipakai atau belum
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Username sudah digunakan!";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Update profil
        $stmt = $pdo->prepare("UPDATE users SET username = ?, nama_lengkap = ? WHERE id = ?");
        $stmt->execute([$username, $nama_lengkap, $userId]);

        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['success'] = "Profil berhasil diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui profil!";
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?> 
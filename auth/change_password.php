<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $userId = $_SESSION['user_id'];

    try {
        // Validasi password baru
        if ($newPassword !== $confirmPassword) {
            throw new Exception("Konfirmasi password tidak cocok!");
        }

        if (strlen($newPassword) < 6) {
            throw new Exception("Password minimal 6 karakter!");
        }

        // Cek password lama
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!password_verify($oldPassword, $user['password'])) {
            throw new Exception("Password lama tidak sesuai!");
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        $_SESSION['success'] = "Password berhasil diubah!";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?> 
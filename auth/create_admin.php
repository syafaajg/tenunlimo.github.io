<?php
require_once '../config/database.php';

try {
    // Hapus user admin yang ada (jika ada)
    $stmt = $pdo->prepare("DELETE FROM users WHERE role = 'admin'");
    $stmt->execute();
    
    // Reset auto increment
    $pdo->query("ALTER TABLE users AUTO_INCREMENT = 1");
    
    // Buat password hash yang baru
    $username = 'tenunlimo4360';
    $password = '20001436';
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert admin baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password_hash, 'Administrator', 'admin']);
    
    echo "Admin berhasil dibuat:<br>";
    echo "Username: tenunlimo4360<br>";
    echo "Password: 20001436<br>";
    echo "<br>Silahkan <a href='/tenun-limo/auth/login.php'>Login</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} 
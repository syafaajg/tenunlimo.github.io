<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simpan pesan sebelum menghapus session
$_SESSION['success'] = "Anda berhasil logout!";

// Hapus semua data session
session_destroy();

// Mulai session baru untuk menampilkan pesan
session_start();
$_SESSION['success'] = "Anda berhasil logout!";

// Redirect ke halaman login
header('Location: /tenun-limo/pages/dashboard.php');
exit; 
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Tenun Limo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="/tenun-limo/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <i class='bx bx-grid-alt fs-4 me-3'></i>
                <a class="navbar-brand" href="/tenun-limo/pages/dashboard.php">Tenun Limo</a>
            </div>
            <div class="navbar-nav flex-row">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a class="nav-link px-3" href="/tenun-limo/pages/dashboard.php">
                        <i class='bx bx-home-alt me-2'></i>Home
                    </a>
                    <a class="nav-link px-3" href="/tenun-limo/pages/stok/index.php">
                        <i class='bx bx-package me-2'></i>Stok Barang
                    </a>
                    <a class="nav-link px-3" href="/tenun-limo/pages/barang-masuk/index.php">
                        <i class='bx bx-download me-2'></i>Barang Masuk
                    </a>
                    <a class="nav-link px-3" href="/tenun-limo/pages/barang-keluar/index.php">
                        <i class='bx bx-upload me-2'></i>Barang Keluar
                    </a>
                    <!-- Tombol Logout -->
                    <a class="nav-link px-3 ms-auto" href="/tenun-limo/auth/logout.php" onclick="return confirm('Yakin ingin keluar?')">
                        <i class='bx bx-log-out me-2'></i>Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link px-3" href="/tenun-limo/pages/dashboard.php">
                        <i class='bx bx-home-alt me-2'></i>Home
                    </a>
                    <a class="nav-link px-3" href="/tenun-limo/pages/collection.php">
                        <i class='bx bx-collection me-2'></i>Koleksi
                    </a>
                    <a class="nav-link px-3 ms-auto" href="/tenun-limo/auth/login.php">
                        <i class='bx bx-log-in me-2'></i>Login Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Tambahkan script untuk menampilkan pesan sukses/error -->
    <script>
    <?php if (isset($_SESSION['success'])): ?>
        alert("<?= $_SESSION['success'] ?>");
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        alert("<?= $_SESSION['error'] ?>");
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
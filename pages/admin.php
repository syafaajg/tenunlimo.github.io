<?php
session_start();
require_once '../config/database.php';
include '../includes/header.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Ambil data admin
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0">
                        <i class='bx bxs-user-circle me-2'></i>Profil Admin
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Tampilkan pesan sukses/error -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Update Username -->
                    <form action="../auth/update_profile.php" method="POST" class="mb-4">
                        <h5 class="border-bottom pb-2">Informasi Akun</h5>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($admin['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save me-1'></i>Simpan Perubahan
                        </button>
                    </form>

                    <!-- Form Update Password -->
                    <form action="../auth/change_password.php" method="POST">
                        <h5 class="border-bottom pb-2">Ubah Kata Sandi</h5>
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Kata Sandi Saat Ini</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-lock me-1'></i>Ubah Kata Sandi
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class='bx bx-arrow-back me-1'></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi form password
    document.querySelector('form[action="../auth/change_password.php"]').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Konfirmasi kata sandi baru tidak cocok!');
        }
        
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('Kata sandi baru minimal 6 karakter!');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?> 
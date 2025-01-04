<?php
session_start();
require_once '../config/database.php';
include '../includes/header.php';

// Array gambar background menggunakan Unsplash API
$backgroundImages = [
    'https://source.unsplash.com/1600x900/?traditional-weaving' => 'Tenun Tradisional',
    'https://source.unsplash.com/1600x900/?textile-making' => 'Proses Pembuatan',
    'https://source.unsplash.com/1600x900/?textile-pattern' => 'Detail Motif',
    'https://source.unsplash.com/1600x900/?textile-art' => 'Pameran Tenun',
    'https://source.unsplash.com/1600x900/?traditional-fabric' => 'Produk Jadi'
];

// Untuk produk unggulan
$featuredProducts = [
    [
        'image' => 'https://source.unsplash.com/800x600/?traditional-textile',
        'title' => 'Tenun Ikat',
        'description' => 'Motif tradisional dengan warna alami'
    ],
    [
        'image' => '../assets/img/songket.jg',
        'title' => 'Tenun Songket',
        'description' => 'Perpaduan benang emas modern'
    ],
    [
        'image' => 'https://source.unsplash.com/800x600/?woven-fabric',
        'title' => 'Tenun Lurik',
        'description' => 'Desain minimalis kontemporer'
    ]
];

// Ambil total koleksi untuk publik
$stmtStok = $pdo->query("SELECT COUNT(*) as total FROM barang");
$totalBarang = $stmtStok->fetch()['total'];

// Ambil data tambahan jika admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $stmtMasuk = $pdo->query("SELECT SUM(jumlah) as total FROM barang_masuk WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())");
    $totalMasuk = $stmtMasuk->fetch()['total'] ?? 0;

    $stmtKeluar = $pdo->query("SELECT SUM(jumlah) as total FROM barang_keluar WHERE MONTH(tanggal) = MONTH(CURRENT_DATE())");
    $totalKeluar = $stmtKeluar->fetch()['total'] ?? 0;
}
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-decoration"></div>
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Tenun Limo</h1>
            <p class="hero-subtitle">
                Melestarikan warisan budaya melalui keindahan tenun tradisional
                dengan sentuhan modern yang menghadirkan keunikan dalam setiap karya
            </p>
            <div class="hero-buttons">
                <a href="/tenun-limo/pages/collection.php" class="btn-hero primary">
                    Lihat Koleksi
                </a>
                <a href="#about" class="btn-hero outline">
                    Tentang Kami
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Section -->
<div class="stats-section py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-4 mb-4">
                <div class="stat-card">
                    <i class='bx bx-collection mb-3'></i>
                    <h3><?= $totalBarang ?></h3>
                    <p>Total Koleksi</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card">
                    <i class='bx bx-purchase-tag mb-3'></i>
                    <h3>100+</h3>
                    <p>Motif Unik</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card">
                    <i class='bx bx-happy-heart-eyes mb-3'></i>
                    <h3>1000+</h3>
                    <p>Pelanggan Puas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Produk Unggulan Section -->
<div class="featured-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Produk Unggulan</h2>
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="featured-card">
                    <img src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>">
                    <div class="featured-content">
                        <h4><?= $product['title'] ?></h4>
                        <p><?= $product['description'] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- About Section -->
<div id="about" class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="../assets/img/logotenun.png" 
                     alt="Tentang Kami" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="mb-4">Tentang Tenun Limo</h2>
                <p class="lead mb-4">Menjaga Tradisi, Menghadirkan Inovasi</p>
                <p class="mb-4">Tenun Limo adalah pusat tenun tradisional yang berkomitmen untuk melestarikan warisan budaya Indonesia melalui produksi kain tenun berkualitas tinggi.</p>
                <div class="row g-4">
                    <div class="col-6">
                        <div class="feature-item">
                            <i class='bx bx-check-shield'></i>
                            <h5>Kualitas Premium</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-item">
                            <i class='bx bx-paint'></i>
                            <h5>Motif Tradisional</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-item">
                            <i class='bx bx-detail'></i>
                            <h5>Pengerjaan Teliti</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-item">
                            <i class='bx bx-certification'></i>
                            <h5>Bahan Terbaik</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3">Tenun Limo</h5>
                <p>Melestarikan warisan budaya Indonesia melalui keindahan tenun tradisional.</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class='bx bxl-facebook'></i></a>
                    <a href="#" class="text-white me-3"><i class='bx bxl-instagram'></i></a>
                    <a href="#" class="text-white me-3"><i class='bx bxl-whatsapp'></i></a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3">Kontak</h5>
                <p><i class='bx bx-map me-2'></i> Desa Troso RT 02/ RW 03, Jepara Jawa Tengah</p>
                <p><i class='bx bx-phone me-2'></i> 082 333 859 741</p>
                <p><i class='bx bx-envelope me-2'></i> novialimo@gmail.com</p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3">Jam Operasional</h5>
                <p>Senin - Jumat: 08:00 - 17:00</p>
                <p>Sabtu: 09:00 - 15:00</p>
                <p>Minggu: Tutup</p>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Tenun Limo. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Modal Ubah Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="changePasswordModalLabel">Ubah Kata Sandi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm" action="../auth/change_password.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Kata Sandi Saat Ini</label>
                        <input type="password" class="form-control bg-dark text-white border-secondary" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Kata Sandi Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-secondary" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" class="form-control bg-dark text-white border-secondary" id="confirmPassword" name="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const totalSlides = slides.length;
    
    // Fungsi untuk menampilkan slide
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
    }
    
    // Fungsi untuk slide berikutnya
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }
    
    // Fungsi untuk slide sebelumnya
    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }
    
    // Event listeners untuk tombol kontrol
    document.querySelector('.next-slide').addEventListener('click', nextSlide);
    document.querySelector('.prev-slide').addEventListener('click', prevSlide);
    
    // Autoplay slideshow
    let slideInterval = setInterval(nextSlide, 5000);
    
    // Pause autoplay saat hover
    const heroSection = document.querySelector('.hero-section');
    heroSection.addEventListener('mouseenter', () => clearInterval(slideInterval));
    heroSection.addEventListener('mouseleave', () => {
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    });
    
    // Tampilkan slide pertama
    showSlide(0);
});
</script>

<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
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
</script>

<?php include '../includes/footer.php'; ?> 
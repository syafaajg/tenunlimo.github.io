<?php
session_start();
require_once '../config/database.php';
include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Koleksi Tenun</h2>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahGambarModal">
                <i class='bx bx-plus'></i> Tambah Gambar
            </button>
        <?php endif; ?>
    </div>

    <!-- Grid Gambar Koleksi -->
    <div class="collection-grid">
        <?php
        // Ambil semua gambar dari direktori
        $gambarDir = "../assets/img/collections/";
        $gambar = glob($gambarDir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        
        if (empty($gambar)): ?>
            <div class="text-center w-100 py-5">
                <i class='bx bx-image' style='font-size: 48px; color: #ccc;'></i>
                <p class="mt-3 text-muted">Belum ada koleksi yang ditampilkan</p>
            </div>
        <?php else:
            foreach ($gambar as $file):
                $filename = basename($file);
        ?>
            <div class="collection-item">
                <img src="/tenun-limo/assets/img/collections/<?= $filename ?>" 
                     alt="Koleksi Tenun"
                     class="img-fluid"
                     data-bs-toggle="modal"
                     data-bs-target="#imageModal"
                     data-bs-img="/tenun-limo/assets/img/collections/<?= $filename ?>">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="collection-actions">
                        <a href="hapus_gambar.php?file=<?= $filename ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus gambar ini?')">
                            <i class='bx bx-trash'></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php 
            endforeach;
        endif; 
        ?>
    </div>
</div>

<!-- Modal untuk preview gambar -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>
                <img src="" class="img-fluid" id="modalImage">
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk tambah gambar (hanya untuk admin) -->
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="modal fade" id="tambahGambarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Gambar Koleksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="upload_gambar.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Pilih Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" 
                                   accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG, GIF (Max. 5MB)</small>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- CSS tambahan -->
<style>
.collection-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.collection-item {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    background: #fff;
}

.collection-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.collection-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    cursor: pointer;
}

.collection-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.collection-item:hover .collection-actions {
    opacity: 1;
}

#modalImage {
    width: 100%;
    height: auto;
    max-height: 90vh;
    object-fit: contain;
}
</style>

<!-- JavaScript untuk modal -->
<script>
document.getElementById('imageModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const imgSrc = button.getAttribute('data-bs-img');
    document.getElementById('modalImage').src = imgSrc;
});
</script>

<?php include '../includes/footer.php'; ?> 
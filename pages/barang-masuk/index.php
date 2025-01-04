<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

require_once '../../config/database.php';
include '../../includes/header.php';

// Ambil data barang masuk dengan join ke tabel barang
$stmt = $pdo->query("
    SELECT bm.*, b.nama_barang, b.kode_barang 
    FROM barang_masuk bm
    JOIN barang b ON bm.id_barang = b.id
    ORDER BY bm.tanggal DESC
");
$barangMasuk = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Barang Masuk</h2>
        <a href="tambah.php" class="btn btn-primary">
            <i class='bx bx-plus'></i> Tambah Barang Masuk
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barangMasuk as $index => $bm): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('d/m/Y', strtotime($bm['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($bm['kode_barang']) ?></td>
                                <td><?= htmlspecialchars($bm['nama_barang']) ?></td>
                                <td><?= $bm['jumlah'] ?></td>
                                <td><?= htmlspecialchars($bm['keterangan']) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $bm['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $bm['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($barangMasuk)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data barang masuk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 
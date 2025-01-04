<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Ambil data barang dengan join ke tabel jenis_kain
$stmt = $pdo->query("
    SELECT b.*, j.nama_jenis 
    FROM barang b
    LEFT JOIN jenis_kain j ON b.id_jenis = j.id
    ORDER BY b.kode_barang
");
$barang = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Stok Barang</h2>
        <a href="tambah.php" class="btn btn-primary">
            <i class='bx bx-plus'></i> Tambah Barang
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jenis Kain</th>
                            <th>Stok</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barang as $index => $b): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($b['kode_barang']) ?></td>
                                <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($b['nama_jenis'] ?? '-') ?></td>
                                <td><?= $b['stok'] ?></td>
                                <td>Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $b['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class='bx bx-trash'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($barang)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data barang</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 
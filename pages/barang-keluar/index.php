<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Ambil data barang keluar dengan join ke tabel barang
$stmt = $pdo->query("
    SELECT bk.*, b.nama_barang, b.kode_barang 
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id
    ORDER BY bk.tanggal DESC
");
$barangKeluar = $stmt->fetchAll();

// Hitung total pendapatan
$stmt = $pdo->query("SELECT SUM(total_harga) as total FROM barang_keluar");
$totalPendapatan = $stmt->fetch()['total'] ?? 0;
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Barang Keluar</h2>
        <div>
            <h4 class="text-success">Total Pendapatan: Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h4>
            <div class="btn-group">
                <a href="tambah.php" class="btn btn-primary">
                    <i class='bx bx-plus'></i> Tambah Barang Keluar
                </a>
                <a href="rekap.php" class="btn btn-info">
                    <i class='bx bx-bar-chart-alt-2'></i> Lihat Rekap
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                            <th>Harga Jual</th>
                            <th>Total Harga</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barangKeluar as $index => $bk): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('d/m/Y', strtotime($bk['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($bk['kode_barang']) ?></td>
                                <td><?= htmlspecialchars($bk['nama_barang']) ?></td>
                                <td><?= $bk['jumlah'] ?></td>
                                <td>Rp <?= number_format($bk['harga_jual'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($bk['total_harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($bk['keterangan']) ?></td>
                                <td>
                                    <a href="cetak_nota.php?id=<?= $bk['id'] ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                    <a href="edit.php?id=<?= $bk['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($barangKeluar)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data barang keluar</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 
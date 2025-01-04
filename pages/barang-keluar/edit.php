<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID transaksi tidak ditemukan";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Ambil data barang keluar
$stmt = $pdo->prepare("
    SELECT bk.*, b.kode_barang, b.nama_barang 
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id
    WHERE bk.id = ?
");
$stmt->execute([$id]);
$barangKeluar = $stmt->fetch();

if (!$barangKeluar) {
    $_SESSION['error'] = "Data transaksi tidak ditemukan";
    header('Location: index.php');
    exit();
}

// Proses form edit jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Hanya bisa edit keterangan
        $keterangan = $_POST['keterangan'];
        
        // Update keterangan
        $stmt = $pdo->prepare("UPDATE barang_keluar SET keterangan = ? WHERE id = ?");
        $stmt->execute([$keterangan, $id]);
        
        $_SESSION['success'] = "Keterangan transaksi berhasil diperbarui";
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Transaksi Barang Keluar</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class='bx bx-info-circle me-2'></i>
                        Hanya keterangan yang dapat diubah untuk menjaga integritas data transaksi.
                    </div>

                    <!-- Informasi Transaksi -->
                    <div class="mb-4">
                        <h6>Informasi Transaksi:</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Tanggal</th>
                                <td><?= date('d/m/Y H:i', strtotime($barangKeluar['tanggal'])) ?></td>
                            </tr>
                            <tr>
                                <th>Kode Barang</th>
                                <td><?= htmlspecialchars($barangKeluar['kode_barang']) ?></td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td><?= htmlspecialchars($barangKeluar['nama_barang']) ?></td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td><?= $barangKeluar['jumlah'] ?></td>
                            </tr>
                            <tr>
                                <th>Harga Jual</th>
                                <td>Rp <?= number_format($barangKeluar['harga_jual'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Total Harga</th>
                                <td>Rp <?= number_format($barangKeluar['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        </table>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($barangKeluar['keterangan']) ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 
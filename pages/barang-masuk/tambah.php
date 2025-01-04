<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        $kode_barang = $_POST['kode_barang'];
        $nama_barang = $_POST['nama_barang'];
        $jumlah = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];
        $tanggal = $_POST['tanggal'];

        // Cek apakah barang sudah ada di database
        $stmt = $pdo->prepare("SELECT id FROM barang WHERE kode_barang = ?");
        $stmt->execute([$kode_barang]);
        $barang = $stmt->fetch();

        if ($barang) {
            // Jika barang sudah ada, gunakan id yang ada
            $id_barang = $barang['id'];
            
            // Update stok barang
            $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
            $stmt->execute([$jumlah, $id_barang]);
        } else {
            // Jika barang belum ada, buat data barang baru
            $stmt = $pdo->prepare("INSERT INTO barang (kode_barang, nama_barang, stok) VALUES (?, ?, ?)");
            $stmt->execute([$kode_barang, $nama_barang, $jumlah]);
            $id_barang = $pdo->lastInsertId();
        }
        
        // Catat barang masuk
        $stmt = $pdo->prepare("INSERT INTO barang_masuk (tanggal, id_barang, jumlah, keterangan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tanggal, $id_barang, $jumlah, $keterangan]);
        
        $pdo->commit();
        $_SESSION['success'] = "Data barang masuk berhasil ditambahkan";
        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Barang Masuk</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 
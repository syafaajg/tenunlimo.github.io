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
    $_SESSION['error'] = "ID barang tidak ditemukan";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Ambil data barang yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    $_SESSION['error'] = "Data barang tidak ditemukan";
    header('Location: index.php');
    exit();
}

// Ambil daftar jenis kain untuk dropdown
$stmt = $pdo->query("SELECT * FROM jenis_kain ORDER BY nama_jenis");
$jenis_kain = $stmt->fetchAll();

// Proses form edit jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $kode_barang = $_POST['kode_barang'];
        $nama_barang = $_POST['nama_barang'];
        $id_jenis = $_POST['id_jenis'];
        $stok = $_POST['stok'];
        $harga_jual = $_POST['harga_jual'];

        // Validasi input
        if (empty($kode_barang) || empty($nama_barang) || $harga_jual < 0) {
            throw new Exception("Semua field harus diisi dengan benar!");
        }

        // Update data barang
        $stmt = $pdo->prepare("
            UPDATE barang 
            SET kode_barang = ?, nama_barang = ?, id_jenis = ?, stok = ?, harga_jual = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$kode_barang, $nama_barang, $id_jenis, $stok, $harga_jual, $id]);
        
        $_SESSION['success'] = "Data barang berhasil diperbarui";
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
                    <h5 class="card-title mb-0">Edit Data Barang</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="kode_barang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="kode_barang" name="kode_barang" 
                                   value="<?= htmlspecialchars($barang['kode_barang']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                                   value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_jenis" class="form-label">Jenis Kain</label>
                            <select class="form-select" id="id_jenis" name="id_jenis">
                                <option value="">Pilih Jenis Kain</option>
                                <?php foreach ($jenis_kain as $jenis): ?>
                                    <option value="<?= $jenis['id'] ?>" 
                                            <?= $jenis['id'] == $barang['id_jenis'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($jenis['nama_jenis']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" 
                                   value="<?= $barang['stok'] ?>" required min="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga_jual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" 
                                   value="<?= $barang['harga_jual'] ?>" required min="0">
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
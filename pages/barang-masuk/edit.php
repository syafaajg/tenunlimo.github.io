<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID barang masuk tidak ditemukan";
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Ambil data barang masuk yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM barang_masuk WHERE id = ?");
$stmt->execute([$id]);
$barangMasuk = $stmt->fetch();

if (!$barangMasuk) {
    $_SESSION['error'] = "Data barang masuk tidak ditemukan";
    header('Location: index.php');
    exit();
}

// Ambil daftar barang untuk dropdown
$stmtBarang = $pdo->query("SELECT id, kode_barang, nama_barang, stok FROM barang ORDER BY nama_barang");
$daftarBarang = $stmtBarang->fetchAll();

// Proses form edit jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $id_barang_baru = $_POST['id_barang'];
        $tanggal = $_POST['tanggal'];
        $jumlah_baru = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];
        
        // Ambil jumlah lama dan id_barang lama
        $jumlah_lama = $barangMasuk['jumlah'];
        $id_barang_lama = $barangMasuk['id_barang'];
        
        // Jika barang berbeda atau jumlah berubah, update stok
        if ($id_barang_baru != $id_barang_lama || $jumlah_baru != $jumlah_lama) {
            // Kembalikan stok barang lama
            $stmtUpdateStokLama = $pdo->prepare("
                UPDATE barang 
                SET stok = stok - ? 
                WHERE id = ?
            ");
            $stmtUpdateStokLama->execute([$jumlah_lama, $id_barang_lama]);
            
            // Update stok barang baru
            $stmtUpdateStokBaru = $pdo->prepare("
                UPDATE barang 
                SET stok = stok + ? 
                WHERE id = ?
            ");
            $stmtUpdateStokBaru->execute([$jumlah_baru, $id_barang_baru]);
        }
        
        // Update data barang masuk
        $stmt = $pdo->prepare("
            UPDATE barang_masuk 
            SET id_barang = ?, tanggal = ?, jumlah = ?, keterangan = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$id_barang_baru, $tanggal, $jumlah_baru, $keterangan, $id]);
        
        $pdo->commit();
        $_SESSION['success'] = "Data barang masuk berhasil diperbarui";
        header('Location: index.php');
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Data Barang Masuk</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="id_barang" class="form-label">Barang</label>
                            <select name="id_barang" id="id_barang" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($daftarBarang as $barang): ?>
                                    <option value="<?= $barang['id'] ?>" 
                                            <?= $barang['id'] == $barangMasuk['id_barang'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($barang['kode_barang'] . ' - ' . $barang['nama_barang']) ?> 
                                        (Stok: <?= $barang['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="<?= $barangMasuk['tanggal'] ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                   value="<?= $barangMasuk['jumlah'] ?>" required min="1">
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($barangMasuk['keterangan']) ?></textarea>
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
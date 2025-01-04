<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Ambil daftar barang untuk dropdown
$stmt = $pdo->query("SELECT id, kode_barang, nama_barang, stok, harga_jual FROM barang WHERE stok > 0 ORDER BY nama_barang");
$barang = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        $id_barang = $_POST['id_barang'];
        $jumlah = $_POST['jumlah'];
        $keterangan = $_POST['keterangan'];
        $tanggal = $_POST['tanggal'];

        // Ambil data barang dan harga jual
        $stmt = $pdo->prepare("SELECT stok, harga_jual FROM barang WHERE id = ?");
        $stmt->execute([$id_barang]);
        $barang_data = $stmt->fetch();

        if (!$barang_data) {
            throw new Exception("Data barang tidak ditemukan!");
        }

        // Cek stok tersedia
        if ($jumlah > $barang_data['stok']) {
            throw new Exception("Stok tidak mencukupi! Stok tersedia: " . $barang_data['stok']);
        }

        // Hitung total harga
        $harga_jual = $barang_data['harga_jual'];
        $total_harga = $jumlah * $harga_jual;
        
        // Update stok barang
        $stmt = $pdo->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
        $stmt->execute([$jumlah, $id_barang]);
        
        // Catat barang keluar
        $stmt = $pdo->prepare("INSERT INTO barang_keluar (tanggal, id_barang, jumlah, harga_jual, total_harga, keterangan) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tanggal, $id_barang, $jumlah, $harga_jual, $total_harga, $keterangan]);
        
        $pdo->commit();
        $_SESSION['success'] = "Data barang keluar berhasil ditambahkan";
        header('Location: index.php');
        exit();
    } catch(Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Barang Keluar</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Barang</label>
                            <select name="id_barang" class="form-select" required onchange="updateHargaJual(this)">
                                <option value="">Pilih Barang</option>
                                <?php foreach($barang as $item): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-harga="<?= $item['harga_jual'] ?>"
                                            data-stok="<?= $item['stok'] ?>">
                                        <?= htmlspecialchars($item['kode_barang'] . ' - ' . $item['nama_barang']) ?> 
                                        (Stok: <?= $item['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1" onchange="hitungTotal()">
                            <small class="text-muted">Stok tersedia: <span id="stok-tersedia">0</span></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input type="text" id="harga_jual_display" class="form-control" readonly>
                            <input type="hidden" name="harga_jual" id="harga_jual">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Harga</label>
                            <input type="text" id="total_harga_display" class="form-control" readonly>
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

<script>
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(angka);
}

function updateHargaJual(select) {
    const option = select.options[select.selectedIndex];
    const harga = option.dataset.harga;
    document.getElementById('harga_jual').value = harga;
    document.getElementById('harga_jual_display').value = formatRupiah(harga);
    document.getElementById('stok-tersedia').textContent = option.dataset.stok;
    hitungTotal();
}

function hitungTotal() {
    const jumlah = document.getElementById('jumlah').value;
    const harga = document.getElementById('harga_jual').value;
    const total = jumlah * harga;
    document.getElementById('total_harga_display').value = formatRupiah(total);
}
</script>

<?php include '../../includes/footer.php'; ?> 
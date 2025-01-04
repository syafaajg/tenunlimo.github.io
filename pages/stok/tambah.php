<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO barang (kode_barang, nama_barang, id_jenis, harga, stok) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['kode_barang'],
            $_POST['nama_barang'],
            $_POST['id_jenis'],
            $_POST['harga'],
            $_POST['stok']
        ]);
        header('Location: index.php');
        exit();
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$jenis_kain = $pdo->query("SELECT * FROM jenis_kain ORDER BY nama_jenis")->fetchAll();
?>

<div class="container mt-4">
    <h2>Tambah Barang Baru</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="col-md-6">
        <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Jenis Kain</label>
            <select name="id_jenis" class="form-control" required>
                <option value="">Pilih Jenis Kain</option>
                <?php foreach($jenis_kain as $jenis): ?>
                    <option value="<?= $jenis['id'] ?>"><?= htmlspecialchars($jenis['nama_jenis']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stok Awal</label>
            <input type="number" name="stok" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div> 
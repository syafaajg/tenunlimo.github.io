<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$stmt = $pdo->prepare("
    SELECT 
        b.kode_barang,
        b.nama_barang,
        j.nama_jenis,
        b.stok,
        COALESCE(SUM(bm.jumlah), 0) as total_masuk,
        COALESCE(SUM(bk.jumlah), 0) as total_keluar
    FROM barang b
    LEFT JOIN jenis_kain j ON b.id_jenis = j.id
    LEFT JOIN barang_masuk bm ON b.id = bm.id_barang 
        AND MONTH(bm.tanggal) = ? AND YEAR(bm.tanggal) = ?
    LEFT JOIN barang_keluar bk ON b.id = bk.id_barang 
        AND MONTH(bk.tanggal) = ? AND YEAR(bk.tanggal) = ?
    GROUP BY b.id
    ORDER BY b.nama_barang
");
$stmt->execute([$bulan, $tahun, $bulan, $tahun]);
$laporan = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Laporan Stok Barang</h2>
    
    <form class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <select name="bulan" class="form-control">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == $bulan ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="tahun" class="form-control">
                    <?php for($i = date('Y'); $i >= date('Y')-5; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == $tahun ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
            <div class="col-md-2">
                <a href="export.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
                   class="btn btn-success">Export Excel</a>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Stok Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($laporan as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                <td><?= htmlspecialchars($item['nama_jenis']) ?></td>
                <td><?= $item['stok'] - $item['total_masuk'] + $item['total_keluar'] ?></td>
                <td><?= $item['total_masuk'] ?></td>
                <td><?= $item['total_keluar'] ?></td>
                <td><?= $item['stok'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> 
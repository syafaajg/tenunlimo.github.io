<?php
session_start();
require_once '../../config/database.php';
include '../../includes/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Set tanggal default (hari ini)
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil rekap harian
$stmt = $pdo->prepare("
    SELECT bk.*, b.kode_barang, b.nama_barang, j.nama_jenis
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id
    LEFT JOIN jenis_kain j ON b.id_jenis = j.id
    WHERE DATE(bk.tanggal) = ?
    ORDER BY bk.tanggal DESC
");
$stmt->execute([$tanggal]);
$rekapHarian = $stmt->fetchAll();

// Hitung total pendapatan hari ini
$stmt = $pdo->prepare("
    SELECT SUM(total_harga) as total 
    FROM barang_keluar 
    WHERE DATE(tanggal) = ?
");
$stmt->execute([$tanggal]);
$totalHarian = $stmt->fetch()['total'] ?? 0;

// Ambil data untuk grafik mingguan (7 hari terakhir)
$stmt = $pdo->query("
    SELECT DATE(tanggal) as tanggal, SUM(total_harga) as total
    FROM barang_keluar
    WHERE tanggal >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)
    GROUP BY DATE(tanggal)
    ORDER BY tanggal
");
$dataGrafik = $stmt->fetchAll();

// Format data untuk Chart.js
$labels = [];
$data = [];
foreach ($dataGrafik as $row) {
    $labels[] = date('d/m', strtotime($row['tanggal']));
    $data[] = $row['total'];
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rekap Harian</h5>
                    <form class="mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label class="form-label">Pilih Tanggal:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>" onchange="this.form.submit()">
                            </div>
                        </div>
                    </form>
                    <h4 class="text-success">Total Pendapatan: Rp <?= number_format($totalHarian, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Grafik Pendapatan Mingguan</h5>
                    <canvas id="grafikPendapatan"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Detail Transaksi Tanggal <?= date('d/m/Y', strtotime($tanggal)) ?></h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rekapHarian as $index => $rekap): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= date('H:i', strtotime($rekap['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($rekap['kode_barang']) ?></td>
                                <td><?= htmlspecialchars($rekap['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($rekap['nama_jenis'] ?? '-') ?></td>
                                <td><?= $rekap['jumlah'] ?></td>
                                <td>Rp <?= number_format($rekap['harga_jual'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($rekap['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <a href="cetak_nota.php?id=<?= $rekap['id'] ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class='bx bx-printer'></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rekapHarian)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada transaksi pada tanggal ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('grafikPendapatan').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Pendapatan',
            data: <?= json_encode($data) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                    }
                }
            }
        }
    }
});
</script>

<?php include '../../includes/footer.php'; ?> 
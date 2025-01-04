<?php
session_start();
require_once '../../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: /tenun-limo/auth/login.php');
    exit();
}

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Ambil data barang keluar
$stmt = $pdo->prepare("
    SELECT bk.*, b.kode_barang, b.nama_barang, j.nama_jenis
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id
    LEFT JOIN jenis_kain j ON b.id_jenis = j.id
    WHERE bk.id = ?
");
$stmt->execute([$id]);
$barang_keluar = $stmt->fetch();

if (!$barang_keluar) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Barang Keluar - <?= $barang_keluar['kode_barang'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .nota {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 5px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items th, .items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f5f5f5;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="nota">
        <div class="header">
            <h2 style="margin:0;">TENUN LIMO</h2>
            <p style="margin:5px 0;">Desa Troso RT 02/ RW 03, Jepara Jawa Tengah</p>
            <p style="margin:5px 0;">Telp: 082 333 859 741</p>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td width="150">No. Transaksi</td>
                    <td>: BK-<?= str_pad($barang_keluar['id'], 4, '0', STR_PAD_LEFT) ?></td>
                    <td width="150">Tanggal</td>
                    <td>: <?= date('d/m/Y', strtotime($barang_keluar['tanggal'])) ?></td>
                </tr>
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($barang_keluar['kode_barang']) ?></td>
                    <td><?= htmlspecialchars($barang_keluar['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($barang_keluar['nama_jenis'] ?? '-') ?></td>
                    <td><?= $barang_keluar['jumlah'] ?></td>
                    <td>Rp <?= number_format($barang_keluar['harga_jual'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($barang_keluar['total_harga'], 0, ',', '.') ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align:right;"><strong>Total</strong></td>
                    <td><strong>Rp <?= number_format($barang_keluar['total_harga'], 0, ',', '.') ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 30px;">
            <p><strong>Keterangan:</strong></p>
            <p><?= nl2br(htmlspecialchars($barang_keluar['keterangan'] ?? '-')) ?></p>
        </div>

        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <div style="text-align: center;">
                <p>Penerima</p>
                <br><br><br>
                <p>(_____________)</p>
            </div>
            <div style="text-align: center;">
                <p>Hormat Kami</p>
                <br><br><br>
                <p>(_____________)</p>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Tenun Limo</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Nota</button>
        <button onclick="window.location.href='index.php'" style="padding: 10px 20px; margin-left: 10px; cursor: pointer;">Kembali</button>
    </div>
</body>
</html> 
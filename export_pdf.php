<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'koneksi.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'stock';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventory</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; color: #FF2A6D; }
        .date { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #FF2A6D; color: white; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <h2>LAPORAN INVENTORY ASET</h2>
    <div class="date">Tanggal Cetak: <?php echo date('d/m/Y H:i:s'); ?></div>
    
    <?php if($type == 'stock'): ?>
    <h3>Laporan Stok Barang</h3>
    <table>
        <thead>
            <tr>
                <th>No</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Lokasi</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, it.status 
                      FROM inventory i 
                      JOIN items it ON i.item_id = it.id 
                      JOIN rooms r ON i.room_id = r.id 
                      LEFT JOIN categories c ON it.category_id = c.id";
            $result = mysqli_query($conn, $query);
            $no = 1;
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['item_name']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['quantity']}</td>
                        <td>{$row['room']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
    <?php elseif($type == 'transaction'): ?>
    <h3>Laporan Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th><th>Tanggal</th><th>No Transaksi</th><th>Jenis</th><th>Anggaran (Rp)</th><th>Realisasi (Rp)</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM transactions ORDER BY transaction_date DESC";
            $result = mysqli_query($conn, $query);
            $no = 1;
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['transaction_date']}</td>
                        <td>{$row['transaction_number']}</td>
                        <td>{$row['type']}</td>
                        <td>" . number_format($row['budget'], 0, ',', '.') . "</td>
                        <td>" . number_format($row['realization'], 0, ',', '.') . "</td>
                        <td>{$row['status']}</td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <div class="footer">
        Dicetak oleh: <?php echo $_SESSION['username']; ?> | Sistem Inventory Aset Yayasan
    </div>
    
    <script>
        window.print();
    </script>
</body>
</html>
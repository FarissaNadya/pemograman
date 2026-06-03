<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'koneksi.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

$title = '';
$icon = '';
$query = "";

if($type == 'low_stock') {
    $title = '⚠️ Barang dengan Stok Menipis';
    $icon = 'fa-exclamation-triangle';
    $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, it.status 
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE i.quantity > 0 AND i.quantity < 5
              ORDER BY i.quantity ASC";
} 
elseif($type == 'expired') {
    $title = '📅 Barang Kadaluarsa (Expired)';
    $icon = 'fa-calendar-times';
    $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, i.expired_date
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE i.expired_date IS NOT NULL AND i.expired_date <= CURDATE()
              ORDER BY i.expired_date ASC";
}
elseif($type == 'broken') {
    $title = '🔧 Barang Rusak';
    $icon = 'fa-wrench';
    $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, it.status
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE it.status = 'rusak' AND i.quantity > 0
              ORDER BY it.name";
}
else {
    header('Location: dashboard.php');
    exit();
}

$result = mysqli_query($conn, $query);
$total = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Sistem Inventory Aset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #1A1A1A; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #0D0D0D; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .header h1 { color: #FF2A6D; font-size: 24px; }
        .header h1 i { margin-right: 10px; }
        .header p { color: #888; margin-top: 5px; }
        .header-right { display: flex; gap: 10px; }
        .btn-back, .btn-dashboard { background: #333; color: #FFF; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: all 0.3s; }
        .btn-back:hover, .btn-dashboard:hover { background: #FF2A6D; transform: translateY(-2px); }
        
        .card { background: #0D0D0D; border-radius: 12px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #222; color: #CCC; }
        th { color: #FF2A6D; }
        
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-low { background: #FFA500; color: #000; }
        .status-expired { background: #FF2A6D; color: #FFF; }
        .status-broken { background: #FF2A6D; color: #FFF; }
        
        .alert-box { background: rgba(255, 42, 109, 0.1); border-left: 3px solid #FF2A6D; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-box i { color: #FF2A6D; font-size: 20px; }
        .alert-box .count { font-size: 24px; font-weight: bold; color: #FF2A6D; margin: 0 5px; }
        
        .empty-state { text-align: center; padding: 50px; color: #888; }
        .empty-state i { font-size: 50px; color: #00FF88; margin-bottom: 15px; }
        
        .btn-fix { background: transparent; border: 1px solid #FF2A6D; color: #FF2A6D; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .btn-fix:hover { background: #FF2A6D; color: #FFF; }
        
        @media (max-width: 768px) {
            .container { padding: 10px; }
            .header { flex-direction: column; text-align: center; }
            th, td { padding: 8px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1><i class="fas <?php echo $icon; ?>"></i> <?php echo $title; ?></h1>
                <p>Daftar barang yang memerlukan perhatian khusus</p>
            </div>
            <div class="header-right">
                <a href="dashboard.php" class="btn-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                <a href="javascript:history.back()" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </div>
        
        <div class="card">
            <?php if($total > 0): ?>
                <div class="alert-box">
                    <i class="fas fa-bell"></i>
                    <span>Ditemukan <span class="count"><?php echo $total; ?></span> data yang memerlukan perhatian:</span>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="item-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Lokasi</th>
                                <th>Status / Info</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
                                <td><strong><?php echo $row['quantity']; ?></strong> unit</td>
                                <td><?php echo htmlspecialchars($row['room'] ?? '-'); ?></td>
                                <td>
                                    <?php if($type == 'low_stock'): ?>
                                        <span class="status-badge status-low">⚠️ Stok Menipis (<?php echo $row['quantity']; ?> unit)</span>
                                    <?php elseif($type == 'expired'): ?>
                                        <span class="status-badge status-expired">📅 Expired: <?php echo date('d/m/Y', strtotime($row['expired_date'])); ?></span>
                                    <?php elseif($type == 'broken'): ?>
                                        <span class="status-badge status-broken">🔧 Dalam kondisi Rusak</span>
                                    <?php endif; ?>
                                 </td>
                                <td>
                                    <a href="inventory.php" class="btn-fix"><i class="fas fa-edit"></i> Kelola</a>
                                 </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>Tidak ada data untuk ditampilkan</p>
                    <p><small style="color:#888;">Semua inventaris dalam kondisi baik</small></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
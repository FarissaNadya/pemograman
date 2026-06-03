<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'koneksi.php';

$notifications = [];

// Cek barang dengan stok menipis (kurang dari 5)
$low_stock = mysqli_query($conn, "SELECT it.name as item_name, i.quantity, r.name as room_name
                                   FROM inventory i 
                                   JOIN items it ON i.item_id = it.id 
                                   JOIN rooms r ON i.room_id = r.id 
                                   WHERE i.quantity > 0 AND i.quantity < 5");
while($row = mysqli_fetch_assoc($low_stock)) {
    $notifications[] = [
        'type' => 'warning',
        'title' => 'Stok Menipis',
        'message' => "Stok {$row['item_name']} di {$row['room_name']} tersisa {$row['quantity']} unit",
        'time' => date('H:i'),
        'icon' => 'fa-exclamation-triangle',
        'color' => '#FFA500'
    ];
}

// Cek barang yang sudah expired
$expired = mysqli_query($conn, "SELECT it.name as item_name, i.expired_date, r.name as room_name
                                 FROM inventory i 
                                 JOIN items it ON i.item_id = it.id 
                                 JOIN rooms r ON i.room_id = r.id 
                                 WHERE i.expired_date IS NOT NULL AND i.expired_date <= CURDATE()");
while($row = mysqli_fetch_assoc($expired)) {
    $notifications[] = [
        'type' => 'danger',
        'title' => 'Barang Expired',
        'message' => "{$row['item_name']} di {$row['room_name']} sudah expired pada " . date('d/m/Y', strtotime($row['expired_date'])),
        'time' => date('H:i'),
        'icon' => 'fa-calendar-times',
        'color' => '#FF2A6D'
    ];
}

// Cek barang rusak
$broken = mysqli_query($conn, "SELECT it.name as item_name, i.quantity, r.name as room_name
                                FROM inventory i 
                                JOIN items it ON i.item_id = it.id 
                                JOIN rooms r ON i.room_id = r.id 
                                WHERE it.status = 'rusak' AND i.quantity > 0");
while($row = mysqli_fetch_assoc($broken)) {
    $notifications[] = [
        'type' => 'danger',
        'title' => 'Barang Rusak',
        'message' => "{$row['item_name']} di {$row['room_name']} berjumlah {$row['quantity']} unit dalam kondisi rusak",
        'time' => date('H:i'),
        'icon' => 'fa-exclamation-circle',
        'color' => '#FF2A6D'
    ];
}

// Jika tidak ada notifikasi
if(empty($notifications)) {
    $notifications[] = [
        'type' => 'success',
        'title' => 'Semua Baik',
        'message' => 'Tidak ada masalah dengan inventaris saat ini',
        'time' => date('H:i'),
        'icon' => 'fa-check-circle',
        'color' => '#00FF88'
    ];
}

echo json_encode($notifications);
?>
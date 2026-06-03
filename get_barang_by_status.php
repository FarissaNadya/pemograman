<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'koneksi.php';

$status = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Bangun query berdasarkan status
if($status == 'semua') {
    $query = "SELECT 
                i.id,
                it.name as item_name,
                c.name as category,
                i.quantity,
                r.name as room,
                it.status
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              LEFT JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE i.quantity > 0
              ORDER BY it.status, it.name";
} 
elseif($status == 'baik') {
    $query = "SELECT 
                i.id,
                it.name as item_name,
                c.name as category,
                i.quantity,
                r.name as room,
                it.status
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              LEFT JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE it.status = 'baik' AND i.quantity > 0
              ORDER BY it.name";
}
elseif($status == 'rusak') {
    $query = "SELECT 
                i.id,
                it.name as item_name,
                c.name as category,
                i.quantity,
                r.name as room,
                it.status
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              LEFT JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE it.status = 'rusak' AND i.quantity > 0
              ORDER BY it.name";
}
elseif($status == 'expired') {
    $query = "SELECT 
                i.id,
                it.name as item_name,
                c.name as category,
                i.quantity,
                r.name as room,
                'expired' as status
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              LEFT JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id
              WHERE i.expired_date IS NOT NULL AND i.expired_date < CURDATE() AND i.quantity > 0
              ORDER BY it.name";
}
else {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit();
}

$result = mysqli_query($conn, $query);
$items = [];

while($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

echo json_encode(['success' => true, 'data' => $items]);
?>
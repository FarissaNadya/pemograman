<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'koneksi.php';

$building_id = isset($_GET['building_id']) ? intval($_GET['building_id']) : 0;

if($building_id > 0) {
    $query = "SELECT r.id, r.name as room_name, COALESCE(SUM(i.quantity), 0) as total 
              FROM rooms r 
              LEFT JOIN inventory i ON r.id = i.room_id 
              WHERE r.building_id = '$building_id'
              GROUP BY r.id";
    $result = mysqli_query($conn, $query);
    
    $rooms = [];
    while($row = mysqli_fetch_assoc($result)) {
        $rooms[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $rooms]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid building ID']);
}
?>
<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'koneksi.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id > 0) {
    $result = mysqli_query($conn, "SELECT building_id FROM rooms WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data ?: ['building_id' => 0]);
} else {
    echo json_encode(['building_id' => 0]);
}
?>
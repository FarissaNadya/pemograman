<?php
session_start();
require_once 'koneksi.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak! Hanya Admin yang bisa menghapus data master.']);
    exit();
}

$table = $_POST['table'];
$id = $_POST['id'];

// Validasi table yang diperbolehkan
$allowed_tables = ['categories', 'items', 'buildings', 'rooms', 'transaction_types'];
if(!in_array($table, $allowed_tables)) {
    echo json_encode(['success' => false, 'error' => 'Invalid table']);
    exit();
}

$query = "DELETE FROM $table WHERE id = " . intval($id);
$success = mysqli_query($conn, $query);

echo json_encode(['success' => $success, 'error' => mysqli_error($conn)]);
?>
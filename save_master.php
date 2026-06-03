<?php
session_start();
require_once 'koneksi.php';

// Hanya admin yang boleh akses
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak! Hanya Admin yang bisa mengubah data master.']);
    exit();
}

$type = $_POST['type'];
$nama = mysqli_real_escape_string($conn, $_POST['nama']);

$success = false;

if($type == 'categories') {
    $success = mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$nama')");
}
else if($type == 'items') {
    $kategori_id = intval($_POST['kategori_id']);
    $success = mysqli_query($conn, "INSERT INTO items (name, category_id) VALUES ('$nama', '$kategori_id')");
}
else if($type == 'buildings') {
    $success = mysqli_query($conn, "INSERT INTO buildings (name) VALUES ('$nama')");
}
else if($type == 'rooms') {
    $gedung_id = intval($_POST['gedung_id']);
    $success = mysqli_query($conn, "INSERT INTO rooms (name, building_id) VALUES ('$nama', '$gedung_id')");
}
else if($type == 'transaction_types') {
    $success = mysqli_query($conn, "INSERT INTO transaction_types (name) VALUES ('$nama')");
}

echo json_encode(['success' => $success, 'error' => mysqli_error($conn)]);
?>
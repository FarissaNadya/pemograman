<?php
session_start();
require_once 'koneksi.php';

// Hanya admin yang boleh akses
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Akses ditolak! Hanya Admin yang bisa mengubah data master.']);
    exit();
}

$type = $_POST['type'];
$id = intval($_POST['id']);
$nama = mysqli_real_escape_string($conn, $_POST['nama']);

$success = false;

if($type == 'categories') {
    $success = mysqli_query($conn, "UPDATE categories SET name='$nama' WHERE id=$id");
}
else if($type == 'items') {
    $kategori_id = intval($_POST['kategori_id']);
    $success = mysqli_query($conn, "UPDATE items SET name='$nama', category_id='$kategori_id' WHERE id=$id");
}
else if($type == 'buildings') {
    $success = mysqli_query($conn, "UPDATE buildings SET name='$nama' WHERE id=$id");
}
else if($type == 'rooms') {
    $gedung_id = intval($_POST['gedung_id']);
    $success = mysqli_query($conn, "UPDATE rooms SET name='$nama', building_id='$gedung_id' WHERE id=$id");
}
else if($type == 'transaction_types') {
    $success = mysqli_query($conn, "UPDATE transaction_types SET name='$nama' WHERE id=$id");
}

echo json_encode(['success' => $success, 'error' => mysqli_error($conn)]);
?>
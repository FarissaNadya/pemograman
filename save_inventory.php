<?php
session_start();
require_once 'koneksi.php';

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header('Location: index.php');
    exit();
}

$item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
$quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
$price = mysqli_real_escape_string($conn, $_POST['price']);
$room_id = mysqli_real_escape_string($conn, $_POST['room_id']);
$expired_date = !empty($_POST['expired_date']) ? "'" . $_POST['expired_date'] . "'" : "NULL";

$barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
if(empty($barcode)) {
    $barcode = 'BRG-' . date('Ymd') . '-' . rand(100, 999);
}

$photo = '';
if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $target_dir = "uploads/";
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $photo = $target_dir . time() . '_' . basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
}

$query = "INSERT INTO inventory (item_id, quantity, price, room_id, barcode, photo, expired_date) 
          VALUES ('$item_id', '$quantity', '$price', '$room_id', '$barcode', '$photo', $expired_date)";

if(mysqli_query($conn, $query)) {
    header('Location: inventory.php?success=1');
} else {
    header('Location: inventory.php?error=1&msg=' . urlencode(mysqli_error($conn)));
}
?>
<?php
session_start();
require_once 'koneksi.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = intval($_POST['item_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $query = "UPDATE items SET status = '$status' WHERE id = $item_id";
    if(mysqli_query($conn, $query)) {
        header('Location: dashboard.php?status_success=1');
    } else {
        header('Location: dashboard.php?status_error=1');
    }
} else {
    header('Location: dashboard.php');
}
?>
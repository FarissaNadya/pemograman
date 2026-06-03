<?php
session_start();
require_once 'koneksi.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = md5($_POST['password']);

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: dashboard.php');
} else {
    header('Location: index.php?error=1');
}
?>
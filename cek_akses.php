<?php
// cek_akses.php - Cek akses berdasarkan role
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$is_admin = ($_SESSION['role'] == 'admin');
$is_staff = ($_SESSION['role'] == 'staff');

// Fungsi untuk cek akses halaman (hanya admin)
function require_admin() {
    global $is_admin;
    if(!$is_admin) {
        header('Location: dashboard.php?error=akses_ditolak');
        exit();
    }
}

// Fungsi untuk cek akses halaman (staff atau admin)
function require_staff() {
    global $is_staff, $is_admin;
    if(!$is_staff && !$is_admin) {
        header('Location: dashboard.php?error=akses_ditolak');
        exit();
    }
}
?>
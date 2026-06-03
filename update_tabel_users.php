<?php
require_once 'koneksi.php';

echo "<h2>Update Tabel Users untuk Multi Role</h2>";

// Tambah kolom role jika belum ada
$cek_role = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role'");
if(mysqli_num_rows($cek_role) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'staff'");
    echo "✅ Kolom 'role' ditambahkan<br>";
}

// Update user admin yang sudah ada
mysqli_query($conn, "UPDATE users SET role = 'admin' WHERE username = 'admin'");

// Tambah user staff inventory jika belum ada
$cek_staff = mysqli_query($conn, "SELECT id FROM users WHERE username = 'staff'");
if(mysqli_num_rows($cek_staff) == 0) {
    $pass = md5('staff123');
    mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('staff', '$pass', 'staff')");
    echo "✅ User 'staff' dibuat (password: staff123)<br>";
}

// Update user lain yang belum punya role
mysqli_query($conn, "UPDATE users SET role = 'staff' WHERE role IS NULL OR role = ''");

// Tampilkan semua user
echo "<h3>Data User Saat Ini:</h3>";
$users = mysqli_query($conn, "SELECT id, username, role FROM users");
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr style='background:#FF2A6D; color:white;'><th>ID</th><th>Username</th><th>Role</th></tr>";
while($user = mysqli_fetch_assoc($users)) {
    $role_badge = ($user['role'] == 'admin') ? '👑 Admin' : '📋 Staff';
    echo "<tr><td>{$user['id']}</td><td>{$user['username']}</td><td>{$role_badge}</td></tr>";
}
echo "</table>";

echo "<hr>";
echo "<a href='dashboard.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Dashboard</a>";
?>
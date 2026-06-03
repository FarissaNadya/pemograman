<?php
// tambah_staff.php - Menambahkan user STAFF ke database
require_once 'koneksi.php';

echo "<h2>👥 Menambahkan User Staff Inventory</h2>";

// 1. Cek apakah user staff sudah ada
$cek = mysqli_query($conn, "SELECT id FROM users WHERE username = 'staff'");

if(mysqli_num_rows($cek) > 0) {
    echo "<p style='color: orange;'>⚠️ User staff sudah ada di database!</p>";
    $user = mysqli_fetch_assoc($cek);
    echo "<p>ID: {$user['id']} | Username: staff</p>";
} else {
    // 2. Tambah user staff
    $password = md5('staff123');
    $query = "INSERT INTO users (username, password, role) VALUES ('staff', '$password', 'staff')";
    
    if(mysqli_query($conn, $query)) {
        $user_id = mysqli_insert_id($conn);
        echo "<p style='color: green;'>✅ User staff berhasil ditambahkan!</p>";
        echo "<p>ID: $user_id | Username: staff | Password: staff123 | Role: staff</p>";
        
        // 3. Tambah profile untuk staff
        $profile_query = "INSERT INTO admin_profile (user_id, nama_lengkap, email, jabatan) 
                          VALUES ('$user_id', 'Staff Inventory', 'staff@yayasan.com', 'Staff Inventory')";
        if(mysqli_query($conn, $profile_query)) {
            echo "<p style='color: green;'>✅ Profile staff juga ditambahkan!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Gagal: " . mysqli_error($conn) . "</p>";
    }
}

// 4. Tampilkan semua user yang ada
echo "<hr>";
echo "<h3>📋 Data User Saat Ini:</h3>";
$result = mysqli_query($conn, "SELECT id, username, role FROM users");
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr style='background: #FF2A6D; color: white;'><th>ID</th><th>Username</th><th>Role</th><th>Akses</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    $role_text = ($row['role'] == 'admin') ? '👑 Admin' : '📋 Staff';
    $akses = ($row['role'] == 'admin') ? 'Full Akses' : 'Terbatas (tidak bisa akses Data Master)';
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td><strong>{$row['username']}</strong></td>";
    echo "<td>{$role_text}</td>";
    echo "<td>{$akses}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h3>🔐 Login Info:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr style='background: #FF2A6D; color: white;'><th>Role</th><th>Username</th><th>Password</th></tr>";
echo "<tr><td>👑 Admin</td><td>admin</td><td>admin123</td></tr>";
echo "<tr><td>📋 Staff Inventory</td><td>staff</td><td>staff123</td></tr>";
echo "</table>";

echo "<hr>";
echo "<a href='index.php' style='background: #FF2A6D; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>🔐 Login ke Sistem</a>";
echo "&nbsp;&nbsp;";
echo "<a href='dashboard.php' style='background: #333; color: #FF2A6D; padding: 10px 20px; text-decoration: none; border-radius: 8px;'>📊 Dashboard</a>";

mysqli_close($conn);
?>
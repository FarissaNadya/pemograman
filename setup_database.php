<?php
// SETUP DATABASE LENGKAP - JALANKAN FILE INI SEKALI SAJA
// http://localhost/nama_folder/setup_database.php

$host = 'localhost';
$user = 'root';
$pass = '';

// Koneksi tanpa database
$conn = mysqli_connect($host, $user, $pass);

if(!$conn) {
    die("❌ Koneksi Gagal: " . mysqli_connect_error());
}

echo "<h2>🚀 Setup Database Sistem Inventory Aset</h2>";

// 1. Buat database
$db_name = 'db_inventaris_yayasan';
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $db_name";
if(mysqli_query($conn, $sql_create_db)) {
    echo "✅ Database '$db_name' berhasil dibuat atau sudah ada<br>";
} else {
    echo "❌ Gagal membuat database: " . mysqli_error($conn) . "<br>";
}

// Pilih database
mysqli_select_db($conn, $db_name);

// 2. Buat tabel users
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'staff'
)";
if(mysqli_query($conn, $sql_users)) {
    echo "✅ Tabel 'users' berhasil dibuat<br>";
} else {
    echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
}

// 3. Buat tabel admin_profile
$sql_profile = "CREATE TABLE IF NOT EXISTS admin_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    no_telepon VARCHAR(20),
    alamat TEXT,
    foto VARCHAR(255),
    jabatan VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
if(mysqli_query($conn, $sql_profile)) {
    echo "✅ Tabel 'admin_profile' berhasil dibuat<br>";
} else {
    echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
}

// 4. Buat tabel categories (jenis barang)
$sql_categories = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_categories);
echo "✅ Tabel 'categories' siap<br>";

// 5. Buat tabel buildings (gedung)
$sql_buildings = "CREATE TABLE IF NOT EXISTS buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql_buildings);
echo "✅ Tabel 'buildings' siap<br>";

// 6. Buat tabel rooms (ruangan)
$sql_rooms = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    building_id INT,
    FOREIGN KEY (building_id) REFERENCES buildings(id)
)";
mysqli_query($conn, $sql_rooms);
echo "✅ Tabel 'rooms' siap<br>";

// 7. Buat tabel items (barang)
$sql_items = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    status ENUM('baik', 'rusak', 'expired') DEFAULT 'baik'
)";
mysqli_query($conn, $sql_items);
echo "✅ Tabel 'items' siap<br>";

// 8. Buat tabel inventory (stok barang)
$sql_inventory = "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    room_id INT,
    quantity INT DEFAULT 0,
    price DECIMAL(15,0),
    barcode VARCHAR(100),
    photo VARCHAR(255),
    expired_date DATE
)";
mysqli_query($conn, $sql_inventory);
echo "✅ Tabel 'inventory' siap<br>";

// 9. Buat tabel transaction_types (jenis transaksi)
$sql_trans_types = "CREATE TABLE IF NOT EXISTS transaction_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql_trans_types);
echo "✅ Tabel 'transaction_types' siap<br>";

// 10. Buat tabel transactions (transaksi)
$sql_transactions = "CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE,
    transaction_number VARCHAR(50),
    type VARCHAR(50),
    budget DECIMAL(15,0) DEFAULT 0,
    realization DECIMAL(15,0) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Selesai',
    evidence_file VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql_transactions);
echo "✅ Tabel 'transactions' siap<br>";

// 11. Buat tabel inventory_room (riwayat mutasi)
$sql_inv_room = "CREATE TABLE IF NOT EXISTS inventory_room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_id INT NOT NULL,
    room_id INT NOT NULL,
    quantity INT DEFAULT 0,
    mutasi_date DATE,
    notes TEXT,
    transaction_id INT NULL
)";
mysqli_query($conn, $sql_inv_room);
echo "✅ Tabel 'inventory_room' siap<br>";

echo "<hr>";

// ================ INSERT DATA DEFAULT ================
echo "<h3>📝 Mengisi Data Default</h3>";

// Hapus data lama (opsional)
mysqli_query($conn, "DELETE FROM users");
mysqli_query($conn, "DELETE FROM transaction_types");
mysqli_query($conn, "DELETE FROM categories");
mysqli_query($conn, "DELETE FROM buildings");

// Insert User Admin
$pass_admin = md5('admin123');
mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('admin', '$pass_admin', 'admin')");
echo "✅ User Admin: admin / admin123 (role: admin)<br>";

// Insert User Staff
$pass_staff = md5('staff123');
mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('staff', '$pass_staff', 'staff')");
echo "✅ User Staff: staff / staff123 (role: staff)<br>";

// Insert Profile untuk Admin
$admin_id = mysqli_insert_id($conn) - 1; // ambil id admin
mysqli_query($conn, "INSERT INTO admin_profile (user_id, nama_lengkap, email, jabatan) VALUES 
    ((SELECT id FROM users WHERE username='admin'), 'Administrator Utama', 'admin@yayasan.com', 'Super Admin')");
echo "✅ Profile Admin ditambahkan<br>";

// Insert Profile untuk Staff
mysqli_query($conn, "INSERT INTO admin_profile (user_id, nama_lengkap, email, jabatan) VALUES 
    ((SELECT id FROM users WHERE username='staff'), 'Staff Inventory', 'staff@yayasan.com', 'Staff Inventory')");
echo "✅ Profile Staff ditambahkan<br>";

// Insert Jenis Transaksi
$trans_types = ['Pembelian', 'Hibah', 'Penghapusan', 'Pemusnahan', 'Mutasi'];
foreach($trans_types as $tt) {
    mysqli_query($conn, "INSERT INTO transaction_types (name) VALUES ('$tt')");
}
echo "✅ Jenis Transaksi: " . implode(', ', $trans_types) . "<br>";

// Insert Kategori Barang
$categories = ['Elektronik', 'Furniture', 'Laboratorium', 'Alat Tulis', 'Komputer'];
foreach($categories as $cat) {
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$cat')");
}
echo "✅ Kategori Barang: " . implode(', ', $categories) . "<br>";

// Insert Gedung
$buildings = ['Gedung Utama', 'Gedung Timur', 'Gedung Barat', 'Gudang Pusat', 'Lab Komputer'];
foreach($buildings as $bg) {
    mysqli_query($conn, "INSERT INTO buildings (name) VALUES ('$bg')");
}
echo "✅ Gedung: " . implode(', ', $buildings) . "<br>";

// Insert Ruangan
$rooms_data = [
    ['Ruang Kelas 1', 1], ['Ruang Kelas 2', 1], ['Lab Komputer 1', 5],
    ['Lab Komputer 2', 5], ['Ruang Guru', 1], ['Perpustakaan', 1],
    ['Gudang Elektronik', 4], ['Ruang Server', 1]
];
foreach($rooms_data as $rm) {
    mysqli_query($conn, "INSERT INTO rooms (name, building_id) VALUES ('{$rm[0]}', {$rm[1]})");
}
echo "✅ Ruangan: 8 ruangan ditambahkan<br>";

// Insert Barang
$items_data = [
    ['Laptop Dell', 1, 'baik'], ['Proyektor', 1, 'baik'],
    ['Meja Kayu', 2, 'baik'], ['Kursi Plastik', 2, 'baik'],
    ['Mikroskop', 3, 'baik'], ['Tabung Reaksi', 3, 'baik'],
    ['Printer', 5, 'rusak'], ['AC Split', 1, 'baik']
];
foreach($items_data as $it) {
    mysqli_query($conn, "INSERT INTO items (name, category_id, status) VALUES ('{$it[0]}', {$it[1]}, '{$it[2]}')");
}
echo "✅ Barang: 8 barang ditambahkan<br>";

// Insert Stok Inventory
$inventory_data = [
    [1, 1, 10, 8000000, 'BRG-001', '2025-12-31'],
    [2, 3, 5, 5000000, 'BRG-002', '2026-06-30'],
    [3, 1, 20, 750000, 'BRG-003', null],
    [4, 5, 50, 150000, 'BRG-004', null],
    [5, 3, 8, 2500000, 'BRG-005', '2025-10-15'],
    [7, 6, 3, 3500000, 'BRG-007', null],
    [8, 8, 2, 15000000, 'BRG-008', null]
];
foreach($inventory_data as $inv) {
    $expired = $inv[5] ? "'{$inv[5]}'" : "NULL";
    mysqli_query($conn, "INSERT INTO inventory (item_id, room_id, quantity, price, barcode, expired_date) 
                         VALUES ({$inv[0]}, {$inv[1]}, {$inv[2]}, {$inv[3]}, '{$inv[4]}', $expired)");
}
echo "✅ Stok Inventory: 7 record ditambahkan<br>";

// Insert Transaksi contoh
mysqli_query($conn, "INSERT INTO transactions (transaction_date, transaction_number, type, budget, realization, status) VALUES
    ('2026-01-15', 'TRX/001/2026', 'Pembelian', 50000000, 48000000, 'Selesai'),
    ('2026-02-10', 'TRX/002/2026', 'Pembelian', 75000000, 72000000, 'Selesai'),
    ('2026-03-05', 'TRX/003/2026', 'Mutasi', 0, 0, 'Selesai')");
echo "✅ Transaksi contoh ditambahkan<br>";

echo "<hr>";
echo "<h3 style='color:#00FF88;'>✅ SETUP SELESAI! ✅</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr style='background:#FF2A6D; color:white;'><th>Role</th><th>Username</th><th>Password</th></tr>";
echo "<tr><td>👑 Admin</td><td>admin</td><td>admin123</td></tr>";
echo "<tr><td>📋 Staff Inventory</td><td>staff</td><td>staff123</td></tr>";
echo "</table>";
echo "<br>";
echo "<a href='index.php' style='background:#FF2A6D; color:white; padding:12px 25px; text-decoration:none; border-radius:8px; display:inline-block;'>🔐 Login ke Sistem</a>";
echo "&nbsp;&nbsp;";
echo "<a href='dashboard.php' style='background:#333; color:#FF2A6D; padding:12px 25px; text-decoration:none; border-radius:8px; display:inline-block;'>📊 Langsung ke Dashboard</a>";

mysqli_close($conn);
?>
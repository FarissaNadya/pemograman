<?php
require_once 'koneksi.php';

echo "<h2>Membuat Tabel yang Kurang</h2>";

// 1. Buat tabel transaction_types
$sql1 = "CREATE TABLE IF NOT EXISTS transaction_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(mysqli_query($conn, $sql1)) {
    echo "✅ Tabel 'transaction_types' berhasil dibuat<br>";
    
    // Insert data jenis transaksi
    $jenis = ['Pembelian', 'Hibah', 'Penghapusan', 'Pemusnahan', 'Mutasi'];
    foreach($jenis as $j) {
        $cek = mysqli_query($conn, "SELECT id FROM transaction_types WHERE name = '$j'");
        if(mysqli_num_rows($cek) == 0) {
            mysqli_query($conn, "INSERT INTO transaction_types (name) VALUES ('$j')");
            echo "&nbsp;&nbsp;✅ Ditambahkan: $j<br>";
        }
    }
} else {
    echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
}

// 2. Cek tabel categories
$sql2 = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql2);
echo "✅ Tabel 'categories' siap<br>";

// 3. Cek tabel buildings
$sql3 = "CREATE TABLE IF NOT EXISTS buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
)";
mysqli_query($conn, $sql3);
echo "✅ Tabel 'buildings' siap<br>";

// 4. Cek tabel rooms
$sql4 = "CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    building_id INT,
    FOREIGN KEY (building_id) REFERENCES buildings(id)
)";
mysqli_query($conn, $sql4);
echo "✅ Tabel 'rooms' siap<br>";

// 5. Cek tabel items
$sql5 = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    status ENUM('baik', 'rusak', 'expired') DEFAULT 'baik'
)";
mysqli_query($conn, $sql5);
echo "✅ Tabel 'items' siap<br>";

// 6. Cek tabel inventory
$sql6 = "CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    room_id INT,
    quantity INT DEFAULT 0,
    price DECIMAL(15,0),
    barcode VARCHAR(100),
    photo VARCHAR(255),
    expired_date DATE
)";
mysqli_query($conn, $sql6);
echo "✅ Tabel 'inventory' siap<br>";

// 7. Cek tabel users
$sql7 = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'admin'
)";
mysqli_query($conn, $sql7);

// Cek apakah user admin ada
$cek_admin = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin'");
if(mysqli_num_rows($cek_admin) == 0) {
    mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('admin', MD5('admin123'))");
    echo "✅ User admin dibuat (password: admin123)<br>";
}
echo "✅ Tabel 'users' siap<br>";

echo "<hr>";
echo "<a href='transaksi.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Transaksi</a>";
?>
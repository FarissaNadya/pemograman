<?php
require_once 'koneksi.php';

echo "<h2>Migrasi Database - Menambah Kolom yang Kurang</h2>";
echo "<pre>";

// Cek dan tambah kolom photo jika belum ada
$check_photo = mysqli_query($conn, "SHOW COLUMNS FROM inventory LIKE 'photo'");
if(!$check_photo) {
    // Jika query error, coba cara lain
    $check_photo = mysqli_query($conn, "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = 'inventory' AND COLUMN_NAME = 'photo'");
}
if(mysqli_num_rows($check_photo) == 0) {
    $sql_photo = "ALTER TABLE inventory ADD COLUMN photo VARCHAR(255) NULL";
    if(mysqli_query($conn, $sql_photo)) {
        echo "✅ Kolom 'photo' berhasil ditambahkan ke tabel inventory\n";
    } else {
        echo "❌ Gagal tambah photo: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Kolom 'photo' sudah ada\n";
}

// Cek dan tambah kolom expired_date di tabel inventory
$check_expired = mysqli_query($conn, "SHOW COLUMNS FROM inventory LIKE 'expired_date'");
if(mysqli_num_rows($check_expired) == 0) {
    $sql_expired = "ALTER TABLE inventory ADD COLUMN expired_date DATE NULL AFTER photo";
    if(mysqli_query($conn, $sql_expired)) {
        echo "✅ Kolom 'expired_date' berhasil ditambahkan ke tabel inventory\n";
    } else {
        echo "❌ Gagal tambah expired_date: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Kolom 'expired_date' sudah ada\n";
}

// Cek dan tambah kolom status di tabel items jika belum ada
$check_status = mysqli_query($conn, "SHOW COLUMNS FROM items LIKE 'status'");
if(mysqli_num_rows($check_status) == 0) {
    $sql_status = "ALTER TABLE items ADD COLUMN status ENUM('baik', 'rusak', 'expired') DEFAULT 'baik'";
    if(mysqli_query($conn, $sql_status)) {
        echo "✅ Kolom 'status' berhasil ditambahkan ke tabel items\n";
    } else {
        echo "❌ Gagal tambah status: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Kolom 'status' sudah ada\n";
}

// Cek dan tambah kolom price di tabel inventory jika belum ada
$check_price = mysqli_query($conn, "SHOW COLUMNS FROM inventory LIKE 'price'");
if(mysqli_num_rows($check_price) == 0) {
    $sql_price = "ALTER TABLE inventory ADD COLUMN price DECIMAL(15,0) NULL";
    if(mysqli_query($conn, $sql_price)) {
        echo "✅ Kolom 'price' berhasil ditambahkan ke tabel inventory\n";
    } else {
        echo "❌ Gagal tambah price: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Kolom 'price' sudah ada\n";
}

// Cek dan tambah kolom barcode di tabel inventory jika belum ada
$check_barcode = mysqli_query($conn, "SHOW COLUMNS FROM inventory LIKE 'barcode'");
if(mysqli_num_rows($check_barcode) == 0) {
    $sql_barcode = "ALTER TABLE inventory ADD COLUMN barcode VARCHAR(100) NULL";
    if(mysqli_query($conn, $sql_barcode)) {
        echo "✅ Kolom 'barcode' berhasil ditambahkan ke tabel inventory\n";
    } else {
        echo "❌ Gagal tambah barcode: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Kolom 'barcode' sudah ada\n";
}

// Buat tabel inventory_room jika belum ada
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'inventory_room'");
if(mysqli_num_rows($check_table) == 0) {
    $sql_table = "CREATE TABLE inventory_room (
        id INT AUTO_INCREMENT PRIMARY KEY,
        inventory_id INT NOT NULL,
        room_id INT NOT NULL,
        quantity INT DEFAULT 0,
        mutasi_date DATE,
        notes TEXT,
        transaction_id INT NULL
    )";
    if(mysqli_query($conn, $sql_table)) {
        echo "✅ Tabel 'inventory_room' berhasil dibuat\n";
    } else {
        echo "❌ Gagal buat tabel inventory_room: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Tabel 'inventory_room' sudah ada\n";
}

// Buat tabel transactions jika belum ada
$check_trans = mysqli_query($conn, "SHOW TABLES LIKE 'transactions'");
if(mysqli_num_rows($check_trans) == 0) {
    $sql_trans = "CREATE TABLE transactions (
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
    if(mysqli_query($conn, $sql_trans)) {
        echo "✅ Tabel 'transactions' berhasil dibuat\n";
    } else {
        echo "❌ Gagal buat tabel transactions: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Tabel 'transactions' sudah ada\n";
}

// Buat tabel users jika belum ada (untuk login)
$check_users = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if(mysqli_num_rows($check_users) == 0) {
    $sql_users = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE,
        password VARCHAR(255),
        role VARCHAR(20) DEFAULT 'admin'
    )";
    if(mysqli_query($conn, $sql_users)) {
        // Insert default admin
        $default_pass = md5('admin123');
        mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('admin', '$default_pass')");
        echo "✅ Tabel 'users' berhasil dibuat dengan user: admin / admin123\n";
    } else {
        echo "❌ Gagal buat tabel users: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "ℹ️ Tabel 'users' sudah ada\n";
}

echo "</pre>";
echo "<hr>";
echo "<a href='dashboard.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Dashboard</a>";
?>
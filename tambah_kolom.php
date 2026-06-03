<?php
require_once 'koneksi.php';

echo "<h2>Menambah Kolom yang Kurang (Aman - Tidak Error)</h2>";

// Fungsi untuk cek dan tambah kolom
function tambahKolom($conn, $table, $column, $type) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    if(mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE $table ADD COLUMN $column $type";
        if(mysqli_query($conn, $sql)) {
            echo "✅ Kolom '$column' berhasil ditambahkan ke tabel $table<br>";
            return true;
        } else {
            echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
            return false;
        }
    } else {
        echo "ℹ️ Kolom '$column' sudah ada di tabel $table (skip)<br>";
        return true;
    }
}

// Tambah kolom yang diperlukan
tambahKolom($conn, 'transactions', 'evidence_file', 'VARCHAR(255) NULL');
tambahKolom($conn, 'inventory', 'photo', 'VARCHAR(255) NULL');
tambahKolom($conn, 'inventory', 'barcode', 'VARCHAR(100) NULL');
tambahKolom($conn, 'inventory', 'expired_date', 'DATE NULL');
tambahKolom($conn, 'inventory', 'price', 'DECIMAL(15,0) NULL');

echo "<hr>";
echo "<a href='transaksi.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Transaksi</a>";
?>
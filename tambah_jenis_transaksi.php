<?php
require_once 'koneksi.php';

// Hapus data yang salah dulu
mysqli_query($conn, "DELETE FROM transaction_types WHERE name = 'Komputer'");

// Data jenis transaksi yang benar
$data = [
    'Pembelian',
    'Hibah', 
    'Penghapusan',
    'Pemusnahan',
    'Mutasi'
];

echo "<h2>Menambah Jenis Transaksi</h2>";
foreach($data as $jenis) {
    $cek = mysqli_query($conn, "SELECT * FROM transaction_types WHERE name = '$jenis'");
    if(mysqli_num_rows($cek) == 0) {
        $sql = "INSERT INTO transaction_types (name) VALUES ('$jenis')";
        if(mysqli_query($conn, $sql)) {
            echo "✅ Berhasil menambah: $jenis<br>";
        } else {
            echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Sudah ada: $jenis<br>";
    }
}

// Tampilkan semua data yang sudah ada
echo "<hr>";
echo "<h3>Data Jenis Transaksi Saat Ini:</h3>";
$result = mysqli_query($conn, "SELECT * FROM transaction_types");
echo "<ul>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<li>{$row['name']}</li>";
}
echo "</ul>";

echo "<hr>";
echo "<a href='transaksi.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Transaksi</a>";
?>
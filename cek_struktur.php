<?php
require_once 'koneksi.php';

echo "<h2>Struktur Tabel Transactions</h2>";
$result = mysqli_query($conn, "SHOW COLUMNS FROM transactions");
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Kolom</th><th>Tipe</th><th>Null</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td></tr>";
}
echo "</table>";

echo "<h2>Struktur Tabel Inventory</h2>";
$result2 = mysqli_query($conn, "SHOW COLUMNS FROM inventory");
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Kolom</th><th>Tipe</th><th>Null</th></tr>";
while($row = mysqli_fetch_assoc($result2)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td></tr>";
}
echo "</table>";
?>
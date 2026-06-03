<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'koneksi.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'stock';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_$type.xls");
header("Pragma: no-cache");
header("Expires: 0");

if($type == 'stock') {
    $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, it.status 
              FROM inventory i 
              JOIN items it ON i.item_id = it.id 
              JOIN rooms r ON i.room_id = r.id 
              LEFT JOIN categories c ON it.category_id = c.id";
    $result = mysqli_query($conn, $query);
    
    echo "<table border='1'>";
    echo "<tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Lokasi</th><th>Status</th></tr>";
    $no = 1;
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['item_name']}</td>
                <td>{$row['category']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['room']}</td>
                <td>{$row['status']}</td>
              </tr>";
        $no++;
    }
    echo "</table>";
}
elseif($type == 'transaction') {
    $query = "SELECT * FROM transactions ORDER BY transaction_date DESC";
    $result = mysqli_query($conn, $query);
    
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No Transaksi</th>
            <th>Jenis</th>
            <th>Anggaran (Rp)</th>
            <th>Realisasi (Rp)</th>
            <th>Status</th>
          </tr>";
    $no = 1;
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['transaction_date']}</td>
                <td>{$row['transaction_number']}</td>
                <td>{$row['type']}</td>
                <td>" . number_format($row['budget'], 0, ',', '.') . "</td>
                <td>" . number_format($row['realization'], 0, ',', '.') . "</td>
                <td>{$row['status']}</td>
              </tr>";
        $no++;
    }
    echo "</table>";
}
?>
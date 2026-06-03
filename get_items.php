<?php
require_once 'koneksi.php';
header('Content-Type: application/json');
$result = mysqli_query($conn, "SELECT items.*, categories.name as category_name FROM items LEFT JOIN categories ON items.category_id=categories.id");
$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
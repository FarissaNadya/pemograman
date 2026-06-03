<?php
require_once 'koneksi.php';
header('Content-Type: application/json');
$result = mysqli_query($conn, "SELECT id, name FROM categories");
$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
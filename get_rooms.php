<?php
require_once 'koneksi.php';
header('Content-Type: application/json');
$result = mysqli_query($conn, "SELECT rooms.*, buildings.name as building_name FROM rooms LEFT JOIN buildings ON rooms.building_id=buildings.id");
$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
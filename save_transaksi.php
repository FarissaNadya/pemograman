<?php
session_start();
require_once 'koneksi.php';

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header('Location: index.php');
    exit();
}

$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
$nomor_transaksi = mysqli_real_escape_string($conn, $_POST['nomor_transaksi']);
$jenis_transaksi = mysqli_real_escape_string($conn, $_POST['jenis_transaksi']);
$anggaran = mysqli_real_escape_string($conn, str_replace('.', '', $_POST['anggaran']));
$realisasi = mysqli_real_escape_string($conn, str_replace('.', '', $_POST['realisasi']));
$status = mysqli_real_escape_string($conn, $_POST['status_transaksi']);

$bukti_file = '';
if(isset($_FILES['bukti_file']) && $_FILES['bukti_file']['error'] == 0) {
    $target_dir = "uploads/bukti/";
    if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $bukti_file = $target_dir . time() . '_' . basename($_FILES['bukti_file']['name']);
    move_uploaded_file($_FILES['bukti_file']['tmp_name'], $bukti_file);
}

$query = "INSERT INTO transactions (transaction_date, transaction_number, type, budget, realization, status, evidence_file) 
          VALUES ('$tanggal', '$nomor_transaksi', '$jenis_transaksi', '$anggaran', '$realisasi', '$status', '$bukti_file')";

if(mysqli_query($conn, $query)) {
    $item_names = $_POST['item_name'] ?? [];
    $item_qtys = $_POST['item_qty'] ?? [];
    $item_prices = $_POST['item_price'] ?? [];
    
    for($i = 0; $i < count($item_names); $i++) {
        if(empty($item_names[$i]) || empty($item_qtys[$i])) continue;
        
        $nama_barang = mysqli_real_escape_string($conn, $item_names[$i]);
        $qty = intval($item_qtys[$i]);
        $harga = intval(str_replace('.', '', $item_prices[$i]));
        
        $cek_item = mysqli_query($conn, "SELECT id FROM items WHERE name = '$nama_barang'");
        if(mysqli_num_rows($cek_item) > 0) {
            $item_row = mysqli_fetch_assoc($cek_item);
            $item_id = $item_row['id'];
        } else {
            mysqli_query($conn, "INSERT INTO items (name, status) VALUES ('$nama_barang', 'baik')");
            $item_id = mysqli_insert_id($conn);
        }
        
        if($jenis_transaksi == 'Pembelian' || $jenis_transaksi == 'Hibah') {
            $cek_inventory = mysqli_query($conn, "SELECT id, quantity FROM inventory WHERE item_id = '$item_id' ORDER BY id DESC LIMIT 1");
            if(mysqli_num_rows($cek_inventory) > 0) {
                $inv_row = mysqli_fetch_assoc($cek_inventory);
                $new_qty = $inv_row['quantity'] + $qty;
                mysqli_query($conn, "UPDATE inventory SET quantity = '$new_qty' WHERE id = '{$inv_row['id']}'");
            } else {
                mysqli_query($conn, "INSERT INTO inventory (item_id, quantity, price) VALUES ('$item_id', '$qty', '$harga')");
            }
        } 
        else if($jenis_transaksi == 'Penghapusan' || $jenis_transaksi == 'Pemusnahan') {
            $cek_inventory = mysqli_query($conn, "SELECT id, quantity FROM inventory WHERE item_id = '$item_id' ORDER BY id DESC LIMIT 1");
            if(mysqli_num_rows($cek_inventory) > 0) {
                $inv_row = mysqli_fetch_assoc($cek_inventory);
                $new_qty = max(0, $inv_row['quantity'] - $qty);
                mysqli_query($conn, "UPDATE inventory SET quantity = '$new_qty' WHERE id = '{$inv_row['id']}'");
            }
        }
    }
    
    header('Location: transaksi.php?success=1');
} else {
    header('Location: transaksi.php?error=1&msg=' . urlencode(mysqli_error($conn)));
}
?>
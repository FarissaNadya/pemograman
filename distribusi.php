<?php
require_once 'cek_akses.php';
require_staff(); // Staff dan Admin boleh akses
require_once 'koneksi.php';

$is_admin = ($_SESSION['role'] == 'admin');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inventory_id = intval($_POST['inventory_id']);
    $ke_room_id = intval($_POST['ke_room_id']);
    $jumlah = intval($_POST['jumlah']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    
    $cek_stok = mysqli_query($conn, "SELECT quantity, item_id, room_id FROM inventory WHERE id = $inventory_id");
    $stok = mysqli_fetch_assoc($cek_stok);
    
    if($stok && $stok['quantity'] >= $jumlah) {
        mysqli_query($conn, "UPDATE inventory SET quantity = quantity - $jumlah WHERE id = $inventory_id");
        
        $cek_tujuan = mysqli_query($conn, "SELECT id FROM inventory WHERE item_id = '{$stok['item_id']}' AND room_id = $ke_room_id");
        if(mysqli_num_rows($cek_tujuan) > 0) {
            $tujuan = mysqli_fetch_assoc($cek_tujuan);
            mysqli_query($conn, "UPDATE inventory SET quantity = quantity + $jumlah WHERE id = {$tujuan['id']}");
        } else {
            mysqli_query($conn, "INSERT INTO inventory (item_id, quantity, room_id) VALUES ('{$stok['item_id']}', '$jumlah', '$ke_room_id')");
        }
        
        mysqli_query($conn, "INSERT INTO inventory_room (inventory_id, room_id, quantity, mutasi_date, notes) 
                            VALUES ('$inventory_id', '$ke_room_id', '$jumlah', CURDATE(), '$catatan')");
        $success = true;
    } else {
        $error = "Stok tidak mencukupi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribusi Barang - Sistem Inventory Aset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #1A1A1A; display: flex; }
        
        .sidebar {
            width: 260px;
            background: #0D0D0D;
            height: 100vh;
            position: fixed;
            border-right: 1px solid #FF2A6D;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header { padding: 25px 20px; text-align: center; flex-shrink: 0; }
        .sidebar-header img { width: 60px; height: 60px; border-radius: 50%; margin-bottom: 10px; }
        .sidebar-header h3 { color: #FFFFFF; font-size: 16px; margin-bottom: 5px; }
        .sidebar-header p { color: #888; font-size: 11px; }
        
        .nav-menu { list-style: none; padding: 20px 0; flex: 1; overflow-y: auto; }
        .nav-link { display: flex; align-items: center; padding: 12px 20px; color: #CCC; text-decoration: none; gap: 12px; font-size: 14px; }
        .nav-link i { width: 24px; font-size: 16px; }
        .nav-link:hover, .nav-link.active { background: #FF2A6D; color: #FFF; }
        
        .logout-wrapper { padding: 15px 20px; border-top: 1px solid #333; margin-top: auto; flex-shrink: 0; }
        .logout-link { display: flex; align-items: center; gap: 12px; padding: 10px 15px; color: #FF2A6D; text-decoration: none; border-radius: 8px; transition: all 0.3s; font-size: 14px; }
        .logout-link:hover { background: #FF2A6D; color: #FFF; }
        .logout-link i { width: 24px; font-size: 16px; }
        
        .main-content { margin-left: 260px; flex: 1; padding: 20px; }
        .top-bar { background: #0D0D0D; padding: 15px 25px; border-radius: 12px; display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; }
        .page-title h1 { color: #FFF; font-size: 24px; }
        .page-title p { color: #888; font-size: 13px; }
        .user-info { display: flex; align-items: center; gap: 20px; }
        .user-info i { color: #FF2A6D; font-size: 20px; }
        .user-name { color: #FF2A6D; font-weight: bold; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.3s; }
        .user-name:hover { color: #FFFFFF; text-decoration: underline; }
        .role-badge { background: #FF2A6D; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        
        .card { background: #0D0D0D; border-radius: 12px; padding: 25px; margin-bottom: 20px; }
        .card h3 { color: #FFF; border-left: 3px solid #FF2A6D; padding-left: 12px; margin-bottom: 20px; font-size: 18px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #FFF; margin-bottom: 8px; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; background: #2A2A2A; border: 1px solid #333; border-radius: 8px; color: #FFF; font-size: 14px; }
        .btn-pink { background: #FF2A6D; color: #FFF; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; width: 100%; font-size: 14px; }
        .success-msg { background: #00FF88; color: #000; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .error-msg { background: #FF2A6D; color: #FFF; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .history-list { list-style: none; }
        .history-list li { padding: 12px 0; border-bottom: 1px solid #222; color: #CCC; font-size: 14px; }
        .history-list li span { color: #FF2A6D; }
        .history-list li small { color: #888; font-size: 12px; }
        
        .notification-bell { position: relative; cursor: pointer; }
        .notification-bell .badge { position: absolute; top: -8px; right: -8px; background: #FF2A6D; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold; }
        .notification-dropdown { position: absolute; top: 50px; right: 20px; width: 350px; max-height: 400px; background: #0D0D0D; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.5); border: 1px solid #FF2A6D; z-index: 1000; display: none; overflow-y: auto; }
        .notification-dropdown.show { display: block; }
        .notification-header { padding: 15px; border-bottom: 1px solid #333; color: #FFF; font-weight: bold; }
        .notification-item { padding: 12px 15px; border-bottom: 1px solid #222; cursor: pointer; transition: background 0.3s; }
        .notification-item:hover { background: #1A1A1A; }
        .notification-item .notif-title { color: #FF2A6D; font-size: 13px; font-weight: bold; }
        .notification-item .notif-message { color: #CCC; font-size: 12px; margin-top: 5px; }
        .notification-item .notif-time { color: #888; font-size: 10px; margin-top: 5px; }
        
        .nav-menu::-webkit-scrollbar { width: 3px; }
        .nav-menu::-webkit-scrollbar-track { background: #1A1A1A; }
        .nav-menu::-webkit-scrollbar-thumb { background: #FF2A6D; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="assest/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/60?text=Logo'">
            <h3>Apex Academy Indonesia</h3>
            <p>Inventory Aset</p>
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dasbor</a></li>
            <?php if($is_admin): ?>
            <li><a href="data-master.php" class="nav-link"><i class="fas fa-database"></i> Data Master</a></li>
            <?php endif; ?>
            <li><a href="inventory.php" class="nav-link"><i class="fas fa-boxes"></i> Inventaris</a></li>
            <li><a href="transaksi.php" class="nav-link"><i class="fas fa-money-bill"></i> Transaksi</a></li>
            <li><a href="distribusi.php" class="nav-link active"><i class="fas fa-truck"></i> Distribusi</a></li>
            <li><a href="laporan.php" class="nav-link"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="profil.php" class="nav-link"><i class="fas fa-user-circle"></i> Profil</a></li>
        </ul>
        <div class="logout-wrapper">
            <a href="logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="page-title"><h1>Distribusi Barang</h1><p>Pindah barang antar ruangan</p></div>
            <div class="user-info">
                <span class="role-badge"><?php echo ($is_admin ? '👑 ADMIN' : '📋 STAFF INVENTORY'); ?></span>
                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notifBadge" style="display: none;">0</span>
                </div>
                <i class="fas fa-user-circle"></i>
                <a href="profil.php" class="user-name"><?php echo $_SESSION['username'] ?? 'Admin'; ?></a>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header"><i class="fas fa-bell"></i> Notifikasi</div>
                <div id="notificationList"><div class="notification-item">Memuat...</div></div>
            </div>
        </div>

        <?php if(isset($success)): ?>
            <div class="success-msg">✅ Mutasi berhasil diproses!</div>
        <?php endif; ?>
        <?php if(isset($error)): ?>
            <div class="error-msg">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3><i class="fas fa-exchange-alt"></i> Form Mutasi Barang</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Pilih Barang</label>
                    <select name="inventory_id" required>
                        <option value="">Pilih Barang</option>
                        <?php
                        $query = "SELECT i.id, it.name as item_name, i.quantity, r.name as room_name 
                                  FROM inventory i 
                                  JOIN items it ON i.item_id = it.id 
                                  JOIN rooms r ON i.room_id = r.id 
                                  WHERE i.quantity > 0";
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id']}'>{$row['item_name']} (Stok: {$row['quantity']}) - {$row['room_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ke Lokasi Tujuan</label>
                    <select name="ke_room_id" required>
                        <option value="">Pilih Lokasi Tujuan</option>
                        <?php
                        $rooms = mysqli_query($conn, "SELECT r.id, r.name, b.name as building_name FROM rooms r JOIN buildings b ON r.building_id = b.id");
                        while($room = mysqli_fetch_assoc($rooms)) {
                            echo "<option value='{$room['id']}'>{$room['building_name']} - {$room['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah Dipindah</label>
                    <input type="number" name="jumlah" required placeholder="1">
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Contoh: Mutasi inventaris"></textarea>
                </div>
                <button type="submit" class="btn-pink"><i class="fas fa-truck-fast"></i> Proses Mutasi</button>
            </form>
        </div>

        <div class="card">
            <h3><i class="fas fa-history"></i> Riwayat Mutasi</h3>
            <ul class="history-list">
                <?php
                $history = mysqli_query($conn, "SELECT ir.*, it.name as item_name, r.name as room_name 
                                                FROM inventory_room ir
                                                JOIN inventory i ON ir.inventory_id = i.id
                                                JOIN items it ON i.item_id = it.id
                                                JOIN rooms r ON ir.room_id = r.id
                                                ORDER BY ir.mutasi_date DESC LIMIT 10");
                if(mysqli_num_rows($history) > 0) {
                    while($h = mysqli_fetch_assoc($history)) {
                        echo "<li><span>{$h['item_name']}</span> ({$h['quantity']} unit) → {$h['room_name']}<br><small><i class='fas fa-calendar'></i> {$h['mutasi_date']} - {$h['notes']}</small></li>";
                    }
                } else {
                    echo "<li>Belum ada riwayat mutasi</li>";
                }
                ?>
            </ul>
        </div>
    </div>

    <script>
        function loadNotifikasi() {
            fetch('get_notifications.php').then(r=>r.json()).then(data=>{
                const notifList = document.getElementById('notificationList');
                const notifBadge = document.getElementById('notifBadge');
                if(!notifList) return;
                notifList.innerHTML = '';
                let notifCount = data.filter(n => n.type !== 'success').length;
                if(notifCount > 0) { notifBadge.style.display = 'inline-block'; notifBadge.textContent = notifCount; }
                else { notifBadge.style.display = 'none'; }
                data.forEach(notif => {
                    const notifItem = document.createElement('div');
                    notifItem.className = 'notification-item';
                    notifItem.innerHTML = `<div class="notif-title"><i class="fas ${notif.icon}" style="color: ${notif.color}; margin-right: 8px;"></i>${notif.title}</div><div class="notif-message">${notif.message}</div><div class="notif-time"><i class="fas fa-clock"></i> ${notif.time}</div>`;
                    notifItem.style.cursor = 'pointer';
                    if(notif.title === 'Stok Menipis') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=low_stock';
                    else if(notif.title === 'Barang Rusak') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=broken';
                    else if(notif.title === 'Barang Expired') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=expired';
                    notifList.appendChild(notifItem);
                });
            }).catch(error => console.error('Error:', error));
        }
        
        document.getElementById('notificationBell').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('notificationDropdown').classList.toggle('show');
        });
        document.addEventListener('click', function() { document.getElementById('notificationDropdown').classList.remove('show'); });
        document.getElementById('notificationDropdown').addEventListener('click', function(e) { e.stopPropagation(); });
        
        loadNotifikasi();
        setInterval(loadNotifikasi, 30000);
    </script>
</body>
</html>
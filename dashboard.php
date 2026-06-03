<?php
require_once 'cek_akses.php';
require_once 'koneksi.php';

$is_admin = ($_SESSION['role'] == 'admin');
$is_staff = ($_SESSION['role'] == 'staff');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard - Sistem Inventory Aset</title>
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
            z-index: 100;
            transition: transform 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .menu-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background: #FF2A6D;
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 20px;
            }
            .top-bar {
                margin-left: 45px;
            }
        }
        
        @media (min-width: 769px) {
            .menu-toggle {
                display: none;
            }
        }
        
        .sidebar-header { 
            padding: 25px 20px; 
            text-align: center; 
            flex-shrink: 0;
        }
        .sidebar-header img { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            object-fit: cover;
            margin-bottom: 10px;
        }
        .sidebar-header h3 { color: #FFFFFF; font-size: 16px; margin-bottom: 5px; }
        .sidebar-header p { color: #888; font-size: 11px; }
        
        .nav-menu { 
            list-style: none; 
            padding: 20px 0; 
            flex: 1;
            overflow-y: auto;
        }
        .nav-link { 
            display: flex; 
            align-items: center; 
            padding: 12px 20px; 
            color: #CCC; 
            text-decoration: none; 
            gap: 12px; 
            font-size: 14px; 
        }
        .nav-link i { width: 24px; font-size: 16px; }
        .nav-link:hover, .nav-link.active { background: #FF2A6D; color: #FFF; }
        
        .logout-wrapper {
            padding: 15px 20px;
            border-top: 1px solid #333;
            margin-top: auto;
            flex-shrink: 0;
        }
        .logout-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            color: #FF2A6D;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }
        .logout-link:hover {
            background: #FF2A6D;
            color: #FFF;
        }
        .logout-link i {
            width: 24px;
            font-size: 16px;
        }
        
        .main-content { margin-left: 260px; flex: 1; padding: 20px; background: #1A1A1A; min-height: 100vh; }
        
        .top-bar {
            background: #0D0D0D;
            padding: 15px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            position: relative;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        @media (max-width: 480px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            .user-info {
                width: 100%;
                justify-content: flex-end;
            }
            .page-title h1 {
                font-size: 20px;
            }
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        .notification-bell .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #FF2A6D;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }
        .notification-dropdown {
            position: absolute;
            top: 50px;
            right: 20px;
            width: 350px;
            max-height: 400px;
            background: #0D0D0D;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.5);
            border: 1px solid #FF2A6D;
            z-index: 1000;
            display: none;
            overflow-y: auto;
        }
        
        @media (max-width: 480px) {
            .notification-dropdown {
                width: 280px;
                right: 10px;
            }
        }
        
        .notification-dropdown.show { display: block; }
        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #333;
            color: #FFF;
            font-weight: bold;
        }
        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #222;
            cursor: pointer;
            transition: background 0.3s;
        }
        .notification-item:hover { background: #1A1A1A; }
        .notification-item .notif-title {
            color: #FF2A6D;
            font-size: 13px;
            font-weight: bold;
        }
        .notification-item .notif-message {
            color: #CCC;
            font-size: 12px;
            margin-top: 5px;
        }
        .notification-item .notif-time {
            color: #888;
            font-size: 10px;
            margin-top: 5px;
        }
        
        .page-title h1 { color: #FFF; font-size: 24px; }
        .page-title p { color: #888; font-size: 13px; }
        .user-info { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .user-info i { color: #FF2A6D; font-size: 20px; cursor: pointer; }
        .user-name { 
            color: #FF2A6D; 
            font-weight: bold; 
            font-size: 14px; 
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .user-name:hover { color: #FFFFFF; text-decoration: underline; }
        .role-badge {
            background: #FF2A6D;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .stat-card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                text-align: left;
            }
            .stat-card i {
                margin-bottom: 0;
            }
            .stat-card div {
                text-align: right;
            }
        }
        
        .stat-card {
            background: #0D0D0D;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #FF2A6D;
            transition: transform 0.3s;
            cursor: pointer;
        }
        .stat-card:hover { transform: translateY(-5px); background: #141414; }
        .stat-card i { font-size: 40px; color: #FF2A6D; margin-bottom: 10px; }
        .stat-card h3 { color: #888; font-size: 13px; margin-bottom: 10px; }
        .stat-card .number { color: #FF2A6D; font-size: 32px; font-weight: bold; }
        
        .section-title {
            color: #FFF;
            font-size: 18px;
            margin-bottom: 20px;
            border-left: 3px solid #FF2A6D;
            padding-left: 12px;
        }
        
        .location-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .location-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .location-card {
            background: #0D0D0D;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            border: 1px solid #222;
            cursor: pointer;
        }
        .location-card:hover {
            border-color: #FF2A6D;
            transform: translateY(-3px);
        }
        .location-icon {
            width: 60px;
            height: 60px;
            background: #2A2A2A;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .location-icon i { font-size: 30px; color: #FF2A6D; }
        .location-info { flex: 1; min-width: 0; }
        .location-name { color: #FFF; font-size: 16px; font-weight: bold; margin-bottom: 5px; }
        .location-detail { color: #888; font-size: 12px; margin-bottom: 8px; }
        .location-stats { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 5px; }
        .location-qty { color: #FF2A6D; font-size: 20px; font-weight: bold; }
        .location-trend { font-size: 12px; color: #00FF88; }
        
        .card {
            background: #0D0D0D;
            border-radius: 12px;
            padding: 20px;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }
            .quick-actions a {
                width: 100%;
                justify-content: center;
            }
        }
        
        .btn-pink {
            background: #FF2A6D;
            color: #FFF;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-pink:hover { background: #D91A5C; transform: translateY(-2px); }
        .btn-outline {
            background: transparent;
            border: 1px solid #FF2A6D;
            color: #FF2A6D;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-outline:hover { background: #FF2A6D; color: #FFF; }
        
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-baik { background: #00FF88; color: #000; }
        .status-rusak { background: #FF2A6D; color: #FFF; }
        .status-expired { background: #FFA500; color: #000; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        .modal-content {
            background: #0D0D0D;
            border-radius: 12px;
            width: 800px;
            max-width: 90%;
            max-height: 80vh;
            border: 1px solid #FF2A6D;
            display: flex;
            flex-direction: column;
        }
        
        @media (max-width: 480px) {
            .modal-content {
                width: 95%;
                max-height: 85vh;
            }
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .modal-header h3 { color: #FFF; font-size: 18px; }
        .modal-header .close { color: #FF2A6D; font-size: 24px; cursor: pointer; }
        .modal-body { padding: 20px; overflow-y: auto; flex: 1; }
        
        .item-table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 500px;
        }
        .item-table th, .item-table td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #222; 
            color: #CCC; 
            font-size: 13px; 
        }
        .item-table th { color: #FF2A6D; font-weight: bold; }
        .item-table tr:hover { background: #1A1A1A; }
        
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .nav-menu::-webkit-scrollbar { width: 3px; }
        .nav-menu::-webkit-scrollbar-track { background: #1A1A1A; }
        .nav-menu::-webkit-scrollbar-thumb { background: #FF2A6D; border-radius: 3px; }
    </style>
</head>
<body>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="assest/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/60?text=Logo'">
            <h3>Apex Academy Indonesia</h3>
            <p>Inventory Aset</p>
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dasbor</a></li>
            <?php if($is_admin): ?>
            <li><a href="data-master.php" class="nav-link"><i class="fas fa-database"></i> Data Master</a></li>
            <?php endif; ?>
            <li><a href="inventory.php" class="nav-link"><i class="fas fa-boxes"></i> Inventaris</a></li>
            <li><a href="transaksi.php" class="nav-link"><i class="fas fa-money-bill"></i> Transaksi</a></li>
            <li><a href="distribusi.php" class="nav-link"><i class="fas fa-truck"></i> Distribusi</a></li>
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
            <div class="page-title">
                <h1>Dashboard</h1>
                <p>Selamat datang, <?php echo $_SESSION['username'] ?? 'Admin'; ?></p>
            </div>
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

        <?php
        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity) as total FROM inventory"))['total'] ?? 0;
        $baik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(inventory.quantity) as total FROM inventory JOIN items ON inventory.item_id = items.id WHERE items.status = 'baik'"))['total'] ?? 0;
        $rusak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(inventory.quantity) as total FROM inventory JOIN items ON inventory.item_id = items.id WHERE items.status = 'rusak'"))['total'] ?? 0;
        $expired = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(inventory.quantity) as total FROM inventory WHERE expired_date IS NOT NULL AND expired_date < CURDATE()"))['total'] ?? 0;
        ?>

        <div class="stats-grid">
            <div class="stat-card" onclick="showBarangDetail('semua')"><i class="fas fa-boxes"></i><div><h3>Total Barang</h3><div class="number"><?php echo number_format($total); ?></div></div></div>
            <div class="stat-card" onclick="showBarangDetail('baik')"><i class="fas fa-check-circle"></i><div><h3>Barang Baik</h3><div class="number"><?php echo number_format($baik); ?></div></div></div>
            <div class="stat-card" onclick="showBarangDetail('rusak')"><i class="fas fa-exclamation-triangle"></i><div><h3>Rusak</h3><div class="number"><?php echo number_format($rusak); ?></div></div></div>
            <div class="stat-card" onclick="showBarangDetail('expired')"><i class="fas fa-calendar-times"></i><div><h3>Expired</h3><div class="number"><?php echo number_format($expired); ?></div></div></div>
        </div>

        <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Barang per Lokasi</h3>
        <div class="location-grid" id="locationGrid">
            <?php
            $query = "SELECT b.id, b.name as building_name, COALESCE(SUM(i.quantity), 0) as total FROM buildings b LEFT JOIN rooms r ON b.id = r.building_id LEFT JOIN inventory i ON r.id = i.room_id GROUP BY b.id ORDER BY total DESC";
            $result = mysqli_query($conn, $query);
            $icons = ['Gedung Utama' => 'fa-building', 'Gedung Timur' => 'fa-building', 'Gedung Barat' => 'fa-building', 'Gudang Pusat' => 'fa-warehouse', 'Gedung Perpus' => 'fa-book', 'GOR' => 'fa-futbol', 'Lab Bahasa' => 'fa-language', 'Lab Komputer' => 'fa-laptop-code'];
            $default_icon = 'fa-building';
            while($row = mysqli_fetch_assoc($result)) {
                $building_id = $row['id']; $building_name = $row['building_name']; $total_qty = $row['total']; $icon = $icons[$building_name] ?? $default_icon;
                $trend_text = $total_qty > 100 ? '↑ High Stock' : ($total_qty > 0 ? '✓ Active' : '○ Empty');
                echo "<div class='location-card' onclick='showBuildingDetail($building_id, \"$building_name\")'><div class='location-icon'><i class='fas $icon'></i></div><div class='location-info'><div class='location-name'>$building_name</div><div class='location-detail'>Total Aset</div><div class='location-stats'><span class='location-qty'>" . number_format($total_qty) . "</span><span class='location-trend'>$trend_text</span></div></div></div>";
            }
            if(mysqli_num_rows($result) == 0) {
                echo "<div class='location-card' onclick='showBuildingDetail(1, \"Gedung Utama\")'><div class='location-icon'><i class='fas fa-building'></i></div><div class='location-info'><div class='location-name'>Gedung Utama</div><div class='location-detail'>Total Aset</div><div class='location-stats'><span class='location-qty'>33</span><span class='location-trend'>✓ Active</span></div></div></div>
                      <div class='location-card' onclick='showBuildingDetail(2, \"Gedung Timur\")'><div class='location-icon'><i class='fas fa-building'></i></div><div class='location-info'><div class='location-name'>Gedung Timur</div><div class='location-detail'>Total Aset</div><div class='location-stats'><span class='location-qty'>30</span><span class='location-trend'>✓ Active</span></div></div></div>
                      <div class='location-card' onclick='showBuildingDetail(3, \"Gudang Pusat\")'><div class='location-icon'><i class='fas fa-warehouse'></i></div><div class='location-info'><div class='location-name'>Gudang Pusat</div><div class='location-detail'>Total Aset</div><div class='location-stats'><span class='location-qty'>1</span><span class='location-trend'>○ Empty</span></div></div></div>";
            }
            ?>
        </div>

        <div class="card">
            <h3 style="color: #FFF; margin-bottom: 15px;"><i class="fas fa-bolt"></i> Akses Cepat</h3>
            <div class="quick-actions">
                <a href="inventory.php" class="btn-pink"><i class="fas fa-plus-circle"></i> Tambah Stok</a>
                <a href="transaksi.php" class="btn-outline"><i class="fas fa-exchange-alt"></i> Input Transaksi</a>
                <a href="distribusi.php" class="btn-outline"><i class="fas fa-truck"></i> Distribusi Barang</a>
                <a href="laporan.php" class="btn-outline"><i class="fas fa-chart-line"></i> Lihat Laporan</a>
            </div>
        </div>
    </div>

    <div id="roomModal" class="modal"><div class="modal-content"><div class="modal-header"><h3 id="modalTitle">Detail Ruangan</h3><span class="close" onclick="closeModal('roomModal')">&times;</span></div><div class="modal-body" id="modalBody"><div style="text-align: center; color: #888;">Memuat...</div></div></div></div>
    <div id="barangModal" class="modal"><div class="modal-content"><div class="modal-header"><h3 id="barangModalTitle">Daftar Barang</h3><span class="close" onclick="closeModal('barangModal')">&times;</span></div><div class="modal-body" id="barangModalBody"><div style="text-align: center; color: #888;">Memuat...</div></div></div></div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        
        if(menuToggle && sidebar) {
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
                const icon = menuToggle.querySelector('i');
                if(sidebar.classList.contains('show')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            });
            
            document.addEventListener('click', function(e) {
                const isMobile = window.innerWidth <= 768;
                if(isMobile && sidebar.classList.contains('show')) {
                    if(!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                        const icon = menuToggle.querySelector('i');
                        icon.className = 'fas fa-bars';
                    }
                }
            });
            
            window.addEventListener('resize', function() {
                if(window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    const icon = menuToggle.querySelector('i');
                    icon.className = 'fas fa-bars';
                }
            });
        }
        
        function loadNotifications() {
            fetch('get_notifications.php').then(r=>r.json()).then(data=>{
                const notifList = document.getElementById('notificationList'); const notifBadge = document.getElementById('notifBadge');
                if(!notifList) return;
                notifList.innerHTML = '';
                let notifCount = data.filter(n => n.type !== 'success').length;
                if(notifCount > 0) { notifBadge.style.display = 'inline-block'; notifBadge.textContent = notifCount; }
                else { notifBadge.style.display = 'none'; }
                data.forEach(notif => {
                    const notifItem = document.createElement('div'); notifItem.className = 'notification-item';
                    notifItem.innerHTML = `<div class="notif-title"><i class="fas ${notif.icon}" style="color: ${notif.color}; margin-right: 8px;"></i>${notif.title}</div><div class="notif-message">${notif.message}</div><div class="notif-time"><i class="fas fa-clock"></i> ${notif.time}</div>`;
                    notifItem.style.cursor = 'pointer';
                    if(notif.title === 'Stok Menipis') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=low_stock';
                    else if(notif.title === 'Barang Rusak') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=broken';
                    else if(notif.title === 'Barang Expired') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=expired';
                    notifList.appendChild(notifItem);
                });
            }).catch(error => console.error('Error:', error));
        }
        
        document.getElementById('notificationBell').addEventListener('click', function(e) { e.stopPropagation(); document.getElementById('notificationDropdown').classList.toggle('show'); });
        document.addEventListener('click', function() { document.getElementById('notificationDropdown').classList.remove('show'); });
        document.getElementById('notificationDropdown').addEventListener('click', function(e) { e.stopPropagation(); });
        
        function showBuildingDetail(buildingId, buildingName) {
            const modal = document.getElementById('roomModal'); const modalTitle = document.getElementById('modalTitle'); const modalBody = document.getElementById('modalBody');
            modalTitle.innerHTML = `<i class="fas fa-building"></i> ${buildingName}`;
            modalBody.innerHTML = '<div style="text-align: center; color: #888;"><i class="fas fa-spinner fa-pulse"></i> Memuat data...</div>';
            modal.style.display = 'flex';
            fetch(`get_rooms_by_building.php?building_id=${buildingId}`).then(r=>r.json()).then(data=>{
                if(data.success && data.data.length > 0) {
                    let html = '<div class="table-wrapper"><table class="item-table"><thead><tr><th>Nama Ruangan</th><th>Jumlah Barang</th></tr></thead><tbody>';
                    data.data.forEach(room => { const qty = room.total; const statusColor = qty > 0 ? '#FF2A6D' : '#888'; html += `<tr><td><i class="fas fa-door-open"></i> ${room.room_name}</td><td style="color: ${statusColor};">${qty} unit</td></tr>`; });
                    html += '</tbody></table></div>'; modalBody.innerHTML = html;
                } else { modalBody.innerHTML = '<div style="text-align: center; color: #888;"><i class="fas fa-folder-open"></i> Belum ada ruangan di gedung ini</div>'; }
            }).catch(error => { modalBody.innerHTML = '<div style="text-align: center; color: #FF2A6D;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data</div>'; });
        }
        
        function showBarangDetail(status) {
            const modal = document.getElementById('barangModal'); const modalTitle = document.getElementById('barangModalTitle'); const modalBody = document.getElementById('barangModalBody');
            let title = status == 'semua' ? '📦 Semua Barang' : (status == 'baik' ? '✅ Barang dalam Kondisi Baik' : (status == 'rusak' ? '⚠️ Barang Rusak' : '📅 Barang Expired'));
            modalTitle.innerHTML = title; modalBody.innerHTML = '<div style="text-align: center; color: #888;"><i class="fas fa-spinner fa-pulse"></i> Memuat data...</div>'; modal.style.display = 'flex';
            fetch(`get_barang_by_status.php?status=${status}`).then(r=>r.json()).then(data=>{
                if(data.success && data.data.length > 0) {
                    let html = '<div class="table-wrapper"><table class="item-table"><thead><tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Jumlah</th><th>Lokasi</th><th>Status</th></tr></thead><tbody>';
                    data.data.forEach((item, index) => {
                        let statusClass = item.status == 'baik' ? 'status-baik' : (item.status == 'rusak' ? 'status-rusak' : 'status-expired');
                        html += `<tr><td>${index+1}</td><td>${item.item_name}</td><td>${item.category || '-'}</td><td>${item.quantity} unit</td><td>${item.room || '-'}</td><td><span class="status-badge ${statusClass}">${item.status}</span></td></tr>`;
                    });
                    html += '</tbody></table></div>'; modalBody.innerHTML = html;
                } else { modalBody.innerHTML = '<div style="text-align: center; color: #888;"><i class="fas fa-folder-open"></i> Tidak ada data barang</div>'; }
            }).catch(error => { modalBody.innerHTML = '<div style="text-align: center; color: #FF2A6D;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data</div>'; });
        }
        
        function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
        window.onclick = function(event) { if(event.target == document.getElementById('roomModal')) document.getElementById('roomModal').style.display = 'none'; if(event.target == document.getElementById('barangModal')) document.getElementById('barangModal').style.display = 'none'; }
        loadNotifications(); setInterval(loadNotifications, 30000);
    </script>
</body>
</html>
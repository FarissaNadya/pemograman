<?php
require_once 'cek_akses.php';
require_staff(); // Staff dan Admin boleh akses
require_once 'koneksi.php';

$is_admin = ($_SESSION['role'] == 'admin');
$is_staff = ($_SESSION['role'] == 'staff');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Inventory Aset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .filter-bar { background: #0D0D0D; border-radius: 12px; padding: 15px 20px; margin-bottom: 20px; }
        .filter-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
        .filter-group { flex: 1; min-width: 150px; }
        .filter-group label { display: block; color: #FFF; margin-bottom: 6px; font-size: 12px; }
        .filter-group input, .filter-group select { width: 100%; padding: 8px 10px; background: #2A2A2A; border: 1px solid #333; border-radius: 8px; color: #FFF; font-size: 13px; }
        .btn-filter { background: #FF2A6D; color: #FFF; padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 13px; }
        
        .tabs { display: flex; gap: 5px; margin-bottom: 20px; background: #0D0D0D; border-radius: 12px; padding: 5px; flex-wrap: wrap; }
        .tab-btn { padding: 10px 20px; background: transparent; border: none; color: #CCC; cursor: pointer; border-radius: 8px; font-size: 13px; }
        .tab-btn.active { background: #FF2A6D; color: #FFF; }
        
        .tab-content { display: none; background: #0D0D0D; border-radius: 12px; padding: 20px; }
        .tab-content.active { display: block; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #222; color: #CCC; font-size: 13px; }
        th { color: #FF2A6D; }
        
        .export-buttons { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 15px; }
        .btn-export { background: #333; color: #FF2A6D; padding: 6px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; }
        
        canvas { max-height: 280px; margin-top: 15px; }
        
        .table-wrapper { overflow-x: auto; max-height: 350px; overflow-y: auto; }
        .table-wrapper::-webkit-scrollbar { width: 5px; height: 5px; }
        .table-wrapper::-webkit-scrollbar-track { background: #1A1A1A; }
        .table-wrapper::-webkit-scrollbar-thumb { background: #FF2A6D; border-radius: 5px; }
        
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
            <li><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dasbor</a></li>
            <?php if($is_admin): ?>
            <li><a href="data-master.php" class="nav-link"><i class="fas fa-database"></i> Data Master</a></li>
            <?php endif; ?>
            <li><a href="inventory.php" class="nav-link"><i class="fas fa-boxes"></i> Inventaris</a></li>
            <li><a href="transaksi.php" class="nav-link"><i class="fas fa-money-bill"></i> Transaksi</a></li>
            <li><a href="distribusi.php" class="nav-link"><i class="fas fa-truck"></i> Distribusi</a></li>
            <li><a href="laporan.php" class="nav-link active"><i class="fas fa-chart-line"></i> Laporan</a></li>
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
            <div class="page-title"><h1>Laporan</h1><p>Lihat dan ekspor data laporan</p></div>
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

        <div class="filter-bar">
            <div class="filter-row">
                <div class="filter-group"><label>Periode Mulai</label><input type="date" id="startDate"></div>
                <div class="filter-group"><label>Periode Selesai</label><input type="date" id="endDate"></div>
                <div class="filter-group"><label>Kategori</label><select id="filterKategori"><option value="">Semua</option></select></div>
                <div class="filter-group"><label>Lokasi</label><select id="filterLokasi"><option value="">Semua</option></select></div>
                <button class="btn-filter" onclick="applyFilter()"><i class="fas fa-search"></i> Terapkan</button>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="openTab(event, 'stock')">Laporan Stok Barang</button>
            <button class="tab-btn" onclick="openTab(event, 'transaction')">Laporan Transaksi</button>
            <button class="tab-btn" onclick="openTab(event, 'budget')">Laporan Anggaran vs Realisasi</button>
        </div>

        <div id="stock" class="tab-content active">
            <div class="export-buttons">
                <button class="btn-export" onclick="exportToPDF('stock')"><i class="fas fa-file-pdf"></i> PDF</button>
                <?php if($is_admin): ?>
                <button class="btn-export" onclick="exportToExcel('stock')"><i class="fas fa-file-excel"></i> Excel</button>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Lokasi</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $query = "SELECT i.id, it.name as item_name, c.name as category, i.quantity, r.name as room, it.status 
                                  FROM inventory i 
                                  JOIN items it ON i.item_id = it.id 
                                  JOIN rooms r ON i.room_id = r.id 
                                  LEFT JOIN categories c ON it.category_id = c.id";
                        $result = mysqli_query($conn, $query);
                        while($row = mysqli_fetch_assoc($result)) {
                            $status_class = ($row['status'] == 'rusak') ? 'style="color:#FF2A6D;"' : '';
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['item_name']}</td>
                                    <td>{$row['category']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['room']}</td>
                                    <td {$status_class}>{$row['status']}</td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="transaction" class="tab-content">
            <div class="export-buttons">
                <button class="btn-export" onclick="exportToPDF('transaction')"><i class="fas fa-file-pdf"></i> PDF</button>
                <?php if($is_admin): ?>
                <button class="btn-export" onclick="exportToExcel('transaction')"><i class="fas fa-file-excel"></i> Excel</button>
                <?php endif; ?>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>No</th><th>Tanggal</th><th>No Transaksi</th><th>Jenis</th><th>Anggaran</th><th>Realisasi</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $trans = mysqli_query($conn, "SELECT * FROM transactions ORDER BY transaction_date DESC");
                        while($t = mysqli_fetch_assoc($trans)) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$t['transaction_date']}</td>
                                    <td>{$t['transaction_number']}</td>
                                    <td>{$t['type']}</td>
                                    <td>Rp " . number_format($t['budget'],0,',','.') . "</td>
                                    <td>Rp " . number_format($t['realization'],0,',','.') . "</td>
                                    <td>{$t['status']}</td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="budget" class="tab-content">
            <div class="export-buttons">
                <button class="btn-export" onclick="exportChartAsImage()"><i class="fas fa-download"></i> Download Grafik</button>
            </div>
            <canvas id="budgetChart"></canvas>
            <div class="table-wrapper" style="margin-top: 20px;">
                <table>
                    <thead>
                        <tr><th>Bulan</th><th>Anggaran (Rp)</th><th>Realisasi (Rp)</th><th>Selisih (Rp)</th><th>Persentase</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $data_bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
                        $anggaran = [50000000, 55000000, 60000000, 58000000, 62000000, 70000000];
                        $realisasi = [45000000, 50000000, 55000000, 52000000, 58000000, 65000000];
                        
                        for($i=0; $i<6; $i++) {
                            $selisih = $anggaran[$i] - $realisasi[$i];
                            $persen = ($realisasi[$i] / $anggaran[$i]) * 100;
                            $warna = ($selisih < 0) ? '#FF2A6D' : '#00FF88';
                            echo "<tr>
                                    <td>{$data_bulan[$i]}</td>
                                    <td>Rp " . number_format($anggaran[$i],0,',','.') . "</td>
                                    <td>Rp " . number_format($realisasi[$i],0,',','.') . "</td>
                                    <td style='color:{$warna}'>Rp " . number_format(abs($selisih),0,',','.') . "</td>
                                    <td style='color:{$warna}'>" . number_format($persen,1) . "%</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
            if(tabName === 'budget' && window.budgetChart) {
                setTimeout(() => window.budgetChart.resize(), 100);
            }
        }
        
        function applyFilter() { 
            alert('Filter akan diterapkan sesuai periode yang dipilih!'); 
        }
        
        function exportToPDF(type) { 
            window.open('export_pdf.php?type=' + type, '_blank'); 
        }
        
        function exportToExcel(type) { 
            window.location.href = 'export_excel.php?type=' + type; 
        }
        
        function exportChartAsImage() {
            if(window.budgetChart) {
                const link = document.createElement('a');
                link.download = 'grafik-anggaran-realisasi.png';
                link.href = window.budgetChart.toBase64Image();
                link.click();
                alert('Grafik berhasil diunduh!');
            }
        }
        
        const ctx = document.getElementById('budgetChart').getContext('2d');
        window.budgetChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Anggaran (Juta Rp)',
                    data: [50, 55, 60, 58, 62, 70],
                    backgroundColor: '#FF2A6D',
                    borderRadius: 8
                }, {
                    label: 'Realisasi (Juta Rp)',
                    data: [45, 50, 55, 52, 58, 65],
                    backgroundColor: '#FFFFFF',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { 
                    legend: { labels: { color: '#FFFFFF' } } 
                },
                scales: { 
                    y: { ticks: { color: '#CCC' }, grid: { color: '#333' } }, 
                    x: { ticks: { color: '#CCC' }, grid: { color: '#333' } } 
                }
            }
        });
        
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        document.getElementById('startDate').valueAsDate = firstDay;
        document.getElementById('endDate').valueAsDate = today;
        
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
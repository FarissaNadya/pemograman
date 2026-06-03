<?php
require_once 'cek_akses.php';
require_staff(); // Staff dan Admin boleh akses
require_once 'koneksi.php';

$is_admin = ($_SESSION['role'] == 'admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Sistem Inventory Aset</title>
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
        
        .form-card { background: #0D0D0D; border-radius: 12px; padding: 25px; max-width: 100%; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #FFF; margin-bottom: 6px; font-size: 13px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; background: #2A2A2A; border: 1px solid #333; border-radius: 8px; color: #FFF; font-size: 13px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #FF2A6D; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .items-list { background: #1A1A1A; border-radius: 8px; padding: 15px; margin: 15px 0; }
        .items-list h4 { color: #FF2A6D; margin-bottom: 10px; font-size: 14px; }
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
        .item-row input { flex: 1; padding: 8px; background: #2A2A2A; border: 1px solid #333; border-radius: 6px; color: #FFF; font-size: 13px; }
        .item-row button { background: #FF2A6D; color: #FFF; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; }
        .btn-pink { background: #FF2A6D; color: #FFF; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; margin-right: 10px; }
        .btn-outline { background: transparent; border: 1px solid #FF2A6D; color: #FF2A6D; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-size: 14px; }
        .btn-add-item { background: #333; color: #FF2A6D; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; margin-top: 10px; font-size: 12px; }
        .success-msg { background: #00FF88; color: #000; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 13px; }
        .error-msg { background: #FF2A6D; color: #FFF; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 13px; }
        
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
            <li><a href="transaksi.php" class="nav-link active"><i class="fas fa-money-bill"></i> Transaksi</a></li>
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
            <div class="page-title"><h1>Input Transaksi</h1><p>Catat transaksi masuk/keluar barang</p></div>
            <div class="user-info">
                <span class="role-badge"><?php echo ($is_admin ? '👑 ADMIN' : '📋 STAFF INVENTORY'); ?></span>
                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notifBadge" style="display: none;">0</span>
                </div>
                <i class="fas fa-user-circle"></i>
                <a href="profil.php" class="user-name"><?php echo $_SESSION['username']; ?></a>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header"><i class="fas fa-bell"></i> Notifikasi</div>
                <div id="notificationList"><div class="notification-item">Memuat...</div></div>
            </div>
        </div>

        <div class="form-card">
            <?php if(isset($_GET['success'])): ?>
                <div class="success-msg"><i class="fas fa-check-circle"></i> Transaksi berhasil disimpan dan stok terupdate!</div>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <div class="error-msg"><i class="fas fa-exclamation-triangle"></i> Error: <?php echo htmlspecialchars($_GET['msg'] ?? ''); ?></div>
            <?php endif; ?>

            <form method="POST" action="save_transaksi.php" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Tanggal Transaksi</label>
                        <input type="date" name="tanggal" id="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Nomor Transaksi</label>
                        <input type="text" name="nomor_transaksi" placeholder="TRX/001/2026" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Jenis Transaksi</label>
                        <select name="jenis_transaksi" required>
                            <option value="">Pilih Jenis</option>
                            <?php
                            $trans = mysqli_query($conn, "SELECT * FROM transaction_types");
                            while($t = mysqli_fetch_assoc($trans)) {
                                echo "<option value='{$t['name']}'>{$t['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Anggaran (Rp)</label>
                        <input type="text" name="anggaran" id="anggaran" placeholder="50.000.000">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-chart-bar"></i> Realisasi (Rp)</label>
                        <input type="text" name="realisasi" id="realisasi" placeholder="48.000.000">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-file"></i> Bukti File</label>
                        <input type="file" name="bukti_file" accept=".pdf,.jpg,.png">
                    </div>
                </div>

                <div class="items-list">
                    <h4><i class="fas fa-box"></i> Daftar Barang dalam Transaksi</h4>
                    <div id="itemsContainer">
                        <div class="item-row">
                            <input type="text" name="item_name[]" placeholder="Nama Barang" required>
                            <input type="number" name="item_qty[]" placeholder="Jumlah" style="width: 100px;" required>
                            <input type="text" name="item_price[]" placeholder="Harga Satuan" style="width: 150px;">
                            <button type="button" onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn-add-item" onclick="addItem()"><i class="fas fa-plus"></i> Tambah Barang</button>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-flag"></i> Status Transaksi</label>
                    <select name="status_transaksi">
                        <option value="Selesai">Selesai</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-pink"><i class="fas fa-save"></i> Simpan Transaksi</button>
                    <button type="reset" class="btn-outline"><i class="fas fa-undo"></i> Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addItem() {
            let container = document.getElementById('itemsContainer');
            let newRow = document.createElement('div');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <input type="text" name="item_name[]" placeholder="Nama Barang">
                <input type="number" name="item_qty[]" placeholder="Jumlah" style="width: 100px;">
                <input type="text" name="item_price[]" placeholder="Harga Satuan" style="width: 150px;">
                <button type="button" onclick="removeItem(this)"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(newRow);
        }
        function removeItem(btn) {
            if(document.querySelectorAll('.item-row').length > 1) {
                btn.closest('.item-row').remove();
            }
        }
        document.getElementById('tanggal').valueAsDate = new Date();
        function formatRupiah(input) {
            let value = input.value.replace(/\./g, '');
            if(value) input.value = new Intl.NumberFormat('id-ID').format(value);
        }
        document.getElementById('anggaran').addEventListener('input', function() { formatRupiah(this); });
        document.getElementById('realisasi').addEventListener('input', function() { formatRupiah(this); });
        
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
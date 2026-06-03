<?php
require_once 'cek_akses.php';
require_admin();
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Data Master - Sistem Inventory Aset</title>
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
            transition: transform 0.3s ease;
            z-index: 100;
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
        .top-bar { background: #0D0D0D; padding: 15px 25px; border-radius: 12px; display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; flex-wrap: wrap; gap: 15px; }
        
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
        
        .page-title h1 { color: #FFF; font-size: 24px; }
        .page-title p { color: #888; font-size: 13px; }
        .user-info { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .user-info i { color: #FF2A6D; font-size: 20px; }
        .user-name { color: #FF2A6D; font-weight: bold; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.3s; }
        .user-name:hover { color: #FFFFFF; text-decoration: underline; }
        .role-badge { background: #FF2A6D; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        
        .notification-bell { position: relative; cursor: pointer; }
        .notification-bell .badge { position: absolute; top: -8px; right: -8px; background: #FF2A6D; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold; }
        .notification-dropdown { position: absolute; top: 50px; right: 20px; width: 350px; max-height: 400px; background: #0D0D0D; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.5); border: 1px solid #FF2A6D; z-index: 1000; display: none; overflow-y: auto; }
        
        @media (max-width: 480px) {
            .notification-dropdown {
                width: 280px;
                right: 10px;
            }
        }
        
        .notification-dropdown.show { display: block; }
        .notification-header { padding: 15px; border-bottom: 1px solid #333; color: #FFF; font-weight: bold; }
        .notification-item { padding: 12px 15px; border-bottom: 1px solid #222; cursor: pointer; transition: background 0.3s; }
        .notification-item:hover { background: #1A1A1A; }
        .notification-item .notif-title { color: #FF2A6D; font-size: 13px; font-weight: bold; }
        .notification-item .notif-message { color: #CCC; font-size: 12px; margin-top: 5px; }
        .notification-item .notif-time { color: #888; font-size: 10px; margin-top: 5px; }
        
        .tabs { display: flex; gap: 5px; margin-bottom: 20px; background: #0D0D0D; border-radius: 12px; padding: 5px; flex-wrap: wrap; overflow-x: auto; }
        .tab-btn { padding: 12px 25px; background: transparent; border: none; color: #CCC; cursor: pointer; border-radius: 8px; font-size: 14px; white-space: nowrap; }
        .tab-btn.active { background: #FF2A6D; color: #FFF; }
        .tab-content { display: none; background: #0D0D0D; border-radius: 12px; padding: 20px; }
        .tab-content.active { display: block; }
        .btn-add { background: #FF2A6D; color: #FFF; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; margin-bottom: 20px; font-size: 14px; }
        
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        table { width: 100%; border-collapse: collapse; min-width: 400px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #222; color: #CCC; font-size: 14px; }
        th { color: #FF2A6D; }
        .edit-btn, .delete-btn { color: #FF2A6D; cursor: pointer; margin: 0 5px; font-size: 18px; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #1A1A1A; padding: 30px; border-radius: 12px; width: 450px; max-width: 90%; border: 1px solid #FF2A6D; }
        @media (max-width: 480px) {
            .modal-content { padding: 20px; }
            .modal-content h3 { font-size: 16px; }
        }
        .modal-content h3 { color: #FFF; margin-bottom: 20px; font-size: 18px; }
        .modal-content input, .modal-content select { width: 100%; padding: 12px; margin: 10px 0; background: #2A2A2A; border: 1px solid #333; border-radius: 8px; color: #FFF; font-size: 14px; }
        .modal-buttons { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
        .modal-buttons button { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; }
        .save-btn { background: #FF2A6D; color: #FFF; }
        .cancel-btn { background: #333; color: #FFF; }
        
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
            <li><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dasbor</a></li>
            <li><a href="data-master.php" class="nav-link active"><i class="fas fa-database"></i> Data Master</a></li>
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
            <div class="page-title"><h1>Data Master</h1><p>Kelola data master sistem (Hanya Admin)</p></div>
            <div class="user-info">
                <span class="role-badge">👑 ADMIN</span>
                <div class="notification-bell" id="notificationBell"><i class="fas fa-bell"></i><span class="badge" id="notifBadge" style="display: none;">0</span></div>
                <i class="fas fa-user-circle"></i>
                <a href="profil.php" class="user-name"><?php echo $_SESSION['username'] ?? 'Admin'; ?></a>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header"><i class="fas fa-bell"></i> Notifikasi</div>
                <div id="notificationList"><div class="notification-item">Memuat...</div></div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('categories')">Jenis Barang</button>
            <button class="tab-btn" onclick="showTab('items')">Barang</button>
            <button class="tab-btn" onclick="showTab('buildings')">Gedung</button>
            <button class="tab-btn" onclick="showTab('rooms')">Ruangan</button>
            <button class="tab-btn" onclick="showTab('transaction_types')">Jenis Transaksi</button>
        </div>

        <div id="categories" class="tab-content active"><button class="btn-add" onclick="openModal('categories')">+ Tambah Kategori</button><div class="table-wrapper"><table><thead><tr><th>No</th><th>Nama Kategori</th><th>Aksi</th></tr></thead><tbody id="categoriesBody"></tbody></table></div></div>
        <div id="items" class="tab-content"><button class="btn-add" onclick="openModal('items')">+ Tambah Barang</button><div class="table-wrapper"><table><thead><tr><th>No</th><th>Nama Barang</th><th>Kategori</th><th>Aksi</th></tr></thead><tbody id="itemsBody"></tbody></table></div></div>
        <div id="buildings" class="tab-content"><button class="btn-add" onclick="openModal('buildings')">+ Tambah Gedung</button><div class="table-wrapper"><table><thead><tr><th>No</th><th>Nama Gedung</th><th>Aksi</th></tr></thead><tbody id="buildingsBody"></tbody></table></div></div>
        <div id="rooms" class="tab-content"><button class="btn-add" onclick="openModal('rooms')">+ Tambah Ruangan</button><div class="table-wrapper"><table><thead><tr><th>No</th><th>Nama Ruangan</th><th>Gedung</th><th>Aksi</th></tr></thead><tbody id="roomsBody"></tbody></table></div></div>
        <div id="transaction_types" class="tab-content"><button class="btn-add" onclick="openModal('transaction_types')">+ Tambah Jenis Transaksi</button><div class="table-wrapper"><table><thead><tr><th>No</th><th>Jenis Transaksi</th><th>Aksi</th></tr></thead><tbody id="transTypesBody"></tbody></table></div></div>
    </div>

    <div id="modal" class="modal"><div class="modal-content"><h3 id="modalTitle">Tambah Data</h3><input type="hidden" id="editId" value="0"><input type="hidden" id="currentTable"><input type="text" id="modalNama" placeholder="Nama"><div id="extraField"></div><div class="modal-buttons"><button class="save-btn" onclick="saveData()">Simpan</button><button class="cancel-btn" onclick="closeModal()">Batal</button></div></div></div>

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
        
        let kategoriList = [], gedungList = [];
        function loadKategori() { fetch('get_categories.php').then(r=>r.json()).then(d=>kategoriList=d); }
        function loadGedung() { fetch('get_buildings.php').then(r=>r.json()).then(d=>gedungList=d); }
        function loadCategories() { fetch('get_categories.php').then(r=>r.json()).then(data=>{ let html=''; data.forEach((item,i)=>{ html+=`<tr><td>${i+1}</td><td>${item.name}</td><td><i class="fas fa-edit edit-btn" onclick="openEditModal('categories',${item.id},'${item.name}')"></i> <i class="fas fa-trash delete-btn" onclick="deleteData('categories',${item.id})"></i></td></tr>`; }); document.getElementById('categoriesBody').innerHTML=html; }); }
        function loadItems() { fetch('get_items.php').then(r=>r.json()).then(data=>{ let html=''; data.forEach((item,i)=>{ html+=`<tr><td>${i+1}</td><td>${item.name}</td><td>${item.category_name||'-'}</td><td><i class="fas fa-edit edit-btn" onclick="openEditModal('items',${item.id},'${item.name}',${item.category_id})"></i> <i class="fas fa-trash delete-btn" onclick="deleteData('items',${item.id})"></i></td></tr>`; }); document.getElementById('itemsBody').innerHTML=html; }); }
        function loadBuildings() { fetch('get_buildings.php').then(r=>r.json()).then(data=>{ let html=''; data.forEach((item,i)=>{ html+=`<tr><td>${i+1}</td><td>${item.name}</td><td><i class="fas fa-edit edit-btn" onclick="openEditModal('buildings',${item.id},'${item.name}')"></i> <i class="fas fa-trash delete-btn" onclick="deleteData('buildings',${item.id})"></i></td></tr>`; }); document.getElementById('buildingsBody').innerHTML=html; }); }
        function loadRooms() { fetch('get_rooms.php').then(r=>r.json()).then(data=>{ let html=''; data.forEach((item,i)=>{ html+=`<tr><td>${i+1}</td><td>${item.name}</td><td>${item.building_name||'-'}</td><td><i class="fas fa-edit edit-btn" onclick="openEditModal('rooms',${item.id},'${item.name}',${item.building_id})"></i> <i class="fas fa-trash delete-btn" onclick="deleteData('rooms',${item.id})"></i></td></tr>`; }); document.getElementById('roomsBody').innerHTML=html; }); }
        function loadTransTypes() { fetch('get_transaction_types.php').then(r=>r.json()).then(data=>{ let html=''; data.forEach((item,i)=>{ html+=`<tr><td>${i+1}</td><td>${item.name}</td><td><i class="fas fa-edit edit-btn" onclick="openEditModal('transaction_types',${item.id},'${item.name}')"></i> <i class="fas fa-trash delete-btn" onclick="deleteData('transaction_types',${item.id})"></i></td></tr>`; }); document.getElementById('transTypesBody').innerHTML=html; }); }
        function showTab(tabId) { document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active')); document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active')); document.getElementById(tabId).classList.add('active'); event.target.classList.add('active'); if(tabId=='categories') loadCategories(); else if(tabId=='items') loadItems(); else if(tabId=='buildings') loadBuildings(); else if(tabId=='rooms') loadRooms(); else if(tabId=='transaction_types') loadTransTypes(); }
        function openModal(table, id=0, name='', extraId=0) { document.getElementById('currentTable').value=table; document.getElementById('editId').value=id; document.getElementById('modalTitle').innerHTML=(id==0?'Tambah ':'Edit ')+table.replace('_',' '); document.getElementById('modalNama').value=name; let extra=document.getElementById('extraField'); if(table=='items') extra.innerHTML=`<select id="kategori_id"><option value="">Pilih Kategori</option>${kategoriList.map(k=>`<option value="${k.id}" ${k.id==extraId?'selected':''}>${k.name}</option>`).join('')}</select>`; else if(table=='rooms') extra.innerHTML=`<select id="gedung_id"><option value="">Pilih Gedung</option>${gedungList.map(g=>`<option value="${g.id}" ${g.id==extraId?'selected':''}>${g.name}</option>`).join('')}</select>`; else extra.innerHTML=''; document.getElementById('modal').style.display='flex'; }
        function openEditModal(table,id,name,extraId){ openModal(table,id,name,extraId); }
        function saveData() { let table=document.getElementById('currentTable').value, id=document.getElementById('editId').value, nama=document.getElementById('modalNama').value; if(!nama){ alert('Nama harus diisi!'); return; } let formData=new FormData(); formData.append('type',table); formData.append('nama',nama); if(table=='items'){ let kid=document.getElementById('kategori_id').value; if(!kid){ alert('Pilih kategori!'); return; } formData.append('kategori_id',kid); } if(table=='rooms'){ let gid=document.getElementById('gedung_id').value; if(!gid){ alert('Pilih gedung!'); return; } formData.append('gedung_id',gid); } let url=(id==0)?'save_master.php':'update_master.php'; if(id!=0) formData.append('id',id); fetch(url,{method:'POST',body:formData}).then(r=>r.json()).then(data=>{ if(data.success){ alert('Data berhasil disimpan!'); closeModal(); location.reload(); } else alert('Error: '+data.error); }); }
        function deleteData(table,id){ if(confirm('Yakin ingin menghapus?')){ let formData=new FormData(); formData.append('table',table); formData.append('id',id); fetch('delete_master.php',{method:'POST',body:formData}).then(r=>r.json()).then(data=>{ if(data.success){ alert('Data berhasil dihapus!'); location.reload(); } else alert('Error: '+data.error); }); } }
        function closeModal(){ document.getElementById('modal').style.display='none'; }
        window.onclick=function(e){ if(e.target==document.getElementById('modal')) closeModal(); }
        
        function loadNotifikasi() { fetch('get_notifications.php').then(r=>r.json()).then(data=>{ const notifList = document.getElementById('notificationList'); const notifBadge = document.getElementById('notifBadge'); if(!notifList) return; notifList.innerHTML = ''; let notifCount = data.filter(n => n.type !== 'success').length; if(notifCount > 0) { notifBadge.style.display = 'inline-block'; notifBadge.textContent = notifCount; } else { notifBadge.style.display = 'none'; } data.forEach(notif => { const notifItem = document.createElement('div'); notifItem.className = 'notification-item'; notifItem.innerHTML = `<div class="notif-title"><i class="fas ${notif.icon}" style="color: ${notif.color}; margin-right: 8px;"></i>${notif.title}</div><div class="notif-message">${notif.message}</div><div class="notif-time"><i class="fas fa-clock"></i> ${notif.time}</div>`; notifItem.style.cursor = 'pointer'; if(notif.title === 'Stok Menipis') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=low_stock'; else if(notif.title === 'Barang Rusak') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=broken'; else if(notif.title === 'Barang Expired') notifItem.onclick = () => window.location.href = 'notifikasi_detail.php?type=expired'; notifList.appendChild(notifItem); }); }).catch(error => console.error('Error:', error)); }
        document.getElementById('notificationBell').addEventListener('click', function(e) { e.stopPropagation(); document.getElementById('notificationDropdown').classList.toggle('show'); });
        document.addEventListener('click', function() { document.getElementById('notificationDropdown').classList.remove('show'); });
        document.getElementById('notificationDropdown').addEventListener('click', function(e) { e.stopPropagation(); });
        
        loadKategori(); loadGedung(); loadCategories(); loadNotifikasi(); setInterval(loadNotifikasi, 30000);
    </script>
</body>
</html>
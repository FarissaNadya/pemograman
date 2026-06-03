<?php
require_once 'cek_akses.php';
require_staff();
require_once 'koneksi.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';
$is_admin = ($_SESSION['role'] == 'admin');

// Ambil data profil
$query = "SELECT p.*, u.username FROM admin_profile p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$profil = mysqli_fetch_assoc($result);

// Jika belum ada profil, buat default
if(!$profil) {
    mysqli_query($conn, "INSERT INTO admin_profile (user_id, nama_lengkap, email, jabatan) 
                         SELECT id, 'Administrator', 'admin@yayasan.com', 'Super Admin' 
                         FROM users WHERE id = '$user_id'");
    $result = mysqli_query($conn, $query);
    $profil = mysqli_fetch_assoc($result);
}

// Proses update profil
if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['ganti_password'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    
    // Upload foto
    $foto = $profil['foto'];
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/profil/";
        if(!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $foto = $target_dir . time() . '_' . basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
    }
    
    $update = "UPDATE admin_profile SET 
               nama_lengkap = '$nama_lengkap',
               email = '$email',
               no_telepon = '$no_telepon',
               alamat = '$alamat',
               jabatan = '$jabatan',
               foto = '$foto'
               WHERE user_id = '$user_id'";
    
    if(mysqli_query($conn, $update)) {
        $message = "Profil berhasil diupdate!";
        // Refresh data
        $result = mysqli_query($conn, $query);
        $profil = mysqli_fetch_assoc($result);
    } else {
        $error = "Gagal update: " . mysqli_error($conn);
    }
}

// Proses ganti password
if(isset($_POST['ganti_password'])) {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = md5($_POST['password_baru']);
    $konfirmasi = md5($_POST['konfirmasi']);
    
    $cek_user = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id'");
    $user_data = mysqli_fetch_assoc($cek_user);
    
    if($user_data['password'] != $password_lama) {
        $error = "Password lama salah!";
    } elseif($password_baru != $konfirmasi) {
        $error = "Password baru dan konfirmasi tidak cocok!";
    } else {
        mysqli_query($conn, "UPDATE users SET password = '$password_baru' WHERE id = '$user_id'");
        $message = "Password berhasil diubah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Sistem Inventory Aset</title>
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
        .user-name { color: #FF2A6D; font-weight: bold; font-size: 14px; cursor: pointer; }
        .user-name:hover { text-decoration: underline; }
        .role-badge { background: #FF2A6D; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        
        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
        }
        
        .profile-card {
            background: #0D0D0D;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #2A2A2A;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 3px solid #FF2A6D;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar i {
            font-size: 60px;
            color: #FF2A6D;
        }
        
        .profile-name {
            color: #FFF;
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .profile-role {
            color: #FF2A6D;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .profile-info {
            text-align: left;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        
        .profile-info p {
            color: #CCC;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .profile-info i {
            width: 25px;
            color: #FF2A6D;
        }
        
        .form-card {
            background: #0D0D0D;
            border-radius: 12px;
            padding: 25px;
        }
        
        .form-card h3 {
            color: #FFF;
            margin-bottom: 20px;
            border-left: 3px solid #FF2A6D;
            padding-left: 12px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #FFF;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            background: #2A2A2A;
            border: 1px solid #333;
            border-radius: 8px;
            color: #FFF;
            font-size: 14px;
        }
        
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #FF2A6D;
        }
        
        .btn-pink {
            background: #FF2A6D;
            color: #FFF;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #FF2A6D;
            color: #FF2A6D;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .success-msg {
            background: #00FF88;
            color: #000;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-msg {
            background: #FF2A6D;
            color: #FFF;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }
        
        .tab-btn {
            background: transparent;
            border: none;
            color: #CCC;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 8px;
        }
        
        .tab-btn.active {
            background: #FF2A6D;
            color: #FFF;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
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
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-header h3, .sidebar-header p, .nav-link span { display: none; }
            .main-content { margin-left: 70px; }
            .profile-container { grid-template-columns: 1fr; }
        }
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
            <li><a href="laporan.php" class="nav-link"><i class="fas fa-chart-line"></i> Laporan</a></li>
            <li><a href="profil.php" class="nav-link active"><i class="fas fa-user-circle"></i> Profil</a></li>
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
                <h1>Profil User</h1>
                <p>Kelola data pribadi Anda</p>
            </div>
            <div class="user-info">
                <span class="role-badge"><?php echo ($is_admin ? '👑 ADMIN' : '📋 STAFF INVENTORY'); ?></span>
                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notifBadge" style="display: none;">0</span>
                </div>
                <i class="fas fa-user-circle"></i>
                <span class="user-name" onclick="window.location.href='profil.php'"><?php echo $_SESSION['username']; ?></span>
            </div>
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header"><i class="fas fa-bell"></i> Notifikasi</div>
                <div id="notificationList"><div class="notification-item">Memuat...</div></div>
            </div>
        </div>

        <?php if($message): ?>
            <div class="success-msg"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php if(!empty($profil['foto']) && file_exists($profil['foto'])): ?>
                        <img src="<?php echo $profil['foto']; ?>" alt="Foto Profil">
                    <?php else: ?>
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($profil['nama_lengkap'] ?? $_SESSION['username']); ?></h2>
                <div class="profile-role"><?php echo htmlspecialchars($profil['jabatan'] ?? ($is_admin ? 'Administrator' : 'Staff Inventory')); ?></div>
                <div class="profile-info">
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($profil['email'] ?? '-'); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($profil['no_telepon'] ?? '-'); ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($profil['alamat'] ?? '-'); ?></p>
                    <p><i class="fas fa-user-tag"></i> Username: <?php echo htmlspecialchars($profil['username']); ?></p>
                    <p><i class="fas fa-shield-alt"></i> Role: <?php echo ($is_admin ? 'Administrator' : 'Staff Inventory'); ?></p>
                </div>
            </div>

            <div class="form-card">
                <div class="tabs">
                    <button class="tab-btn active" onclick="openTab('edit')">Edit Profil</button>
                    <button class="tab-btn" onclick="openTab('password')">Ganti Password</button>
                </div>

                <div id="editTab" class="tab-content active">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($profil['nama_lengkap'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($profil['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>No Telepon</label>
                            <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($profil['no_telepon'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" value="<?php echo htmlspecialchars($profil['jabatan'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" rows="3"><?php echo htmlspecialchars($profil['alamat'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Foto Profil</label>
                            <input type="file" name="foto" accept="image/*">
                            <small style="color:#888;">Format: JPG, PNG. Max 2MB</small>
                        </div>
                        <button type="submit" class="btn-pink"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    </form>
                </div>

                <div id="passwordTab" class="tab-content">
                    <form method="POST">
                        <div class="form-group">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" required>
                        </div>
                        <div class="form-group">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" required>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi" required>
                        </div>
                        <button type="submit" name="ganti_password" class="btn-pink"><i class="fas fa-key"></i> Ganti Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            if(tabName === 'edit') {
                document.getElementById('editTab').classList.add('active');
            } else {
                document.getElementById('passwordTab').classList.add('active');
            }
            event.target.classList.add('active');
        }
        
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
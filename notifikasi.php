<?php
// components/notifikasi.php
// Komponen notifikasi untuk semua halaman - SETIAP NOTIFIKASI BISA DIKLIK
?>

<style>
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
    width: 380px;
    max-height: 450px;
    background: #0D0D0D;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.5);
    border: 1px solid #FF2A6D;
    z-index: 1000;
    display: none;
    overflow-y: auto;
}
.notification-dropdown.show {
    display: block;
}
.notification-header {
    padding: 15px;
    border-bottom: 1px solid #333;
    color: #FFF;
    font-weight: bold;
    position: sticky;
    top: 0;
    background: #0D0D0D;
    z-index: 1;
}
.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #222;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
    cursor: pointer;
}
.notification-item:hover {
    background: #1A1A1A;
    transform: translateX(3px);
}
.notification-item .notif-title {
    color: #FF2A6D;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 5px;
}
.notification-item .notif-message {
    color: #CCC;
    font-size: 12px;
    margin-bottom: 5px;
    line-height: 1.4;
}
.notification-item .notif-time {
    color: #888;
    font-size: 10px;
}
.notification-item .notif-badge {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}
.notification-footer {
    padding: 10px 15px;
    text-align: center;
    border-top: 1px solid #333;
}
.notification-footer a {
    color: #FF2A6D;
    text-decoration: none;
    font-size: 12px;
}
.notification-footer a:hover {
    text-decoration: underline;
}
</style>

<div class="notification-bell" id="notificationBell">
    <i class="fas fa-bell"></i>
    <span class="badge" id="notifBadge" style="display: none;">0</span>
</div>
<div class="notification-dropdown" id="notificationDropdown">
    <div class="notification-header">
        <i class="fas fa-bell"></i> Notifikasi
    </div>
    <div id="notificationList">
        <div class="notification-item">Memuat...</div>
    </div>
    <div class="notification-footer">
        <a href="laporan.php?tab=stock">📊 Lihat semua laporan →</a>
    </div>
</div>

<script>
function loadNotifications() {
    console.log('Loading notifications...');
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            console.log('Notifikasi diterima:', data);
            const notifList = document.getElementById('notificationList');
            const notifBadge = document.getElementById('notifBadge');
            
            if(!notifList) return;
            
            notifList.innerHTML = '';
            let notifCount = data.filter(n => n.type !== 'success').length;
            
            if(notifCount > 0) {
                notifBadge.style.display = 'inline-block';
                notifBadge.textContent = notifCount;
            } else {
                notifBadge.style.display = 'none';
            }
            
            data.forEach((notif, index) => {
                const notifItem = document.createElement('div');
                notifItem.className = 'notification-item';
                
                // Warna badge berdasarkan type
                let badgeColor = '#FFA500';
                if(notif.type === 'danger') badgeColor = '#FF2A6D';
                if(notif.type === 'success') badgeColor = '#00FF88';
                if(notif.type === 'warning') badgeColor = '#FFA500';
                
                notifItem.innerHTML = `
                    <div class="notif-title">
                        <span class="notif-badge" style="background: ${badgeColor};"></span>
                        <i class="fas ${notif.icon}" style="color: ${notif.color}; margin-right: 8px;"></i>
                        ${notif.title}
                    </div>
                    <div class="notif-message">${notif.message}</div>
                    <div class="notif-time"><i class="fas fa-clock"></i> ${notif.time}</div>
                `;
                
                // SEMUA notifikasi bisa diklik
                notifItem.style.cursor = 'pointer';
                
                // Tambahkan event click berdasarkan jenis notifikasi
                if(notif.title === 'Stok Menipis') {
                    notifItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Klik Stok Menipis');
                        document.getElementById('notificationDropdown').classList.remove('show');
                        window.location.href = 'notifikasi_detail.php?type=low_stock';
                    });
                }
                else if(notif.title === 'Barang Expired') {
                    notifItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Klik Barang Expired');
                        document.getElementById('notificationDropdown').classList.remove('show');
                        window.location.href = 'notifikasi_detail.php?type=expired';
                    });
                }
                else if(notif.title === 'Barang Rusak') {
                    notifItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Klik Barang Rusak');
                        document.getElementById('notificationDropdown').classList.remove('show');
                        window.location.href = 'notifikasi_detail.php?type=broken';
                    });
                }
                else if(notif.title === 'Semua Baik') {
                    notifItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        console.log('Klik Semua Baik');
                        document.getElementById('notificationDropdown').classList.remove('show');
                        alert('✅ Semua inventaris dalam kondisi baik! Tidak ada masalah.');
                    });
                }
                else {
                    notifItem.addEventListener('click', function(e) {
                        e.stopPropagation();
                        document.getElementById('notificationDropdown').classList.remove('show');
                        window.location.href = 'laporan.php?tab=stock';
                    });
                }
                
                notifList.appendChild(notifItem);
            });
            
            // Pesan jika tidak ada notifikasi
            if(data.length === 0 || (data.length === 1 && data[0].title === 'Semua Baik' && data[0].type === 'success')) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'notification-item';
                emptyItem.style.textAlign = 'center';
                emptyItem.style.cursor = 'default';
                emptyItem.innerHTML = '✨ Tidak ada notifikasi baru<br><small style="color:#888;">Semua inventaris aman</small>';
                notifList.appendChild(emptyItem);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const notifList = document.getElementById('notificationList');
            if(notifList) {
                notifList.innerHTML = '<div class="notification-item" style="text-align: center; color: #FF2A6D;">❌ Gagal memuat notifikasi</div>';
            }
        });
}

// Toggle dropdown
const bell = document.getElementById('notificationBell');
if(bell) {
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('notificationDropdown');
        if(dropdown) dropdown.classList.toggle('show');
    });
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function() {
    const dropdown = document.getElementById('notificationDropdown');
    if(dropdown) dropdown.classList.remove('show');
});

const dropdown = document.getElementById('notificationDropdown');
if(dropdown) {
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}

// Load notifikasi
loadNotifications();
setInterval(loadNotifications, 30000);
</script>
<?php
require_once 'koneksi.php';

echo "<h2>Membuat Tabel Profil Admin</h2>";

$sql = "CREATE TABLE IF NOT EXISTS admin_profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    no_telepon VARCHAR(20),
    alamat TEXT,
    foto VARCHAR(255),
    jabatan VARCHAR(100),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if(mysqli_query($conn, $sql)) {
    echo "✅ Tabel 'admin_profile' berhasil dibuat<br>";
    
    // Cek apakah sudah ada data untuk user admin
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin'");
    $user = mysqli_fetch_assoc($cek);
    
    if($user) {
        $cek_profil = mysqli_query($conn, "SELECT id FROM admin_profile WHERE user_id = '{$user['id']}'");
        if(mysqli_num_rows($cek_profil) == 0) {
            mysqli_query($conn, "INSERT INTO admin_profile (user_id, nama_lengkap, email, jabatan) 
                                VALUES ('{$user['id']}', 'Administrator', 'admin@yayasan.com', 'Super Admin')");
            echo "✅ Data profil default berhasil ditambahkan<br>";
        }
    }
} else {
    echo "❌ Gagal: " . mysqli_error($conn) . "<br>";
}

echo "<hr>";
echo "<a href='dashboard.php' style='background:#FF2A6D; color:white; padding:10px 20px; text-decoration:none; border-radius:8px;'>Kembali ke Dashboard</a>";
?>
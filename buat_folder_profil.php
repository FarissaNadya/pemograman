<?php
$folder = 'uploads/profil/';
if(!is_dir($folder)) {
    mkdir($folder, 0777, true);
    echo "Folder 'uploads/profil/' berhasil dibuat!";
} else {
    echo "Folder sudah ada!";
}
echo "<br><a href='dashboard.php'>Kembali ke Dashboard</a>";
?>
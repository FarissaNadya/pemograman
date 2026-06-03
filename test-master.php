<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Master Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: Arial; background: #1A1A1A; color: white; padding: 20px; }
        button { background: #FF2A6D; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        table { width: 100%; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #333; }
        .edit-btn, .delete-btn { cursor: pointer; margin: 0 5px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); justify-content: center; align-items: center; }
        .modal-content { background: #1A1A1A; padding: 20px; border-radius: 10px; width: 400px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; background: #333; border: 1px solid #FF2A6D; color: white; }
    </style>
</head>
<body>
    <h1>Test Master Data - Kategori</h1>
    <button onclick="openAddModal()">+ Tambah Kategori</button>
    
    <table>
        <thead><tr><th>No</th><th>Nama Kategori</th><th>Aksi</th></tr></thead>
        <tbody id="tableBody">
            <?php
            $no=1;
            $res = mysqli_query($conn, "SELECT * FROM categories");
            while($row = mysqli_fetch_assoc($res)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['name']}</td>
                        <td>
                            <i class='fas fa-edit edit-btn' onclick='editData({$row['id']}, \"{$row['name']}\")'></i>
                            <i class='fas fa-trash delete-btn' onclick='deleteData({$row['id']})'></i>
                        </td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Tambah Kategori</h3>
            <input type="hidden" id="editId" value="0">
            <input type="text" id="namaKategori" placeholder="Nama Kategori">
            <button onclick="saveData()">Simpan</button>
            <button onclick="closeModal()">Batal</button>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('editId').value = '0';
            document.getElementById('modalTitle').innerHTML = 'Tambah Kategori';
            document.getElementById('namaKategori').value = '';
            document.getElementById('modal').style.display = 'flex';
        }

        function editData(id, name) {
            document.getElementById('editId').value = id;
            document.getElementById('modalTitle').innerHTML = 'Edit Kategori';
            document.getElementById('namaKategori').value = name;
            document.getElementById('modal').style.display = 'flex';
        }

        function saveData() {
            let id = document.getElementById('editId').value;
            let nama = document.getElementById('namaKategori').value;
            
            if(!nama) {
                alert('Nama harus diisi!');
                return;
            }
            
            let formData = new FormData();
            formData.append('nama', nama);
            
            let url = (id == 0) ? 'save_master.php' : 'update_master.php';
            formData.append('type', 'categories');
            if(id != 0) formData.append('id', id);
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Data berhasil disimpan!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function deleteData(id) {
            if(confirm('Yakin ingin menghapus?')) {
                let formData = new FormData();
                formData.append('table', 'categories');
                formData.append('id', id);
                
                fetch('delete_master.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Data berhasil dihapus!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                });
            }
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('modal');
            if(event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
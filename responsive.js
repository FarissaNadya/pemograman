// responsive.js - Simpan di folder yang sama
(function() {
    // Fungsi untuk mengecek dan menambahkan wrapper pada tabel
    function addTableWrapper() {
        document.querySelectorAll('table:not(.no-wrap)').forEach(table => {
            if(!table.closest('.table-wrapper') && table.parentElement && table.parentElement.tagName !== 'DIV') {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-wrapper';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    }
    
    // Fungsi untuk menyesuaikan form row
    function adjustFormRows() {
        if(window.innerWidth <= 768) {
            document.querySelectorAll('.form-row').forEach(row => {
                row.style.flexDirection = 'column';
                row.style.gap = '10px';
            });
        } else {
            document.querySelectorAll('.form-row').forEach(row => {
                row.style.flexDirection = '';
                row.style.gap = '';
            });
        }
    }
    
    // Inisialisasi
    addTableWrapper();
    adjustFormRows();
    
    // Event listener untuk resize
    window.addEventListener('resize', function() {
        adjustFormRows();
    });
    
    // Touch feedback untuk mobile
    if('ontouchstart' in window) {
        document.querySelectorAll('.nav-link, .stat-card, .location-card, .btn-pink, .btn-outline, .btn-add')
            .forEach(el => {
                el.addEventListener('touchstart', function() {
                    this.style.opacity = '0.7';
                    setTimeout(() => {
                        this.style.opacity = '';
                    }, 150);
                });
            });
    }
})();
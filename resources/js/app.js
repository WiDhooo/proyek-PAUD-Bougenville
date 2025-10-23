import './bootstrap';

import Alpine from 'alpinejs'; // <-- TAMBAHKAN INI
window.Alpine = Alpine;       // <-- TAMBAHKAN INI
Alpine.start();               // <-- TAMBAHKAN INI

// TAMBAHKAN BLOK KODE INI
import toastr from 'toastr';
window.toastr = toastr;

// Opsi global untuk notifikasi (opsional, tapi disarankan)
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
}
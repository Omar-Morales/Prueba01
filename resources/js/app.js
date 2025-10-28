import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// DataTables + botones + estilos
import dt from 'datatables.net-bs5';
dt(window, $);

import buttons from 'datatables.net-buttons';
buttons(window, $);

import buttonsBS5 from 'datatables.net-buttons-bs5';
buttonsBS5(window, $);

import colVis from 'datatables.net-buttons/js/buttons.colVis.js';
colVis(window, $);

// CSS DataTables (si usas Vite/Laravel Mix para CSS)
import '../css/datatables/dataTables.bootstrap5.min.css';
import '../css/datatables/buttons.bootstrap5.min.css';

// Select2
import select2 from 'select2';
select2(window, $);
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

// Toastify
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';
window.Toastify = Toastify;

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal; // para usarlo globalmente como antes

//Dropzone
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';
window.Dropzone = Dropzone;


// Alpine
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

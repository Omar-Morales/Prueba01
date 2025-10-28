import axios from 'axios';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
const modal = new bootstrap.Modal(document.getElementById('modalCliente'));

// Inicializar select2 al cargar la página
/*const $status = $('#status');
if (!$status.hasClass('select2-hidden-accessible')) {
  $status.select2({
    dropdownParent: $('#modalCliente'),
    width: '100%',
    placeholder: 'Seleccione una opción',
    allowClear: true,
    theme: 'bootstrap-5'
  });
}*/

// Abrir modal para crear
$('#btnCrearCliente').on('click', function () {
    $('#modalClienteLabel').text('Nuevo Cliente');
    $('#formCliente').trigger('reset');
    $('#cliente_id').val('');
    $('#btnVerLogo').hide(); // 👈 Esta línea es la clave
    $('#modalCliente').modal('show'); // o modal.show()
});


const table = $('#customersTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/customers/data',  // cambia la ruta a tu endpoint de clientes
        type: 'GET',
        xhrFields: {
            withCredentials: true
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        {
          data: 'photo',
          name: 'photo',
          orderable: false,
          searchable: false
        },
        { data: 'ruc', name: 'ruc' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'phone', name: 'phone' },
        /* data: 'status', name: 'status' },*/
        { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
    ],
    language: {
        url: '/assets/js/es-ES.json'
    },
    responsive: true,
    autoWidth: false,
    pageLength: 10,
    order: [[0, 'asc']],
    dom: 'Bfrtip',
buttons: [
  {
    extend: 'colvis',
    text: 'Seleccionar Columnas',
    className: 'btn btn-info',
    postfixButtons: ['colvisRestore']
  }
]
});

// Función para actualizar estilos de los botones colVis
function updateColvisStyles() {
  $('.dt-button-collection .dt-button').each(function () {
    const isActive = $(this).hasClass('active') || $(this).hasClass('dt-button-active');

    if (isActive) {
      // Agregar check si no existe
      if ($(this).find('.checkmark').length === 0) {
        $(this).prepend('<span class="checkmark">✔</span>');
      }
    } else {
      // Remover check si existe
      $(this).find('.checkmark').remove();
    }
  });
}

// Evento cuando se hace alguna acción con los botones (activar/desactivar columna)
table.on('buttons-action', function () {
  setTimeout(updateColvisStyles, 10);
});

// Evento para cuando abren el menú de columnas visibles
$(document).on('click', '.buttons-colvis', function () {
  setTimeout(updateColvisStyles, 50);
});

// Opcional: cuando se carga la página
$(document).ready(function () {
  setTimeout(updateColvisStyles, 100);

$(window).on('scroll', function () {
    const $menu = $('.dt-button-collection:visible');
    if (!$menu.length) return;

    const windowWidth = document.documentElement.clientWidth;
    console.log('window.innerWidth:', window.innerWidth, 'clientWidth:', windowWidth);

    let $nav;
    if (windowWidth >= 1024 && $('.app-menu').is(':visible')) {
        $nav = $('.app-menu');
        console.log('Usando menú lateral (.app-menu)');
    } else {
        $nav = $('#page-topbar');
        console.log('Usando header (#page-topbar)');
    }

    if (!$nav.length) return;

    const menuTop = $menu.offset().top;
    const navBottom = $nav.offset().top + $nav.outerHeight();
    const tolerance = 2;

    console.log('menuTop:', menuTop, 'navBottom + tolerance:', navBottom + tolerance);

    if (menuTop < navBottom + tolerance) {
        const $toggleBtn = $('.buttons-colvis');

        $menu.css('z-index', 50);

        $menu.fadeOut(200, function () {
            $(this).css('z-index', 1050);
        });

        $('body').trigger('click');

        $toggleBtn.removeClass('active dt-btn-split-drop-active');
        $toggleBtn.attr('aria-expanded', 'false');
        $toggleBtn.blur();

        console.log('Menú ocultado');
    }
});
});



// Abrir modal para editar
$(document).on('click', '.edit-btn', function () {
    const id = $(this).data('id');
    axios.get(`/customers/${id}`)
        .then(response => {
            const data = response.data;
            $('#modalClienteLabel').text('Editar Cliente');
            $('#cliente_id').val(data.id);
            $('#ruc').val(data.ruc);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#address').val(data.address);
            /*$('#status').val(data.status).trigger('change');*/
            // Nota: para foto, podrías mostrar preview o dejar vacío
            if (data.photo_url) {
                $('#btnVerLogo').data('photo-url', data.photo_url).show();
            } else {
                $('#btnVerLogo').hide(); // opcional, pero ya no debería suceder
            }

            modal.show();
        })
        .catch(error => {
            console.error('Error al obtener el cliente:', error);
            Toastify({
                text: "No se pudo cargar el cliente",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545"
            }).showToast();
        });
});

const modalVerLogoEl = document.getElementById('modalVerLogo');
const modalVerLogo = new bootstrap.Modal(modalVerLogoEl, {
    keyboard: true
});

$('#btnVerLogo').on('click', function () {
    const url = $(this).data('photo-url');
    $('#imgLogoModal').attr('src', url);
    modalVerLogo.show();
});


// Guardar cliente
document.getElementById('formCliente').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('cliente_id').value;
    const url = id ? `/customers/${id}` : '/customers';

    let formData = new FormData();
    if(id) formData.append('_method', 'PUT');
    formData.append('ruc', document.getElementById('ruc').value);
    formData.append('name', document.getElementById('name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('phone', document.getElementById('phone').value);
    /*formData.append('status', document.getElementById('status').value);*/
    formData.append('address', document.getElementById('address').value);
    // Si se subió una foto
    const photoInput = document.getElementById('photo');
    if(photoInput.files.length > 0) {
        formData.append('photo', photoInput.files[0]);
    }

    axios.post(url, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        modal.hide();
        this.reset();
        $('#customersTable').DataTable().ajax.reload();

        Toastify({
            text: response.data.message || (id ? "Cliente actualizado" : "Cliente creado"),
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745"
        }).showToast();
    })
    .catch(error => {
        console.error(error);
        Toastify({
            text: "Error al guardar el cliente",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545"
        }).showToast();
    });
});

$(document).on('click', '.delete-btn', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'No podrás revertir esta acción.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/customers/${id}`)
            .then(response => {
                $('#customersTable').DataTable().ajax.reload();

                Toastify({
                    text: response.data.message || "Cliente eliminado",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#28a745"
                }).showToast();
            })
            .catch(error => {
                console.error(error);
                Toastify({
                    text: "Error al eliminar el cliente",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            });
        }
    });
});

document.getElementById('ruc').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

document.getElementById('phone').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

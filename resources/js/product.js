import axios from 'axios';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

const modal = new bootstrap.Modal(document.getElementById('modalProducto'));

let table = $('#productsTable').DataTable({
  processing: true,
  serverSide: true,
  ajax: { url: '/products/data', type: 'GET' },
  columns: [
    { data:'id' },
    { data:'image', orderable:false, searchable:false },
    { data:'name' },
    { data:'category_name', name:'category.name' },
    { data:'price' },
    { data:'quantity' },
    { data:'estado', name:'estado' },
    { data:'acciones', orderable:false, searchable:false }
  ],
  language: { url: '/assets/js/es-ES.json' },
  responsive: true,
  dom: 'Bfrtip',
  buttons: [
    { extend:'colvis', text:'Seleccionar Columnas', className:'btn btn-info', postfixButtons:['colvisRestore'] }
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

$(document).ready(() => {
  $('#category_id').select2({
    dropdownParent: $('#modalProducto'),
    width: '100%',
    theme: 'bootstrap-5',
    placeholder: '',
    allowClear: true
  });
});

/*
$(document).ready(() => {
    $('#category_id, #status').select2({
    dropdownParent: $('#modalProducto'),
    width: '100%',
    placeholder: 'Seleccione una opción',
    allowClear: true,
    theme: 'bootstrap-5'
  });
});*/

// Dropzone Global
let myDropzone = null;
let suppressRemoveEvent = false;
function initDropzone(productId = null, images = []) {
  if (myDropzone) {
    suppressRemoveEvent = true;
    myDropzone.removeAllFiles(true);  // elimina archivos sin disparar evento removedfile
    myDropzone.destroy();
    myDropzone = null;
    suppressRemoveEvent = false;
  }

  myDropzone = new Dropzone("#dropzoneImages", {
    url: productId ? `/products/${productId}/images/upload` : '/products/images/temp-upload',
    paramName: "file",
    maxFilesize: 2,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    dictRemoveFile: "Eliminar",
    dictDefaultMessage: "Sube tus imágenes aquí.",
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    init: function() {
      const dz = this;

      suppressRemoveEvent = true;

      images.forEach(img => {
        const mockFile = {
          name: img.image_path.split('/').pop(),
          size: 12345,
          serverId: img.id,
          accepted: true
        };
        dz.emit("addedfile", mockFile);
        dz.emit("thumbnail", mockFile, `/storage/${img.image_path}`);
        dz.emit("complete", mockFile);
        dz.files.push(mockFile);
      });

      suppressRemoveEvent = false;

      dz.on("success", function(file, response) {
        file.serverId = response.id;
      });

      dz.on("removedfile", function(file) {
        if (suppressRemoveEvent) return;

        if (file.serverId && productId) {
          axios.post(`/products/${productId}/images/delete`, { id: file.serverId })
            .then(() => {
                table.ajax.reload(null, false); // ✅ recargar tabla después de borrar imagen
            })
            .catch(() => alert("Error al eliminar imagen"));
        }
      });

      dz.on("processing", function(file) {
        $('#btnGuardarProducto').prop('disabled', true);
      });

      dz.on("queuecomplete", function() {
        $('#btnGuardarProducto').prop('disabled', false);
      });
    }
  });
}
//Select de categoria
let categoriasCache = null;

function cargarCategoriasEnSelect(idSeleccionado = null, callback = null) {
  const $select = $('#category_id');

  // Coloca un estado temporal claro para evitar datos fantasma
  $select.empty().append(new Option('Cargando categorías...', '', true, true)).trigger('change');

  // Armar URL con include_id si aplica
  let url = '/categorias/select';
  if (idSeleccionado) {
    url += '?include_id=' + encodeURIComponent(idSeleccionado);
  }

  axios.get(url)
    .then(response => {
      const categorias = response.data;

      // Guardamos caché si quieres evitar futuras peticiones
      categoriasCache = categorias;

      // Limpiar y poblar correctamente
      $select.empty().append(new Option('-- Seleccione --', '', true, false));

      categorias.forEach(c => {
        const isSelected = idSeleccionado == c.id;
        $select.append(new Option(c.text, c.id, false, isSelected));
      });

      $select.trigger('change');

       //Esta línea es la clave para que se ejecute el callback y se muestre el modal
      if (typeof callback === 'function') {
        callback();
      }
    })
    .catch(error => {
      console.error('Error al cargar categorías:', error);

      $select.empty().append(new Option('-- Error al cargar --', '', true, true)).trigger('change');

      Toastify({
        text: "Error al cargar categorías",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "#dc3545"
      }).showToast();
    });
}

$('#modalProducto').on('hidden.bs.modal', function () {
    if (myDropzone) {
        suppressRemoveEvent = true;
        myDropzone.removeAllFiles(true); // borra sin llamar a backend
        myDropzone.destroy();
        myDropzone = null;
        suppressRemoveEvent = false;
    }
});


// Crear producto
$('#btnCrearProducto').on('click', async () => {
  $('#formProducto')[0].reset();
  $('#producto_id').val('');
  $('#modalProductoLabel').text('Nuevo Producto');
  $('#previewImages').empty();
  //$('#category_id').val(null).trigger('change');
  //$('#category_id, #status').val(null).trigger('change');

  modal.show();
  $('#modalProducto .modal-content').append('<div id="cargandoOverlay" class="modal-loading-overlay"></div>');

    try {
        await new Promise(resolve => cargarCategoriasEnSelect(null, resolve));
        initDropzone();
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        Toastify({
            text: 'No se pudo cargar las categorías',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: { background: '#dc3545' }
        }).showToast();
    }finally {
        $('#cargandoOverlay').remove(); // Quitar overlay de bloqueo
    }
});

// Editar producto
// Editar producto - Abrir modal
$(document).on('click', '.edit-btn',async function() {
  const id = $(this).data('id');

$('#modalProducto .modal-content').append('<div id="cargandoOverlay" class="modal-loading-overlay"></div>');

try {
        const { data } = await axios.get(`/products/${id}`);
        const product = data.product;
        const categories = data.categories;

    $('#producto_id').val(product.id);
    $('#name').val(product.name);
    //$('#category_id').val(product.category_id).trigger('change');
    $('#price').val(product.price);
    $('#quantity').val(product.quantity);
    //$('#status').val(product.status).trigger('change');
    const $select = $('#category_id');
    $select.empty(); // Vaciar opciones actuales
    categories.forEach(cat => {
        const selected = cat.id === product.category_id ? 'selected' : '';
        $select.append(`<option value="${cat.id}" ${selected}>${cat.text}</option>`);
    });
    $select.trigger('change');

    $('#images').val(null);
    $('#modalProductoLabel').text('Editar Producto');
    initDropzone(product.id, product.images || []);
    modal.show();
  }
  catch(error) {
    Toastify({
      text: "Error al cargar producto",
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "#dc3545"
    }).showToast();
  }finally {
        $('#cargandoOverlay').remove();
    }
});

// Guardar producto
$('#formProducto').on('submit', function(e){
  e.preventDefault();

  const id = $('#producto_id').val();
  const url = id ? `/products/${id}` : '/products';
  const method = 'post';
  const fd = new FormData(this);

  if (id) fd.append('_method', 'PUT');

  // Agregar los paths temporales de Dropzone al FormData
  if (myDropzone && myDropzone.files.length) {
    myDropzone.files.forEach(file => {
      if (file.xhr) {
        const response = JSON.parse(file.xhr.response);
        if (response.path) {
          fd.append('temp_images[]', response.path);
        }
      }
    });
  }

  axios({
    method,
    url,
    data: fd,
    headers: { 'Content-Type': 'multipart/form-data' }
  }).then(({data}) => {
    Toastify({
      text: data.message,
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "#28a745"
    }).showToast();

    if (!id) {
      $('#producto_id').val(data.id);
      initDropzone(data.id); // reinicializar con nuevo ID
    }

    modal.hide();
    table.ajax.reload(null, false);
  }).catch(err => {

    Toastify({
      text: err.response?.data?.message || 'Error al guardar producto.',
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "#dc3545"
    }).showToast();
  });
});



// Eliminar producto con confirmación SweetAlert2
$(document).on('click', '.delete-btn', function(e){
  e.preventDefault();
  let id = $(this).data('id');

  Swal.fire({
    title: '\u00BFEst\u00e1s seguro?',
    text: '\u00A1No podr\u00e1s revertir esto!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'S\u00ed, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(res => {
    if(res.isConfirmed){
      axios.delete(`/products/${id}`)
        .then(() => {
          table.ajax.reload(null, false);
          Toastify({
            text: "Producto eliminado",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#28a745"
          }).showToast();
        }).catch(() => {
          Toastify({
            text: "Error al eliminar producto",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545"
          }).showToast();
        });
    }
  });
});


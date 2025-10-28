import axios from 'axios';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

const table = $('#backupTable').DataTable({
    processing: true,
    serverSide: false, // si quieres usar paginación en Laravel, cambia a true y haz `paginate` en el controlador
    ajax: {
        url: '/admin/backups/list',
        type: 'GET',
        xhrFields: {
            withCredentials: true
        }
    },
    columns: [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'size', name: 'size' },
        { data: 'date', name: 'date' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
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

// 🔁 Estilo de botones de columna (como ya usaste en categorias)
function updateColvisStyles() {
    $('.dt-button-collection .dt-button').each(function () {
        const isActive = $(this).hasClass('active') || $(this).hasClass('dt-button-active');
        if (isActive) {
            if ($(this).find('.checkmark').length === 0) {
                $(this).prepend('<span class="checkmark">✔</span>');
            }
        } else {
            $(this).find('.checkmark').remove();
        }
    });
}

table.on('buttons-action', function () {
    setTimeout(updateColvisStyles, 10);
});

$(document).on('click', '.buttons-colvis', function () {
    setTimeout(updateColvisStyles, 50);
});

$(document).ready(function () {
    setTimeout(updateColvisStyles, 100);
});

// ✅ Crear Backup
$('#btnCrearBackup').on('click', function () {
    Swal.fire({
        title: '¿Crear nuevo backup?',
        text: 'Se generará una copia de seguridad de tu sistema.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Crear',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
        showSpinner();
        axios.post('/admin/backups/create')
            .then(response => {
                const timestamp = response.data.timestamp;
                Toastify({
                    text: response.data.message || "Backup en proceso...",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#007bff"
                }).showToast();

                // 🔁 Verificar estado del backup
                const intervalId = setInterval(() => {
                    axios.get('/admin/backups/status', {
                        params: { timestamp }
                    })
                    .then(statusResp => {
                        if (statusResp.data.status === 'ok') {
                            clearInterval(intervalId);
                            hideSpinner();
                            Toastify({
                                text: "Backup creado correctamente.",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#28a745"
                            }).showToast();
                            table.ajax.reload();
                        }
                    })
                    .catch(err => {
                        clearInterval(intervalId);
                        hideSpinner();
                        const errorMsg = err.response?.data?.error || "Ocurrió un error inesperado.";
                        Toastify({
                            text: "❌ Error en el backup: " + errorMsg,
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545"
                        }).showToast();
                    });
                }, 3000); // cada 3 segundos
            })
            .catch(error => {
                hideSpinner();
                Toastify({
                    text: "Error al crear backup.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            });

        }
    });
});

// ✅ Eliminar Backup
$(document).on('submit', '.formEliminarBackup', function (e) {
    e.preventDefault();

    const form = $(this);
    const url = form.attr('action');

    Swal.fire({
        title: '¿Eliminar este backup?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            showSpinner();
            axios.delete(url)
                .then(response => {
                    hideSpinner();
                    Toastify({
                        text: response.data.message || "Backup eliminado correctamente.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745"
                    }).showToast();
                    table.ajax.reload();
                })
                .catch(error => {
                    hideSpinner();
                    Toastify({
                        text: "Error al eliminar backup.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545"
                    }).showToast();
                });
        }
    });
});

$(document).on('submit', '.formRestaurarBackup', function (e) {
    e.preventDefault();
    const form = $(this);
    const url = form.attr('action');

    Swal.fire({
        title: '¿Restaurar este backup?',
        text: 'Se sobrescribirá la base de datos actual. ¡Ten cuidado!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showSpinner();
            axios.post(url)
                .then(response => {
                    Toastify({
                        text: response.data.message || "Backup restaurado.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ffc107"
                    }).showToast();

                    // 🔁 Redirigir al login tras 3 segundos
                    setTimeout(() => {
                        hideSpinner();
                        window.location.replace('/login');
                    }, 3000);
                })
                .catch(error => {
                    hideSpinner();

                    // ✅ Si el servidor ya cayó tras restaurar, redirigir igual al login
                    if (error.response?.status === 503) {
                        Swal.fire({
                            title: 'Restaurando...',
                            text: 'El sistema está en mantenimiento. Serás redirigido al login.',
                            icon: 'info',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            window.location.replace('/login');
                        });
                    } else {
                        Toastify({
                            text: "Error al restaurar el backup.",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545"
                        }).showToast();
                    }
                });
        }
    });
});


// Cargar configuración inicial
/*axios.get('/admin/backups/settings')
  .then(({ data }) => {
    document.getElementById('frequency').value = data.frequency || 'daily';
    document.getElementById('time').value = data.time || '02:00';
  })
  .catch(() => {
    Toastify({
      text: "Error cargando configuración.",
      duration: 3000,
      gravity: "top",
      position: "right",
      backgroundColor: "#dc3545",
    }).showToast();
  });*/

// Guardar configuración
/*
document.getElementById('formBackupSettings').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  axios.post('/admin/backups/settings', formData)
    .then(({ data }) => {
      Toastify({
        text: 'Configuración guardada correctamente.',
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "#28a745",
      }).showToast();
    })
    .catch(error => {
      let message = "Error al guardar configuración.";
      if (error.response?.data?.message) {
        message = error.response.data.message;
      }
      Toastify({
        text: message,
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "#dc3545",
      }).showToast();
    });
});
*/

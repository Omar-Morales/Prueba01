import axios from 'axios';

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

$(document).ready(function () {
    const table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/inventories',
            type: 'GET',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            xhrFields: {
                withCredentials: true
            }
        },
        columns: [
            { data: 'reference_id', name: 'reference_id', orderable: false, searchable: false },
            { data: 'total_quantity', name: 'total_quantity' },
            { data: 'type', name: 'type' },
            { data: 'reason', name: 'reason' },
            { data: 'user', name: 'user' },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        language: {
            url: '/assets/js/es-ES.json'
        },
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        order: [[0, 'desc']], // Orden por fecha
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

    function updateColvisStyles() {
        $('.dt-button-collection .dt-button').each(function () {
            const isActive = $(this).hasClass('active') || $(this).hasClass('dt-button-active');
            if (isActive && $(this).find('.checkmark').length === 0) {
                $(this).prepend('<span class="checkmark">✔</span>');
            } else if (!isActive) {
                $(this).find('.checkmark').remove();
            }
        });
    }

    table.on('buttons-action', () => setTimeout(updateColvisStyles, 10));
    $(document).on('click', '.buttons-colvis', () => setTimeout(updateColvisStyles, 50));
    setTimeout(updateColvisStyles, 100);

    $('#filterBtn').on('click', function () {
        table.ajax.reload();
    });

        $('#exportExcel').on('click', function (e) {
        e.preventDefault();

        const start = $('#start_date').val();
        const end = $('#end_date').val();

        if (!start || !end) {
            Toastify({
                text: "Coloca un intervalo de fecha.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#dc3545"
            }).showToast();
            return;
        }

        const url = `/inventories/export?start_date=${start}&end_date=${end}`;
        const filename = `reporte_inventarios_${start}_al_${end}.xlsx`;
        showSpinner();
        axios({
            url: url,
            method: 'GET',
            responseType: 'blob' // 👈 necesario para archivos
        })
        .then(response => {
            const blob = new Blob([response.data], { type: response.headers['content-type'] });
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(downloadUrl);
            hideSpinner();
        })
        .catch(error => {
            console.error("Error al exportar:", error);
            hideSpinner();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo generar el archivo. Inténtalo de nuevo.'
            });
        });
    });


    // Ver detalle (dinámicamente renderizado en 'botones.blade.php')
    $(document).on('click', '.btn-detalle', function () {
        const referenceId = $(this).data('id');
        const type = $(this).data('type');

        axios.get(`/inventories/${referenceId}?type=${type}`)
            .then(response => {
                const data = response.data;

                // Mostrar datos generales
                $('#detalleId').text(`${data.codigo ?? '—'}`);
                $('#detalleTipo').text(
                    data.tipo === 'purchase' ? 'Compra' :
                    data.tipo === 'sale' ? 'Venta' : 'Ajuste'
                );

                $('#detalleEntidad').text(
                    data.tipo === 'purchase' ? data.supplier :
                    data.tipo === 'sale' ? data.customer : '—'
                );

                $('#detalleFecha').text(
                    new Date(data.fecha).toLocaleDateString('es-ES')
                );

                $('#detalleUsuario').text(data.user ?? '—');

                // Cargar tabla de productos
                const detallesHtml = data.detalles.map(detalle => `
                    <tr>
                        <td>${detalle.producto}</td>
                        <td>${detalle.quantity}</td>
                        <td>${detalle.unit_price ?? '—'}</td>
                        <td>${detalle.subtotal ?? '—'}</td>
                    </tr>
                `).join('');

                $('#detalleProductos').html(detallesHtml);
                $('#detalleTotal').text(data.total.toFixed(2));

                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
                modal.show();
            })
            .catch(() => {
                Toastify({
                    text: "No se pudieron cargar los detalles",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545"
                }).showToast();
            });
    });



    $(document).on('click', '.btn-export-pdf', function () {
        const referenceId = $(this).data('id');
        const type = $(this).data('type');
        const url = `/inventories/${referenceId}/pdf?type=${type}`;
        const filename = `inventario_${referenceId}.pdf`;

        showSpinner();

        axios({
            url: url,
            method: 'GET',
            responseType: 'blob'
        })
        .then(response => {
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            window.URL.revokeObjectURL(link.href);
            hideSpinner();
        })
        .catch(error => {
            console.error('Error al descargar PDF:', error);
            hideSpinner();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo descargar el PDF.'
            });
        });
    });

    $(document).on('click', '.btn-export-excel', function () {
        const referenceId = $(this).data('id');
        const type = $(this).data('type');
        const url = `/inventories/export/${referenceId}?type=${type}`;
        const filename = `inventario_${referenceId}.xlsx`;

        showSpinner();

        axios({
            url: url,
            method: 'GET',
            responseType: 'blob'
        })
        .then(response => {
            const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            window.URL.revokeObjectURL(link.href);
            hideSpinner();
        })
        .catch(error => {
            console.error('Error al descargar Excel:', error);
            hideSpinner();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo descargar el archivo Excel.'
            });
        });
    });


});

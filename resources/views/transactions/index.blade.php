@extends('layouts.app')

@section('title', 'Historial de Transacciones')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Encabezado -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Historial de Transacciones</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Transacciones</a></li>
                            <li class="breadcrumb-item active">Historial</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y exportación -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="start_date">Fecha inicio:</label>
                                <input type="date" id="start_date" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label for="end_date">Fecha fin:</label>
                                <input type="date" id="end_date" class="form-control">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary w-100">Filtrar</button>
                            </div>
                            @can('administrar.transacciones.export')
                            <div class="col-md-3 d-flex align-items-end">
                                <a id="exportExcel" href="#" class="btn btn-success w-100">Exportar a Excel</a>
                            </div>
                            @endcan
                        </div>

                        <!-- Tabla -->
                        <div class="table-responsive">
                            <table id="transactionsTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <br/>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="detalleTransaccionModal" tabindex="-1" aria-labelledby="detalleTransaccionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleTransaccionModalLabel">Detalles de la Transacción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Codigo:</strong> <span id="transId"></span></p>
                <p><strong>Tipo:</strong> <span id="transTipo"></span></p>
                <p><strong>Monto:</strong> <span id="transMonto"></span></p>
                <p><strong>Usuario:</strong> <span id="transUsuario"></span></p>
                <p><strong>Fecha:</strong> <span id="transFecha"></span></p>

                <h5>Productos</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="transProductos"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td id="transTotal" class="fw-bold">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/transaction.js')
@endpush

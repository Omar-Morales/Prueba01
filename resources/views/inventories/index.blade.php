@extends('layouts.app')

@section('title', 'Historial de Inventario')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Historial de Inventario</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#">Inventario</a></li>
                            <li class="breadcrumb-item active">Historial</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Encabezado -->

        <!-- Tabla -->
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
                            @can('administrar.inventarios.export')
                            <div class="col-md-3 d-flex align-items-end">
                                <a id="exportExcel" href="#" class="btn btn-success w-100">Exportar a Excel</a>
                            </div>
                            @endcan
                        </div>

                        <div class="table-responsive">
                            <table id="inventoryTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th># Prod. Distintos</th>
                                    <th>Tipo</th>
                                    <th>Razón</th>
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
</div>
        <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">Detalles del Movimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Codigo:</strong> <span id="detalleId"></span></p>
                <p><strong>Tipo:</strong> <span id="detalleTipo"></span></p>

                <div id="campoEntidad">
                    <p><strong>Cliente/Proveedor:</strong> <span id="detalleEntidad"></span></p>
                </div>

                <div id="campoRazon" class="d-none">
                    <p><strong>Razón:</strong> <span id="detalleRazon"></span></p>
                </div>

                <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                <p><strong>Usuario:</strong> <span id="detalleUsuario"></span></p>

                <h5>Productos</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio/Coste</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="detalleProductos"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td id="detalleTotal" class="fw-bold">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite('resources/js/inventorie.js')
@endpush

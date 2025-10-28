@extends('layouts.app')

@section('title', 'Mantenimiento de Ventas')
@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Listado de Ventas</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Mantenimiento</a></li>
                            <li class="breadcrumb-item active">Ventas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body">
                        @can('administrar.ventas.create')
                        <button type="button" class="btn btn-primary mb-3" id="btnCrearVenta" data-bs-toggle="modal" data-bs-target="#modalVenta">
                            Nueva Venta
                        </button>
                        @endcan
                        <div class="table-responsive">
                            <table id="ventasTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Tipo Documento</th>
                                        <th>Usuario</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Método Pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTable llenará esto -->
                                </tbody>
                            </table>
                        </div>
                        <br/>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Crear/Editar Venta -->
<div class="modal fade" id="modalVenta" tabindex="-1" aria-labelledby="modalVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formVenta">
            @csrf
            <input type="hidden" id="venta_id" name="venta_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVentaLabel">Nueva Venta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label for="customer_id" class="form-label">Cliente</label>
                            <select class="form-select" id="customer_id" name="customer_id" required>
                                <option value="">-- Seleccione --</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="tipodocumento_id" class="form-label">Tipo Documento</label>
                            <select class="form-select" id="tipodocumento_id" name="tipodocumento_id" required>
                                <option value="">-- Seleccione --</option>
                                @foreach ($tiposDocumento as $documento)
                                <option value="{{ $documento->id }}">{{ $documento->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="payment_method" class="form-label">Método de Pago</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">-- Seleccione --</option>
                                <option value="cash">Efectivo</option>
                                <option value="card">Tarjeta</option>
                                <option value="transfer">Transferencia</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="sale_date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">-- Seleccione --</option>
                                <option value="completed">Completada</option>
                                <option value="pending">Pendiente</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="total_price" class="form-label">Total</label>
                            <input type="number" class="form-control" id="total_price" name="total_price" required readonly>
                        </div>

                    </div>

                    <div class="mb-1">
                        <h5>Productos</h5>
                        <button type="button" class="btn btn-sm btn-info mb-2" id="addProductRow">+ Agregar Producto</button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="detalleVentaTableEditable">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="detalleVentaBody">
                                    <!-- Las filas se llenarán con JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Detalle -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="modalDetalleVentaLabel">Detalle de Venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <table id="detalleVentaTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="detalleVentaBodydos">
                <!-- Se llenará dinámicamente -->
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
    <style>
        #modalVenta .select2-container {
            z-index: 9999 !important;
            position: relative !important;
        }

        #modalVenta .select2-dropdown {
            position: absolute !important;
        }

        #modalVenta .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
@endpush

@push('scripts')
    @vite('resources/js/venta.js')
@endpush

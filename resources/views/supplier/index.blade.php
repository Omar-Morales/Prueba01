@extends('layouts.app')

@section('title', 'Mantenimiento de Proveedores')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Listado de Proveedores</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Mantenimiento</a></li>
                            <li class="breadcrumb-item active">Proveedores</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body">
                        @can('administrar.proveedores.create')
                        <button type="button" class="btn btn-primary mb-3" id="btnCrearProveedor" data-bs-toggle="modal" data-bs-target="#modalProveedor">
                            Nuevo Proveedor
                        </button>
                        @endcan
                        <div class="table-responsive">
                            <table id="suppliersTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Logo</th>
                                        <th>RUC</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <!--<th>Estado</th>-->
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables llenará esta tabla -->
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

<!-- Modal Crear/Editar Proveedor -->
<div class="modal fade" id="modalProveedor" tabindex="-1" aria-labelledby="modalProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formProveedor">
            @csrf
            <input type="hidden" id="proveedor_id" name="proveedor_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProveedorLabel">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ruc" class="form-label">RUC</label>
                        <input type="text" class="form-control" id="ruc" name="ruc" maxlength="11" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="phone" name="phone" maxlength="9">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección</label>
                        <textarea name="address" class="form-control" id="address"></textarea>
                    </div>
                    <!--<div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">-- Seleccione --</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>-->
                    <div class="mb-3">
                        <label for="photo" class="form-label">Logo (foto)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif">
                    </div>

                        <!-- Botón para mostrar imagen -->
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="btnVerLogo" style="display: none;">
                        Ver logo actual
                    </button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarProveedor">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver logo -->
<style>
  #modalVerLogo .modal-dialog {
    max-width: 400px;
  }
</style>

<div class="modal fade" id="modalVerLogo" tabindex="-1" aria-labelledby="modalVerLogoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    <div class="modal-body text-center">
        <img id="imgLogoModal" src="" alt="Logo del proveedor" class="img-fluid rounded shadow" style="max-height: 300px;">
    </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/supplier.js')
@endpush

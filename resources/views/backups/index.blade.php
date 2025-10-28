@extends('layouts.app')

@section('title', 'Mantenimiento de Backups')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <!-- Título -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Listado de Backups</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Mantenimiento</a></li>
                            <li class="breadcrumb-item active">Backups</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabla de backups -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @can('administrar.backups.create')
                        <button type="button" class="btn btn-primary mb-3" id="btnCrearBackup">
                            Crear Backup Manual
                        </button>
                        @endcan
                        <div class="table-responsive">
                            <table id="backupTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Tamaño</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTable llenará automáticamente esta tabla -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/backup.js')
@endpush
